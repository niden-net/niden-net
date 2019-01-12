---
layout: post
title: Keeping your Linux users in sync in a cluster
date: 2011-02-20T23:45:00.000Z
tags:
  - bash
  - linux
  - nfs
  - rsync
  - storage
  - how-to
---
As websites become more and more popular, your application might not be able to cope with the demand that your users put on your server. To accommodate that, you will need to move out of the one server solution and create a cluster. The easiest cluster to create would be with two servers, one to handle your web requests (HTTP/PHP etc.) and one to handle your database load (MySQL). Again this setup can only get you so far. If your site is growing, you will need a cluster of servers.
<img class="post-image" src="/files/2011-02-20-sync.png" />

#### Database

The scope of this How-To will not cover database replication; I will need to dedicate a separate blog post for that. However, clustering your database is relatively easy with MySQL replication. You can set a virtual hostname (something like `mysql.mydomain.com`) which is visible only within your network. You then set up the configuration of your application to use that as the host. The virtual hostname will map to the current master server, while the slave(s) will only replicate.

For instance, if you have two servers A and B, you can configure both of them to become master or slave in MySQL. You then set one of them as master (A) and the other as slave (B). If something happens to A, B gets promoted to master instantly. Once A comes back up, it gets demoted to a slave and the process is repeated if/when B has a problem. This can be a very good solution but you will need to have pretty evenly matched servers to keep with the demand. Alternatively B can be less powered than A and when A comes back up you keep it momentarily as a slave (until everything is replicated) and then promote it back to master.

One thing to note about replication (that I found through trial and error). MySQL keeps binary logs to handle replication. If you are not cautious in your deployment, MySQL will never recycle those logs and therefore you will soon run out of space when having a busy site. By default those logs will be under `/var/lib/mysql`.

By changing directives in `my.cnf` you can store the binary logs in a different folder and even set up 'garbage collection' or recycling. You can for instance set the logs to rotate every X days with the following directive in `my.cnf`:

```sh
expire_logs_days = 5
```

I set mine to 5 days which is extremely generous. If your replication is broken you must have the means to know about it within minutes (see [nagios](http://www.nagios.org/) for a good monitoring service). In most cases 2 days is more than enough.

#### Files

There are numerous ways of keeping your cluster in sync. A really good tool that I have used when playing around with a cluster is [csync2](http://oss.linbit.com). Installation is really easy and and all you will need is to run a cron task every X minutes (up to you) to synchronize the new files. Imagine it as a two way [rsync](http://samba.anu.edu.au/rsync/). Another tool that can do this is [unison](http://www.cis.upenn.edu/~bcpierce/unison/) but I found it to be slow and difficult to implement - that's just me though.

Assume an implementation of a website being served by two (or more) servers behind a load balancer. If your users upload files, you don't know where those files are uploaded, which server that is. As a result if user A uploads the file <em>abc.txt</em> to server A, user B might be served the content from server B and would not be able to access the file. [csync2](http://oss.linbit.com) would synchronize the file across the number of servers, thus providing access to the content and keeping multiple copies of the content (additional backup if you like).

#### NFS

An alternative to keeping everything synchronized is to use a NFS. This approach has many advantages and some disadvantages. It is up to you on whether the disadvantages are something you can live with.

##### Disadvantages

* NFS is slow - slower than the direct access to a local hard drive.
* Most likely you will use a symlink to the NFS folder, which can slow things down even more.

##### Advantages

* The NFS does not rely on the individual web servers for content.
* The web servers can be low to medium spec boxes without the need to have really fast and large hard drives
* A well designed NFS with <a href="http://oss.linbit.com/">DRDB</a> provides a raid-1 over a network. Using gigabit Network Interface Cards you can keep performance at really high levels.

I know that my friend [Floren](http://www.axivo.com/) does not agree with my approach on the NFS and would definitely have gone with the [csync2](http://oss.linbit.com) approach. Your implementation depends on your needs.

#### Users and Groups

Using the NFS approach, we need to keep the files and permissions properly set up for our application. Assume that we have two servers and we need to create one user to access our application and upload files.

The user has been created on both servers and the files are stored on the NFS. Connecting to server A and looking at the files we can see something like this:

```sh
drwxr-xr-x 17 niden  niden  4096 Feb 18 13:41 www.niden.net
drwxr-xr-x  5 niden  niden  4096 Nov 15 22:10 www.niden.net.files
drwxr-xr-x  7 beauty beauty 4096 Nov 21 17:42 www.beautyandthegeek.it
```

However when connecting to server B, the same listing tells another story:

```sh
drwxr-xr-x 17 508    510    4096 Feb 18 13:41 www.niden.net
drwxr-xr-x  5 508    510    4096 Nov 15 22:10 www.niden.net.files
drwxr-xr-x  7 510    511    4096 Nov 21 17:42 www.beautyandthegeek.it
```

The problem here is the uid and gid of the users and groups of each user respectively. Somehow (and this is really easy to happen) server A had one or more users added to it, thus the internal counter of the user IDs has been increased by one or more and is not identical to that one of server B. So adding a new user in server A will get the uid 510 while on server B the same process will produce a user with a uid of 508.

To have all users setup on all servers the same way, we need to use two commands: [groupadd](http://man.he.net/man8/groupadd) and [useradd](http://man.he.net/man8/useradd) (in some Linux distributions you might find them as addgroup and adduser).

##### groupadd

First of all you will need to add groups. You can of course keep all users in one group but my implementation was to keep one user and one group per access. To cater for that I had to first create a group for every user and then the user account itself. Like users, groups have unique ids (gid). The purpose of gid is:

> The numerical value of the groups ID. This value must be unique, unless the -o option is used. The value must be non-negative. The default is to use the smallest ID value greater than 999 and greater than every other group. Values between 0 and 999 are typically reserved for system accounts.

I chose to assign each group a unique id (you can override this behavior by using the -o switch in the command below, thus allowing a gid to be used in more than one group). The arbitrary number that I chose was 2000.

As an example, I will set `niden` as the user/group for accessing this site and <em>beauty</em> as the user/group that accesses [BeautyAndTheGeek.IT](http://www.beautyandthegeek.it/). Note that this is only an example.

```sh
groupadd --gid 2000 niden
groupadd --gid 2001 beauty
```

Repeat the process as many times as needed for your setup. Connect to the second server and repeat this process. Of course if you have more than two servers, repeat the process on each of the servers that you have (and each accesses your NFS)

##### useradd

The next step is to add the users. Like groups, we will need to set the uid up. The purpose of the uid is:

> The numerical value of the users ID. This value must be unique, unless the -o option is used. The value must be non-negative. The default is to use the smallest ID value greater than 999 and greater than every other user. Values between 0 and 999 are typically reserved for system accounts.

Like with the groups, I chose to assign each user a unique id starting from 2000.

So to in the example above, the commands that I used were:

```sh
useradd --uid 2000 -g niden --create-home niden
useradd --uid 2000 -g beauty --create-home beauty
```

You can also use a different syntax, utilizing the numeric gids:

```sh
useradd --uid 2000 --gid 2000 --create-home niden
useradd --uid 2000 --gid 2001 --create-home beauty
```

Again, repeat the process as many times as needed for your setup and to as many servers as needed.

In the example above I issued the --create-home switch (or -m) so as a home folder to be created under /home for each user. Your setup might not need this step. Check the references at the bottom of this blog post for the manual pages for [groupadd](http://man.he.net/man8/groupadd) and [useradd](http://man.he.net/man8/useradd).


I would suggest that you keep a log of which user/group has which uid/gid. It helps in the long run, plus it is a good habit to keep proper documentation on projects :)

#### Passwords?

So how about the passwords on all servers? My approach is crude but effective. I connected to the first server, and set the password for each user, writing down what the password was:

```sh
passwd niden
```

Once I had all the passwords set, I opened the /etc/shadow file.

```sh
nano /etc/shadow
```

and that revealed a long list of users and their scrambled passwords:

```sh
niden:$$$$long_string_of_characters_goes_here$$$$:13864:0:99999:7:::
beauty:$$$$again_long_string_of_characters_goes_here$$$$:15009:0:99999:7:::
```

Since I know that I added niden and beauty as users, I copied these two lines. I then connected to the second server, opened `/etc/shadow` and located the two lines where the `niden` and `beauty` users are referenced. I deleted the existing lines, and pasted the ones that I had copied from server A. Saved the file and now my passwords are synchronized in both servers.

### Conclusion

The above might not be the best way of keeping users in sync in a cluster but it gives you an idea on where to start. There are different implementations available (Google is your friend) and your mileage might vary. The above has worked for me for a number of years since I never needed to add more than a handful of users on the servers each year.

### References

* [MySQL System Variables](http://dev.mysql.com/doc/refman/5.0/en/server-system-variables.html)
* [Nagios](http://www.nagios.org/)
* [csync2](http://oss.linbit.com)
* [rsync](http://samba.anu.edu.au/rsync/)
* [Unison File Synchronizer](http://www.cis.upenn.edu/~bcpierce/unison/)
* [DRDB](http://oss.linbit.com/)
* [Axivo Inc.](http://www.axivo.com/)
* [groupadd](http://man.he.net/man8/groupadd)
* [useradd](http://man.he.net/man8/useradd)
