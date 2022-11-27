---
layout: post
title: Create an inexpensive hourly remote backup
date: 2010-08-21T23:45:00.000Z
tags:
  - backup
  - gentoo
  - installation
  - linux
  - storage
  - how-to
image: '/assets/files/2010-08-21-remote-backup.png'
image-alt: Backup
---
> There are two kinds of people, those who backup regularly, and those that never had a hard drive fail

As you can tell [the above](/post/subversion-backup-how-to) is my favorite quote. It is so true and I believe everyone should evaluate how much their data (emails, documents, files) is worth to them and, based on that value, create a backup strategy that suits them. I know for sure that if I ever lost the pictures and videos of my family I would be devastated since those are irreplaceable.

So the question is how can I have an inexpensive backup solution? All my documents and emails are stored in Google, since my domain is on [Google Apps](https://workspace.google.com/). What happens to the live/development servers though that host all my work? I program on a daily basis and the code has to be backed up regularly so as to avoid any hard drive failures and thus result in loss of time and money.

So here is my solution. I have an old computer (IBM Thincentre) which I decided to beef up a bit. I bought 4Gb of RAM from eBay for less than $100 for it. Although this was not necessary since my solution would be based on Linux ([Gentoo](https://www.gentoo.org) in particular), I wanted to have faster compilation times for packages.

I bought two external drives (750Gb and 500Gb respectively) and one 750Gb internal drive. I already have a 120Gb hard drive in the computer. The two external ones are connected to the computer using USB while the internal ones are connected using SATA.

The external drives are formatted using NTFS while the whole computer is built using ReiserFS.

Here is the approach:

* I have installed and have a working Gentoo installation on the machine
* I have an active Internet connection
* I have installed LVM on the machine and set up the core system on the 120Mb drive while the 500Mb is on LVM
* I have 300Mb active on the LVM (from the available 500Mb)
* I have generated a public SSH key (I will need this to exchange it with the target servers)
* I have mounted the internal 500Mb drive to the `/storage` folder
* I have mounted the external USB 750Mb drive to the `/backup_hourly` folder
* I have mounted the external USB 500Mb drive to the `/backup_daily` folder

Here is how my backup works:

Every hour a script runs. The script uses rsync to syncrhonize files and folders from a remote server locally. Those files and folders are kept in relevant server named subfolders in the `/storage` folder (remember this is my LVM). So for instance my subfolders will be `/storage/beryllium.niden.net`, `/storage/nitrogen.niden.net`, `/storage/argon.niden.net` etc.

Once the rsync completes, the script continues by compressing the relevant 'server' folder and creates the compressed file with a date-time stamp on its name.

When all compressions are completed, if the time that the script has executed is midnight, the backups are moved from the `/storage` folder to the `/backup_daily` folder (which has the external USB 500Gb mounted). If it is any other time, the files are moved in the `/backup_hourly` folder (which has the external USB 750Gb mounted).

This way I ensure that I keep a lot of backups (daily and hourly ones). The backups are being recycled, so older ones get deleted. The amount of data that you need to archive as well as the storage space you have available dictate how far back you can go in your hourly and daily cycles.

So let's get down to business. The script itself:

```sh
#!/bin/bash
DATE=`date +%Y-%m-%d-%H-%M`
DATE2=`date +%Y-%m-%d`
DATEBACK_HOUR=`date --date='6 days ago' +%Y-%m-%d`
DATEBACK_DAY=`date --date='60 days ago' +%Y-%m-%d`
FLAGS="--archive --verbose --numeric-ids --delete --rsh='ssh'"
BACKUP_DRIVE="/storage"
DAY_USB_DRIVE="/backup_daily"
HOUR_USB_DRIVE="/backup_hourly"
```

These are some variables that I need for the script to work. The `DATE` and `DATE2` are used to date/time stamp the backups, while the `DATEBACK_`* are used to clear previous backups. In this case it is set to 6 days ago (for my system). It can be set to whatever you want provided that you do not run out of space.

The `FLAGS` variable keeps the rsync command options while the `BACKUP_DRIVE`, `DAY_USB_DRIVE` and `HOUR_USB_DRIVE` hold the locations of the rsync folders, daily backup and hourly backup sorage areas.

The script works with arrays. I have 4 arrays to do the work and the 3 of them must have exactly the same elements.


```sh
# RSync Information
rsync_info[1]="beryllium.niden.net html rsync"
rsync_info[2]="beryllium.niden.net db rsync"
rsync_info[3]="nitrogen.niden.net html rsync"
rsync_info[4]="nitrogen.niden.net html db"
rsync_info[5]="nitrogen.niden.net html svn"
rsync_info[6]="argon.niden.net html rsync"
```

This is the first array which holds descriptions to what needs to be done as far as source is concerned. These descriptions get appended to the log and helps me identify what step I am in.

```sh
# RSync Source Folders
rsync_source[1]="beryllium.niden.net:/var/www/localhost/htdocs/"
rsync_source[2]="beryllium.niden.net:/niden_backup/db/"
rsync_source[3]="nitrogen.niden.net:/var/www/localhost/htdocs/"
rsync_source[4]="nitrogen.niden.net:/niden_backup/db"
rsync_source[5]="nitrogen.niden.net:/niden_backup/svn"
rsync_source[6]="argon.niden.net:/var/www/localhost/htdocs/"
```

This array holds the source host and folder. Remember that I have already exchanged SSH keys with each server, therefore when the script runs there is a direct connection to the source server. If you need to keep things a bit more secure for you, then you will need to alter the contents of the rsync_source array so that it reflects the user that you log in with as well as the password.

```sh
# RSync Target Folders
rsync_target[1]="beryllium.niden.net/html/"
rsync_target[2]="beryllium.niden.net/db/"
rsync_target[3]="nitrogen.niden.net/html/"
rsync_target[4]="nitrogen.niden.net/db/"
rsync_target[5]="nitrogen.niden.net/svn/"
rsync_target[6]="argon.niden.net/html/"
```

This array holds the target locations for the rsync. These folders exist in my case under the `/storage` subfolder.

```sh
# GZip target files
servers[1]="beryllium.niden.net"
servers[2]="nitrogen.niden.net"
servers[3]="argon.niden.net"
```

This array holds the names of the folders to be archived. These are the folders directly under the `/storage` folder and I am also using this array for the prefix of the compressed files. The suffix of the compressed files is a date/time stamp.

Here is how the script evolves:

```sh
echo "BACKUP START" >> $BACKUP_DRIVE/logs/$DATE.log
date >> $BACKUP_DRIVE/logs/$DATE.log

echo "BACKUP START" >> $BACKUP_DRIVE/logs/$DATE.log
date >> $BACKUP_DRIVE/logs/$DATE.log

# Loop through the RSync process
element_count=${#rsync_info[@]}
let "element_count = $element_count + 1"
index=1
while [ "$index" -lt "$element_count" ]
do
    echo ${rsync_info[$index]} > $BACKUP_DRIVE/logs/$DATE.log
    rsync $FLAGS ${rsync_source[$index]} $BACKUP_DRIVE/${rsync_target[$index]} > $BACKUP_DRIVE/logs/$DATE.log
    let "index = $index + 1"
done
```

The snippet above loops through the `rsync_info` array and prints out the information in the log file. Right after that it uses the `rsync_source` and `rsync_target` arrays (as well as the `FLAGS` variable) to rsync the contents of the source server with the local folder. Remember that all three arrays have to be identical in size (`rsync_info`, `rsync_source`, `rsync_target`).

The next thing to do is zip the data (I loop through the servers array)

```sh
# Looping to GZip data
element_count=${#servers[@]}
let "element_count = $element_count + 1"
index=1
while [ "$index" -lt "$element_count" ]
do
    echo "GZip ${servers[$index]}" > $BACKUP_DRIVE/logs/$DATE.log
    tar cvfz $BACKUP_DRIVE/${servers[$index]}-$DATE.tgz $BACKUP_DRIVE/${servers[$index]} > $BACKUP_DRIVE/logs/$DATE.log
    let "index = $index + 1"
done
```

The compression method I use is tar/gzip. I found it to be fast with a good compression ratio. You can choose anything you like.

Now I need to delete old files from the drives and copy the files on those drives. I use the servers array again.

```sh
# Looping to copy the produced files (if applicable) to the daily drive
element_count=${#servers[@]}
let "element_count = $element_count + 1"
index=1

while [ "$index" -lt "$element_count" ]
do
    # Copy the midnight files
    echo "Removing old daily midnight files" > $BACKUP_DRIVE/logs/$DATE.log
    rm -f $DAY_USB_DRIVE/${servers[$index]}/${servers[$index]}-$DATEBACK_DAY*.* > $BACKUP_DRIVE/logs/$DATE.log
    echo "Copying daily midnight files" > $BACKUP_DRIVE/logs/$DATE.log
    cp -v $BACKUP_DRIVE/${servers[$index]}-$DATE2-00-*.tgz $DAY_USB_DRIVE/${servers[$index]} &nbsp>>; $BACKUP_DRIVE/logs/$DATE.log
    rm -f $BACKUP_DRIVE/${servers[$index]}-$DATE2-00-*.tgz > $BACKUP_DRIVE/logs/$DATE.log

    # Now copy the files in the hourly
    echo "Removing old hourly files" > $BACKUP_DRIVE/logs/$DATE.log
    rm -f $HOUR_USB_DRIVE/${servers[$index]}/${servers[$index]}-$DATEBACK_HOUR*.* > $BACKUP_DRIVE/logs/$DATE.log
    echo "Copying daily midnight files" > $BACKUP_DRIVE/logs/$DATE.log
    cp -v $BACKUP_DRIVE/${servers[$index]}-$DATE.tgz $HOUR_USB_DRIVE/${servers[$index]} > $BACKUP_DRIVE/logs/$DATE.log
    rm -f $HOUR_USB_DRIVE/${servers[$index]}/${servers[$index]}-$DATEBACK*.* > $BACKUP_DRIVE/logs/$DATE.log
    let "index = $index + 1"
done

echo "BACKUP END" >> $BACKUP_DRIVE/logs/$DATE.log
```

The last part of the script loops through the servers array and:

* Deletes the old files (recycling of space) from the daily backup drive (`/storage/backup_daily`) according to the `DATEBACK_DAY` variable. If the files are not found a warning will appear in the log.
<li>Copies the daily midnight file to the daily drive (if the file does not exist it will simply echo a warning in the log - I do not worry about warnings of this kind in the log file and was too lazy to use an IF EXISTS condition)
* Removes the daily midnight file from the `/storage` drive.

The reason I am using copy and then remove instead of the move (`mv`) command is that I have found this method to be faster.

Finally the same thing happens with the hourly files

* Old files are removed (`DATEBACK_HOUR` variable)
* Hourly file gets copied to the `/backup_hourly` drive
* Hourly file gets deleted from the `/storage` drive

All I need now is to add the script in my crontab and let it run every hour.

**NOTE**: The first time you will run the script you will need to do it manually (not in a cron job). The reason behind it is that the first time rsync will need to download all the contents of the source servers/folders in the `/storage` drive so as to create an exact mirror. Once that lengthy step is done, the script can be added in the crontab. Subsequent runs of the script will download only the changed/deleted files.

This method can be very effective while not using a ton of bandwidth every hour. I have used this method for the best part of a year now and it has saved me a couple of times.

The last thing I need to present you is the backup script that I have for my databases. As you notice above the source folder of beryllium.niden.net as far as databases are concerned is `beryllium.niden.net/db/`. What I do is I dump and zip the databases every hour on my servers. Although this is not a very efficient way of doing things and it adds to the bandwidth consumption every hour (since the dump will create a new file every hour) I have the following script running on my database servers every hour at the 45th minute:

```sh
#!/bin/bash

DBUSER=mydbuser
DBPASS='dbpassword'
DBHOST=localhost
BACKUPFOLDER="/niden_backup"
DBNAMES="`mysql --user=$DBUSER --password=$DBPASS --host=$DBHOST --batch --skip-column-names -e "show databases"| sed 's/ /%/g'`"
OPTIONS="--quote-names --opt --compress "

# Clear the backu folder
rm -fR $BACKUPFOLDER/db/*.*

for i in $DBNAMES; do
    echo Dumping Database: $i
    mysqldump --user=$DBUSER --password=$DBPASS --host=$DBHOST $OPTIONS $i &gt; $BACKUPFOLDER/db/$i.sql
    tar cvfz $BACKUPFOLDER/db/$i.tqz $BACKUPFOLDER/db/$i.sql
    rm -f $BACKUPFOLDER/db/$i.sql
done
```

That's it.

The backup script can be found in my GitHub [here](https://github.com/niden/Hourly_Backup_Linux).

Update: The metric units for the drives were GB not MB. Thanks to [Jani Hartikainen](https://www.codeutopia.net/) for pointing it out.
