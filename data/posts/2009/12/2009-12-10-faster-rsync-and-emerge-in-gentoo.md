#### Scenario

Recently I have started setting up a cluster of 7 Gentoo boxes for a project I am working on. The problem with boxes coming right out of the setup process of a hosting company is that they do not contain the packages that you need. Therefore you need to setup your `USE` flags and emerge the packages you require as per the role of every box.
<img class="post-image" src="{{ cdnUrl }}/files/gentoo.png" />

I have implemented the following procedure many times in my local networks (since I have more than one Gentoo boxes) and have also implemented the same process at work (we run 3 Gentoo boxes).

The way to speed up `rsync` and `emerge` is to run a local `rsync` mirror and to use `http-replicator`. This will not make the packages compile faster but what it will do is reduce the resource usage (downloads in particular) of your network since each package will be downloaded only one time and reduce the time you have to wait for each package to be downloaded. The same applies with the `rsync`.

My network has as I said 7 boxes. 5 of them are going to be used as web servers so effectively they have the same `USE` flags and 2 as database servers. For the purposes of this tutorial I will name the web servers `ws1`, `ws2`, `ws3`, `ws4`, `ws5` and the database servers `db1`, `db2`. The `ws1` box will be used as the local `rsync` mirror and will run `http-replicator`.

I am going to set up the `/etc/hosts` file on each machine so that the local network is resolved in each box and no hits to the DNS are required. So for my network I have:

```sh
10.13.18.101  ws1
10.13.18.102  ws2
10.13.18.103  ws3
10.13.18.104  ws4
10.13.18.105  ws5
10.13.18.201  db1
10.13.18.202  db2
```

Modify the above to your specific setup needs.

#### Setting up a local `rsync`

##### Server setup (ws1)

There is a really good tutorial can be found in the [Gentoo Documentation](http://www.gentoo.org/doc/en/rsync.xml) but here is the short version:

The `ws1` box already has the `rsync` package in there. All I need to do is start the daemon. Some configuration is necessary before I start the service:

```sh
nano -w /etc/rsyncd.conf
```

and what I should have in there is:

```sh
# Restrict the number of connections
max connections = 5
# Important!! Always use chroot
use chroot = yes
# Just in case you are allowed only read only access
read only = yes
# The user has no privileges
uid = nobody
gid = nobody
# Recommended: Restrict via IP (subnets or just IP addresses)
hosts allow = 10.13.18.0/24
# Everyone else denied
hosts deny  = *

# The local portage
[niden-gentoo-portage]
path = /usr/portage
comment = niden.net Gentoo Portage tree
exclude = /distfiles /packages
```

That's it. Now I add the service to the default runlevel and start the service

```sh
rc-update add rsyncd default
/etc/init.d/rsyncd start
```

**NOTE**: If you have a firewall using `iptables`, you will need to add the following rule:

```sh
# RSYNC
-A INPUT --protocol tcp --source 10.13.18.0/24 --match state --state NEW --destination-port 873 --jump ACCEPT
```

##### Client setup

In my clients I need to edit the /etc/make.conf file and change the SYNC directive to:

```sh
SYNC="rsync://ws1/niden-gentoo-portage"
```

or I can use the IP address:

```sh
SYNC="rsync://10.13.18.101/niden-gentoo-portage"
```

Note that the path used in the SYNC command is what I have specified as a section in the `rsyncd.conf` file (`niden-gentoo-portage` in my setup). This path can be anything you like.

##### Testing

I have already run

```sh
emerge --sync
```

in the ws1 box, so all I need to do now is run it on my clients. Once I run it I can see the following (at the top of the listing):

```sh
emerge --sync
>>> Starting rsync with rsync://10.13.18.101/niden-gentoo-portage...
receiving incremental file list
......
```

So everything works as I expect it.

##### Setting up http-replicator

`http-replicator` is a proxy server. When a machine (the local or a remote) requests a package, `http-replicator` checks its cache and if the file is there, it passes it to the requesting machine. If the file doesn't exist though, `http-replicator` downloads it from a mirror and then passes it to the requesting machine. The file is then kept in `http-replicator`'s cache for future requests. This way I save on resources by downloading once and serving many times locally.

Although this might not seem as a 'pure speedup' it will make your installations and updates faster since the download factor will be reduced to a bare minimum. Waiting for packages like mysql, Gnome or others to be downloaded does take a long time. Multiply that time with the number of machines you have on your network and you can see the benefits of having a setup like this.

##### Server setup (ws1)

First of all I need to emerge the package

```sh
emerge http-replicator
```

Once everything is done I need to change the configuration file to suit my needs:

```sh
nano -w /etc/conf.d/http-replicator
```

and the file should have:

```sh
GENERAL_OPTS="--dir /var/cache/http-replicator"
GENERAL_OPTS="$GENERAL_OPTS --user portage"
DAEMON_OPTS="$GENERAL_OPTS"
DAEMON_OPTS="$DAEMON_OPTS --alias /usr/portage/packages/All:All"
DAEMON_OPTS="$DAEMON_OPTS --log /var/log/http-replicator.log"
DAEMON_OPTS="$DAEMON_OPTS --ip 10.13.18.*"
## The proxy port on which the server listens for http requests:
DAEMON_OPTS="$DAEMON_OPTS --port 8080"
```

The last line with the `--port` parameter specifies the port that the http-replicator will listen to. You can change it to whatever you want. Also the `--ip` parameter restricts who is allowed to connect to this proxy server. I have allowed my whole internal network; change it to suit your needs. Lastly the `--dir` option is where the cached data is stored. You can change it to whatever you like. I have left it to what it is. Therefore I need to create that folder:

```sh
mkdir /var/cache/http-replicator
```

Since I have specified that the user that this proxy will run as is portage (see `--user` directive above) I need to change the owner of my cache folder:

```sh
chown portage:portage /var/cache/http-replicator
```

I add the service to the default runlevel and start the service

```sh
rc-update add http-replicator default
/etc/init.d/http-replicator start
```

**NOTE**: If you have a firewall using iptables, you will need to add the following rule:

```sh
# HTTP-REPLICATOR
-A INPUT --protocol tcp --source 10.13.18.0/24 --match state --state NEW --destination-port 8080 --jump ACCEPT
```

You will need also to regularly run

```sh
repcacheman
```

and

```sh
rm -rf /usr/portage/distfiles/*
```

to clear the distfiles folder. I have added those in a bash script and I run it every night using my cron.

##### Client setup

In my clients I need to edit the /etc/make.conf and change the SYNC directive to:

```sh
http_proxy="http://ws1:8080"
RESUMECOMMAND=" /usr/bin/wget -t 5 --passive-ftp  \${URI} -O \${DISTDIR}/\${FILE}"</pre>
```

I have commented any previous `RESUMECOMMAND` statements.

##### Testing

The testing begins in one of the clients (you can choose any package):

```sh
emerge logrotate
```

and see in the output that everything works fine

```sh
ws2 ~ # emerge logrotate
Calculating dependencies... done!

>>> Verifying ebuild manifests

>>> Emerging (1 of 1) app-admin/logrotate-3.7.8
>>> Downloading 'http://distfiles.gentoo.org/distfiles/logrotate-3.7.8.tar.gz'
--2009-12-10 06:46:47--  http://distfiles.gentoo.org/distfiles/logrotate-3.7.8.tar.gz
Resolving ws1... 10.13.18.101
Connecting to ws1|10.13.18.101|:8080... connected.
Proxy request sent, awaiting response... 200 OK
Length: 43246 (42K)
Saving to: `/usr/portage/distfiles/logrotate-3.7.8.tar.gz'

100%[=============================>] 43,246      --.-K/s   in 0s

2009-12-10 06:46:47 (89.6 MB/s) - `/usr/portage/distfiles/logrotate-3.7.8.tar.gz' saved [43246/43246]
.....
```

#### Final thoughts

Setting up local proxies allows your network to be as efficient as possible. It does not only reduce the download time for your updates but it is also courteous to the Gentoo community. Since mirrors are run by volunteers or non-profit organizations, it is only fair to not abuse the resources by downloading an update more than once for your network.

I hope this quick guide will help you and your network :)
