---
layout: post
title: Chromium OS - Part II
tags: [chromium, chromium os, google, installation, linux, how-to]
---

Continued from [Part I](/post/chromium-os-part-i)

The download took quite a while, so I thought it might be a good idea to split this post in parts, so as to ensure good readability.
<img class="post-image" src="{{ site.baseurl }}/files/2009-11-24-chromium-os.png" />

I need to create some symlinks. Also a good place to add my repository is /usr/local hence the commands for Chromium OS and Chromium respectively.

```sh
sudo mv chromiumos/ /usr/local/
sudo mv chromium/ /usr/local/
```

and now adding the symbolic links

```sh
ln -s /usr/local/chromiumos/chromiumos.git ~/chromiumos
ln -s /usr/local/chromium ~/chromium
```

#### Creating the local repository

All the scripts are in the src/script folder. So let's go to that folder (the symbolic link set earlier helps :))

```sh
cd ~/chromiumos/src/scripts
```

and running the command to create the local repository:

```sh
./make_local_repo.sh
```

This command will ask you for your password - and bare in mind you must run all this as a normal user with sudo access - and then it will create a debootstrap. It will fetch the necessary packages from the Chromium OS Package Management.

##### NOTE

If something fails you will need to do

```sh
rm -rf ~/chromiumos/repo
```

and then rerun the `./make_local_repo.sh` script again.

#### Creating the build environment

All we need to do is run the following command:

```sh
./make_chroot.sh
```

The script will check if all the dependencies are satisfied, and if something is missing it will pull the necessary files and compile them as necessary. Although I did not encounter any problems, the documentation states that the <em>/etc/apt/sources.list</em> is used for retrieving the packages. If your setup is pulling packages from somewhere else then you may need to get the most recent packages from the repository. You can do that by running:


```sh
./make_chroot.sh --mirror=http://build.chromium.org/buildbot/packages --suite=chromeos_dev
```

#### Building Chromium OS

I need to build Chromium first (since I chose to download it too). This is necessary since your build will fail if you try it the other way around :)

```sh
./build_chrome.sh --chrome_dir ~/chromium
```

##### Enter the chroot build environment

Run the following command gets us back in the chroot environment (you will be asked for your password)

```sh
./enter_chroot.sh
```

##### Set the shared user password

This is a one-off step for those of us that want to be able to sudo from a terminal. I am setting the shared user password running the following script:

```sh
./set_shared_user_password.sh
```

This will prompt for the password and the output will be stored in the `./shared_user_password.txt` file. Don't worry the password is encrypted so if you do not have anyone watching over your shoulder while typing your password you are OK. Just to be on the safe side, clear the screen.

```sh
clear
```

##### Build the platform packages

In the chroot environment run

```sh
./build_platform_packages.sh
```

Unfortunately I hit a snag :( The `build_platform_packages` script produced an error:

```sh
Checking for latest build of Chrome
Downloading http://chrome-web/buildbot/snapshots/chromium-rel-linux-chromiumos/LATEST
--2009-11-24 19:44:49--  http://chrome-web/buildbot/snapshots/chromium-rel-linux-chromiumos/LATEST
Resolving chrome-web... failed: Name or service not known.
wget: unable to resolve host address `chrome-web'
make: *** [build-stamp] Error 1
```

I quickly found what I need to do (Google is your friend :)). It appears that this is [known bug](http://codereview.chromium.org/414029/show) and it is easily fixable. All I had to do is edit the `copy_chrome_zip.sh` file. I tried using nano in the chroot environment but it was not there. For that I exited the chroot and edited the file.

```sh
exit
nano -w ~/chromiumos/src/platform/chrome/copy_chrome_zip.sh
```

Locate the line with the `BASE_URL` variable and change `chrome-web` to `build.chromium.org` and save the file. After that enter again the chroot and rerun the `build_platform_packages.sh` script.

```sh
./enter_chroot.sh
./build_platform_packages.sh</pre>
```

Quite a bit later the script execution ended with **All packages built** :)

##### Build the kernel

In the chroot environment run

```sh
./build_kernel.sh
```

A bit later I am looking at this message and grinning :)

```sh
Kernel build successful, check /home/ndimopoulos/trunk/src/build/x86/local_packages/linux-image-2.6.30-chromeos-intel-menlow_002_i386.deb
```

##### Build the image

In the chroot environment run

```sh
./build_image.sh
```

The script starts with validations, configurations, unpacking and compilations - all too fast for my eye to capture.

The script finished compiling and there are warnings and errors :(. They all have to do with the disk geometry and *partition 1 extends past the end of the disk* /shrug again....

In the end I get this on the screen which hopefully is OK...

```sh
Re-reading the partition table ...
BLKRRPART: Inappropriate ioctl for device

If you created or changed a DOS partition, /dev/foo7, say, then use dd(1)
to zero the first 512 bytes:  dd if=/dev/zero of=/dev/foo7 bs=512 count=1
(See fdisk(8).)
Done.  Image created in /home/ndimopoulos/trunk/src/build/images/999.999.32809.203441-a1
To copy to USB keyfob, outside the chroot, do something like:
  ./image_to_usb.sh --from=/usr/local/chromiumos/chromiumos.git/src/build/images/999.999.32809.203441-a1 --to=/dev/sdb
To convert to VMWare image, outside the chroot, do something like:
  ./image_to_vmware.sh --from=/usr/local/chromiumos/chromiumos.git/src/build/images/999.999.32809.203441-a1
```

Continued in [Part III](/post/chromium-os-part-iii)
