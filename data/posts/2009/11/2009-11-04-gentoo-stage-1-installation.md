This is my effort to install Gentoo Linux on my *Acer Ferrari LMi 3000*.
<img class="post-image" src="{{ cdnUrl }}/files/gentoo.png" />

The first thing I did was to start ssh. The live CD of Gentoo had already identified my network card which is based on the `via-rhine` module.

```sh
# /etc/init.d/sshd start
```
The key is generated and of course I needed to change the password to something known (it is scrambled for security reasons by the Live CD)

```sh
# passwd
```

Following that I left the notebook where it is and invoked PuTTY from my Windows box to access it. I printed the Gentoo Handbook guide just in case and had it near by as a reference.

I decided to check the hard drive performance. I used

```sh
# hdparm -tT /dev/hda
```

and it reported:

```sh
/dev/hda:
Timing cached reads: 1068 MB in 2.00 seconds = 533.81 MB/sec 
Timing buffered disk reads: 80 MB in 3.05 seconds = 26.22 MB/sec
```

Just in case I activated DMA:

```sh
# hdparm -d 1 /dev/hda
```

and it reported:

```sh
/dev/hda:
setting
using_dma to 1 (on)
using_dma = 1 (on)
```

So now with the hard drive tweaked for max performance the network works just fine (I am using ssh so it must be working ) I skip to chapter 4 to prepare my disks. I read thoroughly through the installation guide and decided to proceed with the following structure:

```sh
Partition Filesystem  Size             Description
/dev/hda1 ReiserFS    110 Mb           Boot partition
/dev/hda2 swap       1024 Mb           Swap partition
/dev/hda3 ReiserFS   Rest of the Disk  Root partition
```

This partition scheme is nearly identical to the one used by the guide only that my choice of filesystem is ReiserFS and I have increased the swap to 1024 Mb.

I used the cfdisk tool that comes with the CD.

```sh
# cfdisk
and in that program I defined
<pre><code>Name Flags    Part   Type  FS Type [Label] Size (MB)
----------------------------------------------------
hda1 Boot   Primary  Linux                   106.93
hda2        Primary  Linux swap             1019.94
hda3        Primary  Linux                 58884.78
```

I toggled the `Boot` flag from the interface after having selected `hda1`. Once I finished with the partitioning I chose `Write` and confirmed it so that the partition table is written on the disk. I chose `Quit` and then rebooted the system just in case.

```sh
# reboot
```

I restarted the system and it booted again from the Live CD. Again I started `sshd` after setting a password for the root account. Now it is the time to format my partitions. The first one is the boot partition and I chose to label it `boot`

```sh
# mkreiserfs -l boot /dev/hda1
```

following that the root partition which was labeled `root`

```sh
# mkreiserfs -l root /dev/hda3
```

Finally time to format the swap partition

```sh
# mkswap /dev/hda2
```

and activate it

```sh
# swapon /dev/hda2
```

The partitions are now ready so all I have to do is mount them and start the installation.

```sh
# mount /dev/hda3 /mnt/gentoo
```

I will need to create a boot folder in the newly mounted partition

```sh
# mkdir /mnt/gentoo/boot
```

and now mount the boot partition in that folder

```sh
# mount /dev/hda1 /mnt/gentoo/boot
```

Moving on I need to check the date/time issuing the following command:

```sh
# date
```

The time was a bit off so I had to set it using the following command:

```sh
# date 120123042004
```

(where 12 is the month 01 is the day 23 is the hour, 04 the minute and 2004 the year)

Now it is time to fetch the tarball. First I change the directory to `/mnt/gentoo`

```sh
# cd /mnt/gentoo
```

and then I use the `links2` program (I like it better) to navigate through the mirrors and pick one which is closer to me (Austria)

```sh
# links2 http://www.gentoo.org/main/en/mirrors.xml
```

I chose the Inode network and then navigated to `/releases/x86/2004.2/stages/x86` and downloaded the `stage1-x86-2004.2.tar.bz2`. Following that I unpacked the stage:

```sh
# tar -xvjpf stage1-x86-2004.3.tar.bz2
```

Then I had to tweak the `make.conf` file

```sh
# nano /mnt/gentoo/etc/make.conf
```

My `make.conf` is as follows:

```sh
USE="-* X aalib acl acpi aim alsa apache2 apm audiofile 
avi berkdb bidi bindist bitmap-fonts bzlib caps cdr 
cpdflib crypt cscope ctype cups curl curlwrappers 
dba dbx dga dio directfb divx4linux dvd dvdr encode 
ethereal exif fam fastcgi fbcon fdftk flac flash 
flatfile foomaticdb ftp gd gdbm ggi gif gmp gnome 
gnutls gphoto2 gpm gtk gtk2 gtkhtml iconv icq imagemagick 
imap imlib inifile innodb ipv6 jabber jack jikes jpeg 
kerberos krb4 ladcca lcms ldap libwww mad maildir 
mailwrapper mbox mcal memlimit mhash mikmod ming mmap mmx 
motif moznocompose moznoirc moznomail mpeg mpi msn mssql 
mysql -mysqli nas ncurses netcdf nhc98 nis nls offensive 
oggvorbis opengl oscar pam pcmcia pcntl pcre pda pdflib 
perl php pic pie plotutils png pnp posix ppds prelude 
python quicktime readline samba sasl scanner sdl session 
shared sharedmem simplexml slang slp snmp soap sockets 
socks5 speex spell spl ssl svga sysvipc szip tcltk tcpd 
tetex theora tidy tiff tokenizer truetype trusted uclibc 
unicode usb vhosts videos wavelan wddx wmf xface xine 
xml xml2 xmlrpc xmms xosd xprint xsl xv xvid yahoo yaz 
zeo zlib x86"
CHOST="i686-pc-linux-gnu"

CFLAGS="-march=athlon-xp -O3 -pipe -fomit-frame-pointer"

CXXFLAGS="${CFLAGS}"
ACCEPT_KEYWORDS="~x86"
PORTAGE_TMPDIR=/var/tmp
PORTDIR=/usr/portage
DISTDIR=${PORTDIR}/distfiles
PKGDIR=${PORTDIR}/packages
PORT_LOGDIR=/var/log/portage
PORTDIR_OVERLAY=/usr/local/portage

http_proxy="http://taurus.niden.net:8080"
    RESUMECOMMAND="
        /usr/bin/wget 
        -t 5 
        –passive-ftp \${URI} 
        -O \${DISTDIR}/\${FILE}"

GENTOO_MIRRORS="
    http://gentoo.inode.at/ 
    http://gentoo.osuosl.org 
    http://gentoo.oregonstate.edu"
SYNC="rsync://taurus.niden.net/portage"

MAKEOPTS="-j2"

AUTOCLEAN="yes"
FEATURES="sandbox"
```
You will notice that I use

```sh
http_proxy="http://taurus.niden.net:8080" \
    RESUMECOMMAND="
        /usr/bin/wget 
            -t 5 
            –passive-ftp \${URI} 
            -O \${DISTDIR}/\${FILE}"
```

because I have set up the `httpd-replicator` on my server and keep a local rsync mirror so that I don’t abuse the internet bandwidth. You will not need these lines on your installation. Additionally I set up my sync mirror to be my local server

```sh
SYNC="rsync://taurus.niden.net/portage"
```

whereas you will need to use one of the below (the closer to your location the better)

```sh
Default: "rsync://rsync.gentoo.org/gentoo-portage" 
North America: "rsync://rsync.namerica.gentoo.org/gentoo-portage" 
South America: "rsync://rsync.samerica.gentoo.org/gentoo-portage" 
Europe: "rsync://rsync.europe.gentoo.org/gentoo-portage" 
Asia: "rsync://rsync.asia.gentoo.org/gentoo-portage" 
Australia: "rsync://rsync.au.gentoo.org/gentoo-portage"
```

Also I set up some Portage paths which have to be created (`PORTDIR_OVERLAY` and `PORT_LOGDIR`):

```sh
# mkdir /mnt/gentoo/usr/local/portage 
# mkdir /mnt/gentoo/var/log/portage
```

Before chrooting I need to copy the `resolv.conf` file in our mounted partition

```sh
# cp -L /etc/resolv.conf /mnt/gentoo/etc/resolv.conf
```

mount the proc partition

```sh
# mount -t proc none /mnt/gentoo/proc
```

and chroot to the new environment

```sh
# chroot /mnt/gentoo /bin/bash 
# env-update 
# source /etc/profile
```

Now let us update the portage for the first time

```sh
# emerge sync
```

and here comes the wait - bootstraping

```sh
# cd /usr/portage 
# scripts/bootstrap.sh
```

The compilation started at 13:30 and finished at 16:14, 3 hours later error free so I moved on to emerge my whole system.

```sh
# emerge system
```

73 packages were to be merged and for that I started at 06:00 and finished at 8.27. Not bad for my baby notebook!
There was one config file that needed updating so I went on and updated it:

```sh
# etc-update
```

It appeared that there were trivial changes, nothing to report.

So now off to set our timezone. For me it is Vienna, Austria. A little look at my system with:

```sh
# ls /usr/share/zoneinfo
```

reveals a Europe folder which in turn has the Vienna zone. Hence the command to set the link to my timezone:

```sh
# ln -sf /usr/share/zoneinfo/Europe/Vienna /etc/localtime
```

Okay now to the easy stuff. We need to grab a kernel. From the choices (and the handbook plus the Gentoo Kernel Guide have a wealth of information helping you choose) I opted for `gentoo-dev-sources`.

```sh
# emerge gentoo-dev-sources
```

Before I choose what I need for my kernel (modules or built in) I went and read the Gentoo udev guide. I choose to with udev since this is the way things are moving and I might as well get a head start with it.

First I need to emerge the udev, which will emerge baselayout and hotplug

```sh
# emerge udev
```

and also `coldplug` for boot support on plugged devices

```sh
# emerge coldplug
```

Now I have to compile the kernel. This requires a bit more attention so I tried to get it first time right.

```sh
# cd /usr/src/linux 
# make menuconfig
```

After making my choices I compile the kernel

```sh
# make && make modules_install
```

I installed the kernel by copying the relevant file in my boot partition:

```sh
# cp arch/i386/boot/bzImage /boot/kernel-2.6.10-gentoo-r4
```

I also copy the System.map and the .config file just in case:

```sh
# cp System.map /boot/System.map-2.6.10-gentoo-r4 
# cp .config /boot/config-2.6.10-gentoo-r4
```

At this point I need to sort out the fstab file for my system to load properly.
```sh
# nano -w /etc/fstab
```

My fstab is as follows:

```sh
/dev/hda1 /boot      reiserfs noauto,noatime,notail 1 2
/dev/hda2 none       swap     defaults              0 0
/dev/hda3 /          reiserfs noatime               0 1
none      /proc      proc     defaults              0 0
none      /dev/pts   devpts   defaults              0 0
none      /dev/shm   tmpfs    defaults              0 0
none      /sys       sysfs    defaults              0 0
/dev/hdc  /mnt/cdrom auto     noauto,ro             0 0
```

What follows is the host name, domain name and network configuration.

**Hostname**

```sh
# nano -w /etc/conf.d/hostname
```

**Domain name**

```sh
# nano -w /etc/conf.d/domainname
```

Adding the domain name to the default runlevel

```sh
# rc-update add domainname default
```

There is no need for me to touch the `/etc/conf.d/net` file since I will be using DHCP for my LAN. I won’t add it to the default runlevel either (the network) since I don’t usually connect to the network by the LAN interface rather than the wireless one - for that a bit later.

Finally I need to set up the `hosts` file:

```sh
# nano -w /etc/hosts
```

with the available hosts in my network.

What follows is the `PCMCIA`. This is handled by emerging the `pcmcia-cs` package (note that I am using the -X flag since I don’t want `xorg-x11` to be installed now - the handbook is king!)

```sh
# USE="-X" emerge pcmcia-cs
```

Critical dependency is `dhcpd`. I need to merge it so that I can obtain an IP address from my router

```sh
# emerge dhcpd
```

Also critical is to set the root password

```sh
# passwd
```

I am also emerging `pciutils`. These will give me `lsmod` and `lspci` later on

```sh
# emerge pciutils
```

Now is the time for the system tools. I will install a system logger, a cron daemon, file system tools and bootloader.

**System Logger** - I chose syslog-ng.

```sh
# emerge syslog-ng
```

and added it to the default runlevel

```sh
# rc-update add syslog-ng default
```

**Cron daemon** - I chose vixie-cron

```sh
# emerge vixie-cron
```

and added it to the default runlevel

```sh
# rc-update add vixie-cron default
```

**File System tools** - Naturally I need reiserprogs due to my file system

```sh
# emerge reiserfsprogs
```

**Bootloader** - I chose grub.

```sh
# emerge grub
```

once grub was compiled I setup my `grub.conf`

```sh
# nano -w /boot/grub/grub.conf
```

Now let us set grub properly by updating the `/etc/mtab`

```sh
# cp /proc/mounts /etc/mtab
```

and `grub-install` will finish the job

```sh
# grub-install –root-directory=/boot /dev/hda
```

Finally we are ready to reboot the system.

Exit the chrooted environment

```sh
# exit
```

change directory to the root of the Live CD

```sh
# cd /
```

unmount the mounted partitions

```sh
# umount /mnt/gentoo/boot/ /mnt/gentoo/proc/ /mnt/gentoo/
```

and reboot

```sh
# reboot
```

Make sure you eject the CD when the system reboots because you don’t want to boot from it.

Well it appears to be OK so far since the grub menu showed up and after the whole boot sequence I had my first Linux login.

