---
layout: post
title: Subversion Backup How-To
date: 2010-08-01T23:45:00.000Z
tags:
  - backup
  - gentoo
  - how-to
  - linux
  - subversion
image: '/assets/files/2010-08-01-online-backup.png'
---
I will start this post once again with the words of a wise man:

> There are two kinds of people, those who backup regularly, and those that never had a hard drive fail

So the moral of the story here is **backup often**. If something is to happen, the impact on your operations will be minimal if your backup strategy is in place and operational.

There are a lot of backup scenarios and strategies. Most of them suggest a backup once a day, usually at the early hours of the day. This however might not work very well with a fast paced environment where data changes several times per hour. This kind of environment is usually a software development one.

If you have chosen [Subversion](https://subversion.tigris.org/) to be your software version control software then you will need a backup strategy for your repositories. Since the code changes very often, this strategy cannot rely on the daily backup schedule. The reason being is that, in software, a day's worth of work usually costs a lot more than the actual daily rate of the programmers.

Below are some of the scripts I have used over the years for my incremental backups, that I hope will help you too. You are more than welcome to copy and paste the scripts and use them &nbsp;or modify them to suit your needs. Please note though that the scripts are provided as is and that you must check your backup strategy with a full backup/restore cycle. I cannot assume responsibility of something that might happen in your system.

Now that the 'legal' stuff are out of the way, here are the different strategies that you can adopt. :)

#### `svn-hot-backup`

This is a script that is provided with [Subversion](https://subversion.tigris.org/). It copies (and compresses if requested) the whole repository to a specified location. This technique allows for a full copy of the repository to be moved to a different location. The target location can be a resource on the local machine or a network resource. You can also backup on the local drive and then as a next step transfer the target files to an offsite location with [FTP](https://en.wikipedia.org/wiki/File_Transfer_Protocol), [SCP](https://en.wikipedia.org/wiki/Secure_copy), [RSync](https://www.samba.org/rsync/) or any other mechanism you prefer.

```sh
#!/bin/bash

# Grab listing of repositories and copy each
# repository accordingly

SVNFLD="/var/svn"
BACKUPFLD="/backup"

# First clean up the backup folder
rm -f $BACKUPFLD/*.*

for i in $(ls -1v $SVNFLD); do
    if [ $i != 'conf' ]; then
        /usr/bin/svn-hot-backup --archive-type=bz2 $SVNFLD/$i $BACKUPFLD
    fi
done
```

This script will create a copy of each of your repositories and compress it as a bz2 file in the target location. Note that I am filtering for 'conf'. The reason being is that I have a conf file with some configuration scripts in the same SVN folder. You can adapt the script to your needs to include/exclude repositories/folders as needed.

This technique gives the ability to immediately restore a repository (or more than one) by changing the configuration file of SVN to point to the backup location. If you run the script every hour or so then your downtime and loss will be minimal, should something happens.

There are some configuration options that you can tweak by editing the actual `svn-hot-backup` script. In Gentoo it is located under `/usr/bin/`. The default number of backups (`num_backups`) that the script will keep is 64. You can choose 0 to *keep them all* but you can adjust it according to your storage or your backup strategy.

One last thing to note is that you can change the compression mechanism by changing the parameter of the `--archive-type` option. The compression types supported are gz (.tar.gz), bz2 (.tar.bz2) and zip (.zip)

#### Full backup using dump

This method is similar to the svn-hot-backup. It works by 'dumping' the repository in a portable file format and compressing it.

```sh
#!/bin/bash

# Grab listing of folders and dump each
# repository accordingly

SVNFLD="/var/svn"
BACKUPFLD="/backup"

# First clean up the backup folder
rm -f $BACKUPFLD/svn/*.*

for i in $(ls -1v $SVNFLD); do
    if [ $i != 'conf' ]; then
        svnadmin dump $SVNFLD/$i/ &gt; $BACKUPFLD/$i.svn.dump
        tar cvfz $BACKUPFLD/svn/$i.tgz $BACKUPFLD/$i.svn.dump
        rm -f $BACKUPFLD/$i.svn.dump
    fi
done
```

As you can see, this version does the same thing as the `svn-hot-backup`. It does however give you a bit more control over the whole backup process and allows for a different compression mechanism - since the compression happens on a separate line in the script.

**NOTE:** If you use the `hotcopy` parameter in `svnadmin` (`svnadmin hotcopy ....`) you will be duplicating the behavior of `svn-hot-backup`.

#### Incremental backup using dump based on revision

This last method is what I use at work. We have our repositories backed up externally and we rely on the backup script to have everything backed up and transferred to the external location within an hour, since our backup strategy is an hourly backup. We have discovered that sometimes the size of a repository can cause problems with the transfer, since the Internet line will not be able to transfer the files across in the allocated time. This happened once in the past with a repository that ended up being 500Mb (don't ask :)).

So in order to minimize the upload time, I have altered the script to dump each repository's revision in a separate file. Here is how it works:

We backup using `rsync`. This way the 'old' files are not being transferred.

Every hour the script loops through each repository name and does the following:

- Checks if the `.latest` file exists in the `svn-latest` folder. If not, then it sets the `LASTDUMP` variable to 0.
- If the file exists, it reads it and obtains the number stored in that file. It then stores that number incremented by 1 in the `LASTDUMP` variable.
- Checks the number of the latest revision and stores it in the `LASTVERSION` variable
- It loops through the repository, dumps each revision (`LASTDUMP` to `LASTVERSION`) and compresses it

This method creates new files every hour so long as new code has been added in each repository via the `checkin` process. The `rsync` command will then pick only the new files and nothing else, therefore the data transferred is reduced to a bare minimum allowing easily for hourly external backups. With this method we can also restore a single revision in a repository if we need to.

The script that achieves that is as follows:

```sh
#!/bin/bash

# Grab listing of folders and dump each
# repository accordingly

SVNFLD="/var/svn"
BACKUPFLD="/backup"
CHECKFLD=$BACKUPFLD/svn-latest

for i in $(ls -1v $SVNFLD); do
    if [ $i != 'conf' ]; then
        # Find out what our 'start' will be
        if [ -f $CHECKFLD/$i.latest ]
        then
            LATEST=$(cat $CHECKFLD/$i.latest)
            LASTDUMP=$LATEST+1
        else
            LASTDUMP=0
        fi

        # This is the 'end' for the loop
        LASTREVISION=$(svnlook youngest $SVNFLD/$i/)

        for ((r=$LASTDUMP; r&lt; =$LASTREVISION; r++ )); do
            svnadmin dump $SVNFLD/$i/ --revision $r &gt; $BACKUPFLD/$i-$r.svn.dump
            tar cvfz $BACKUPFLD/svn/$i-$r.tgz $BACKUPFLD/$i-$r.svn.dump
            rm -f $BACKUPFLD/$i-$r.svn.dump
            echo $r &gt; $CHECKFLD/$i.latest
        done
    fi
done
```

#### Conclusion

You must **always** backup your data. The frequency is dictated by the rate that your data updates and how critical your data is. I hope that the methods presented in this blog post will complement your programming and source control should you choose to adopt them.
