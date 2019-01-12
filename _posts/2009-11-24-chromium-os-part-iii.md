---
layout: post
title: Chromium OS Part III
date: 2009-11-24T23:45:00.000Z
tags:
  - chromium
  - chromium os
  - google
  - installation
  - linux
  - how-to
---
Continued from [Part II](/post/chromium-os-part-ii)

I had to shut the computer down because I had a train to catch. However booting up again the computer on the train and trying to enter the chroot environment produced some errors. It might very well be the fact that I do not have an Internet connection (at least a reliable one).
<img class="post-image" src="/files/2009-11-24-chromium-os.png" />

So this post will have to wait until I get back home so that I can continue the installation. The key point here will be how to add the Chromium OS image over the second partition of my hard drive so that I can keep my dual boot system. I will run Image for DOS and clone my hard drive, in an effort to keep a mirror backup of my notebook at this particular point in time - you never know when you will use it.

So I booted again into Ubuntu and started the process. I run

```sh
./create_chroot.sh
```

but it complained that a chroot already exists. I then run

```sh
./create_chroot.sh --delete
```

which effectively removed the existing chroot and replaced it with a new one. Little did I know that I only had to run

```sh
./enter_chroot.sh
```

to re-enter my previous chroot and continue where I left off. Oh well, you live and learn :)

Just to be on the safe side I rerun the `./build_platform_packages.sh`, `./build_kernel.sh` and `./build_image.sh` scripts. I am now exactly where I was prior to shutting the computer off. I have built the platform packages, the kernel and the image.

#### Using the image

##### Check the contents of the image

I will mount the image locally to ensure that everything is OK. Please note that the folder below is the one created on my machine and might very well be different than yours. At the end of the <em>build_image.sh</em> script you will see a message that will reveal where your image folder is.

```sh
cd ~/trunk/src/build/images/999.999.32909.021312-a1/
sudo mount -o loop rootfs.image rootfs
sudo chroot rootfs
```

Inside the image basic commands will reveal success or failure:

```sh
df
dpkg -l
```

Once everything is OK (or at least seems OK) I exit and unmount the image.

```sh
exit
sudo umount rootfs
```

Although I got a `cannot read table of mounted file systems: No such file or directory` when I run `df`, `dpkg` had a long list of packages installed. I will for the moment ignore the df output and proceed to the next steps.

##### Copy the image to a USB key

Somehow I have misplaced my 16GB USB drive so I had to borrow a 4GB one from a friend of mine. This step copies the actual image from the hard drive to the USB drive. The drive itself is wiped clean so make sure that you have backed up the data that you have on it prior to running this step.

You need to find out the device that your USB drive corresponds to. Running:

```sh
sudo fdisk -l
```

will reveal which device is the USB drive. For my system it is `/dev/sdc1`. Outside the chroot you run the script `image_to_usb.sh`. The command is:

```sh
./image_to_usb.sh --from=~/chromiumos/src/build/images/SUBDIR --to=/dev/USBKEYDEV
```

and for my system the command was:

```sh
./image_to_usb.sh --from=~/chromiumos/src/build/images/999.999.32909.021312-a1/ --to=/dev/sdc1
```

The output on the screen running the above command is:

```sh
ndimopoulos@ARGON:~/chromiumos/src/scripts$ ./image_to_usb.sh --from=~/chromiumos/src/build/images/999.999.32909.021312-a1/ --to=/dev/sdc1
Copying USB image /usr/local/chromiumos/chromiumos.git/src/build/images/999.999.32909.021312-a1 to device /dev/sdc1...
This will erase all data on this device:
Disk /dev/sdc1: 4013 MB, 4013917184 bytes
Are you sure (y/N)? y
attempting to unmount any mounts on the USB device
Copying root fs...
opening for read /usr/local/chromiumos/chromiumos.git/src/build/images/999.999.32909.021312-a1/rootfs.image
opening for write /dev/sdc1
seeking to 1992294912 bytes in output file
copy 996147200 bytes took 102.384 s
speed: 9.7 MB/s
Creating stateful partition...
mke2fs 1.41.9 (22-Aug-2009)
Filesystem label=C-STATE
OS type: Linux
Block size=4096 (log=2)
Fragment size=4096 (log=2)
60800 inodes, 243200 blocks
12160 blocks (5.00%) reserved for the super user
First data block=0
Maximum filesystem blocks=251658240
8 block groups
32768 blocks per group, 32768 fragments per group
7600 inodes per group
Superblock backups stored on blocks:
 32768, 98304, 163840, 229376

Writing inode tables: done
Creating journal (4096 blocks): done
Writing superblocks and filesystem accounting information: done

This filesystem will be automatically checked every 30 mounts or
180 days, whichever comes first.  Use tune2fs -c or -i to override.
Copying MBR...
opening for read /usr/local/chromiumos/chromiumos.git/src/build/images/999.999.32909.021312-a1/mbr.image
opening for write /dev/sdc1
copy 512 bytes took 0.001 s
speed: 0.0 MB/s
Done.
```

I have booted the system using the USB drive and I logged in the system using my google account. Using Ctrl+Alt+T I open a terminal and enter:

```sh
/usr/sbin/chromeos-install
```

This asks now for the password that I have set up earlier (the one stored in the text file) and then it nukes the hard drive replacing everything (make sure you backup your hard drive).

A bit later I unpluged the USB drive and rebooted. Unfortunately things did not work very well but that is probably due to my particular hardware. I will retry this on my other notebook and update this blog post.
