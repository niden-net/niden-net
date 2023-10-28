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
Many of us use Linux machines as our primary workstations. I personally use Linux Mint, and this post reflects my current installation.

I opted for the default installation that came out of the box. I'm currently running Linux Mint Cinnamon 21. While I have removed some pre-installed packages, such as LibreOffice (since I use the .AppImage file instead), I haven't made any other significant changes.

After the installation, as expected, I had a swap disk. This is a disk that is used when there is a need to swap data from memory to disk when the available memory is insufficient.

### Swap Disk

To improve performance, I decided to switch to a RAM-based swap disk.

You might wonder why. The swap disk comes into play when the system's memory is full, and an operation requires more RAM. In such cases, the operating system moves data from memory to the swap disk and retrieves it when needed. However, creating a swap disk in RAM may not be the most efficient approach since it essentially means moving data from one part of the RAM to another when the system requires more memory.

This is where `zRAM` comes in.

zRAM is a Linux module that allows you to create a swap disk in memory, and the data stored in it is already compressed. Depending on the compression algorithm used, you can achieve compression ratios ranging from 1:2 to 1:3, or even higher. Since the swap disk is in memory, it's incredibly fast, and the compression operation has minimal impact on performance.

zRAM is particularly beneficial when your machine has limited memory. Constantly swapping data on a disk-based swap device can significantly slow down operations. However, with `zRAM`, you can achieve the same result but much faster, given that the data is compressed.

There are various data compression algorithms available, such as `lz4`, `zlib`, `zstd`, and more. For my implementation, I chose `zstd`.

### Current Swap Disk

First, we need to identify our current swap disk:

```shell
cat /proc/swaps
```

This command will produce output similar to this:

```shell
$ cat /proc/swaps 
Filename                    Type        Size      Used    Priority
/dev/mapper/vgmint-swap_1   partition   5000000   0       -2  
```

We need to disable this device first. To do so, we should edit the `/etc/fstab` file:

```shell
sudo nano /etc/fstab
```

Then, comment out the line that sets up the swap disk:

```shell
#/dev/mapper/vgmint-swap_1   none   swap   sw   0   0  
```

Now, you can either reboot your system or turn off the swap disk:

```shell
sudo swapoff /dev/mapper/vgmint-swap_1
```

### Installing `zRAM`

To install zRAM, follow these steps:

```shell
sudo apt install zram-config
```

A reboot is required for the changes to take effect.

Once your system restarts, check the status of the swap disk:

```shell
cat /proc/swaps
```

The output should be something like this:

```shell
Filename     Type        Size       Used   Priority
/dev/zram0   partition   32886300   0      5 
```

By default, `zRAM` will allocate half of your memory for the swap disk. In my case, it picked up 32GB.

### Fine Tuning

If you're not satisfied with zRAM using half of your RAM or want to change the compression algorithm, here's how to do it:

To determine the compression algorithm in use, issue this command:


```shell
cat /sys/block/zram0/comp_algorithm
```

This will display something like this (with the enabled algorithm in brackets):

```shell
lzo [lzo-rle] lz4 lz4hc 842 zstd
```

The configuration options are stored in the `/usr/bin/init-zram-swapping` file. The file contents are similar to this:

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

To adjust the disk size, modify this line:

```shell
mem=$((totalmem / 2 * 1024))
```

To change the compression algorithm, replace this:

```shell
mem=$((totalmem / 2 * 1024))
```

with this:

```shell
mem=$((totalmem / 2 * 1024))
echo zstd > /sys/block/zram0/comp_algorithm
```

Reboot the system.

After the system restarts, your new swap disk will be a zRAM one, and it will use the compression mechanism you've selected. In my case, it looked like this:

```shell
$ cat /sys/block/zram0/comp_algorithm
lzo lzo-rle lz4 lz4hc 842 [zstd] 
```


Enjoy!
