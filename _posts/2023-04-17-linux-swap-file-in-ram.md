---
layout: post
title: Linux Swap file in RAM
date: 2023-04-17T14:20:18.200Z
tags:
  - linux
  - swap
  - zram
  - zstd
---
Many of us have Linux machines, as our primary workstations. Mine comes with the Linux Mint flavor, and this post reflects that installation.

The installation I chose was what came out of the box. I am running Linux Mint Cinnamon 21 currently. I have removed some of the pre-installed packages, such as LibreOffice (I use the `.AppImage` file instead), but in all I have not changed anything else.

After the installation, as expected I had a swap disk. This is the disk where data can be swapped from memory (to the disk) when more memory is required than is available.

### Swap Disk

To speed things up, I changed my swap disk to a RAM based one. 

Why you might ask. The swap disk is used when the memory gets full and there is an operation that needs RAM. As such the OS will take something already in memory, put it in the swap disk and recover it later when required. Creating a swap disk in RAM might not be the best way to help your system.

Valid point. It does not make much sense to reduce your available RAM by creating your swap disk in your RAM. That will mean that when the system needs more ram, it will just move it from one part of the RAM to another.

Here comes `zRAM`. 

This Linux module allows us to create a swap disk in memory and the data stored in it is already compressed. Depending on the algorithm used, one can achieve from 1:2 to 1:3, possibly higher compression rates. Since the swap disk is in memory, it is very fast and the compression operation is really negligible, if one considers it a performance hit.

zRAM is particularly helpful when your machine does not have a lot of memory. The constant swapping of data on a disk based swap device, certainly slows operations down. However, with zRAM one can achieve the same result but much faster (since the data will be compresed).

There are many data compression algorithms such as `lz4`, `zlib`, `zstd` etc. I chose `zstd` for my implementation.

### Current Swap Disk

First, we need to figure out what our current swap disk is. 

```shell
cat /proc/swaps
```

This will output something like this:

```shell
$ cat /proc/swaps 
Filename                    Type        Size      Used    Priority
/dev/mapper/vgmint-swap_1   partition   5000000   0       -2  
```

We need to disable this device first. To do so, we need to edit `/etc/fstab`

```shell
sudo nano /etc/fstab
```

and comment out the line that sets up the swap disk

```shell
#/dev/mapper/vgmint-swap_1   none   swap   sw   0   0  
```

We can now either reboot our system or switch the swap disk off:

```shell
sudo swapoff /dev/mapper/vgmint-swap_1
```

### zRAM installation

Install `zRAM` as follows:

```shell
sudo apt install zram-config
```

A reboot is now required for changes to take effect.

Once the system comes back up, check the swap disk:

```shell
cat /proc/swaps
```

which should output something similar to this:

```shell
Filename     Type        Size       Used   Priority
/dev/zram0   partition   32886300   0      5 
```

By default, zRAM will use half of your memory for the swap disk. In my case it picked up 32G.

### Fine Tuning

You might not be happy that `zRAM` picked up half of your RAM and potentially want to reduce that. Additionally you might want to change the compression algorithm. Here is how to do that

To find what the compression algorithm used, you can issue this command:

```shell
cat /sys/block/zram0/comp_algorithm
```

This will output something like this (enabled algorithm in brackets):

```shell
lzo [lzo-rle] lz4 lz4hc 842 zstd
```

The configuration options are stored in `/usr/bin/init-zram-swapping` file. The file contents are similar to this:

```shell
$ sudo nano /usr/bin/init-zram-swapping
#!/bin/sh

modprobe zram

# Calculate memory to use for zram (1/2 of ram)
totalmem=`LC_ALL=C free | grep -e "^Mem:" | sed -e 's/^Mem: *//' -e 's/  *.*//'`
mem=$((totalmem / 2 * 1024))

# initialize the devices
echo $mem > /sys/block/zram0/disksize
mkswap /dev/zram0
swapon -p 5 /dev/zram0
```

By editing this file, we can now reduce the size of our disk (remember it picks up half the RAM by default), but also change the compression algorithm.

To change the size of the disk, change this line:

```shell
mem=$((totalmem / 2 * 1024))
```

To change the compression algoritm, change this:

```shell
mem=$((totalmem / 2 * 1024))
```

to this

```shell
mem=$((totalmem / 2 * 1024))
echo zstd > /sys/block/zram0/comp_algorithm
```

Reboot the system.

After the system comes back up, your new swap disk will be a `zRAM` one and it will use the compression mechanism you have chosen. In my case:

```shell
$ cat /sys/block/zram0/comp_algorithm
lzo lzo-rle lz4 lz4hc 842 [zstd] 
```


Enjoy!
