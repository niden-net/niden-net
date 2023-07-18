---
layout: post
title: Fujitsu ScanSnap S1300 in Linux
date: 2022-12-18T16:18:29.078Z
tags:
  - fujitsu
  - scan
  - linux
  - s1300
---
Home scanners have not fully caught up with their drivers and support for Linux. For years, the Linux community had to rely on hacks and solutions offered by the open source community, so that scanners can work reliably on Linux environments. Luckily, more and more companies are offering Linux drivers for their scanners, but there is still work to be done.

<!--more-->

### Fujitsu ScanSnap S1300

We purchased the Fujitsu ScanSnap S1300 a decade ago (in 2011 actually), as a home scanner for our documents. The goal was to scan every document that we want to keep and store it in a home and cloud location for our records. This has worked really well for us, but it meant that we had to have one Windows machine so that the scanner can work. The ScanSnap suite of tools was (and still is) only available for Windows environments.

As the years passed, our scanner model has been discontinued, therefore there are no more bug fixes or updated files for the scanning suite, but that is not a concern, since the existing software works very well.

### Linux

Recently, I decided to bring the scanner to my office and connect it to my Linux workstation. There had to be a way to scan documents from it. 

After a lot of research, head scratching and playing around with [SANE (Scanner Access Now Easy)](http://sane-project.org), I was ready to give up, until I discovered a blog post by [Josh Archer](https://www.josharcher.uk/code/install-scansnap-s1300-drivers-linux/) regarding the matter. I have to thank him for sharing this information with all of us, since his solution was what made things work for me.

This post concentrates on what I did to make my scanner work. For further information, please visit [his post](https://www.josharcher.uk/code/install-scansnap-s1300-drivers-linux/).

### Drivers

Since we have the S1300 model, I needed to get the drivers installed. [Josh](https://www.josharcher.uk/code/install-scansnap-s1300-drivers-linux/) has the drivers in his blog post, and I am offering them here also just in case:

* [Fujitsu ScanSnap S300 Driver](/assets/files/300_0C00.nal)
* [Fujitsu ScanSnap S1100 Driver](/assets/files/1100_0A00.nal)
* [Fujitsu ScanSnap S1300 Driver](/assets/files/1300_0C26.nal)
* [Fujitsu ScanSnap S1300i Driver](/assets/files/1300i_0D12.nal)

> NOTE: I have only used the S1300 driver. I cannot confirm that the other drivers will work, since I only have the S1300 scanner
{: .alert .alert-info }

Assuming that you have downloaded the driver in your `/Downloads` folder, open a terminal and move the driver to the appropriate location

```shell
sudo mkdir -p /usr/share/sane/epjitsu
sudo cp -v ~/Downloads/1300_0C26.nal /usr/share/sane/epjitsu
```

### SANE

We need to install SANE now, and make sure our scanner is recognized.

```shell
sudo apt install sane sane-utils
```

After the installation is completed connect your scanner to a USB port on your machine and then connect the power to the scanner. You will notice that the blue light for the scan button is flashing. This is normal for now.

Check if the scanner has been identified in the system:

```shell
dmesg
```

A message similar to the one below should appear. If it does not, disconnect the scanner from the power, and reconnect it.

```shell
[ 2295.612614] usb 3-1: new high-speed USB device number 3 using xhci_hcd
[ 2295.765207] usb 3-1: New USB device found, idVendor=04c5, idProduct=11ed, bcdDevice= 1.00
[ 2295.765213] usb 3-1: New USB device strings: Mfr=1, Product=2, SerialNumber=0
[ 2295.765215] usb 3-1: Product: ScanSnap S1300
[ 2295.765217] usb 3-1: Manufacturer: FUJITSU
```

Now that the scanner is registered in the system, we need to check if SANE can find it.

Issue the following command in the terminal:

```shell
sudo sane-find-scanner
```

A message similar to the one below should appear. If not, then check your installation and you might need to reboot your computer. These are suggestions only, since mine was recognized. 

```shell
$ sudo sane-find-scanner

  # sane-find-scanner will now attempt to detect your scanner. If the
  # result is different from what you expected, first make sure your
  # scanner is powered up and properly connected to your computer.

  # No SCSI scanners found. If you expected something different, make sure that
  # you have loaded a kernel SCSI driver for your SCSI adapter.

found possible USB scanner (vendor=0x04c5 [FUJITSU], product=0x11ed [ScanSnap S1300]) at libusb:003:003
could not fetch string descriptor: Pipe error
could not fetch string descriptor: Pipe error
could not fetch string descriptor: Pipe error
  # Your USB scanner was (probably) detected. It may or may not be supported by
  # SANE. Try scanimage -L and read the backend's manpage.

  # Not checking for parallel port scanners.

  # Most Scanners connected to the parallel port or other proprietary ports
  # can't be detected by this program.
```

You can ignore any errors in the output. All you are interested in is the line (it will be slightly different in your system):

```shell
found possible USB scanner (vendor=0x04c5 [FUJITSU], product=0x11ed [ScanSnap S1300]) at libusb:003:003
```

### Configuration

Now we need to check the SANE configuration file. This is a sanity check, to ensure that the drivers are located in the correct places for SANE to load.

```shell
sudo xed /etc/sane.d/epjitsu.conf
```

> NOTE: You can also use `gedit` or if you are more comfortable in the terminal, you can use `nano` to check and edit (if need be) the file
{: .alert .alert-warning }

The lines I was looking at are:

```
# Fujitsu S1300
firmware /usr/share/sane/epjitsu/1300_0C26.nal
usb 0x04c5 0x11ed
```
If you notice the `usb` line above lists the same `0x04c5` (vendor) and `0x11ed` (product) as identified by the `sane-scan-finder` command above. If any of the information in the config file is incorrect, you will need to adjust it. Primarily we are looking for the path.

### Communication

All we need to do now is to communicate with the scanner. Issue the following command:

```shell
scanimage -L
```
It might take a few seconds, but you will hear a brief noise from the scanner and the blue light will no longer be flashing. The output should show:


```shell
$ scanimage -L
device `epjitsu:libusb:003:003' is a FUJITSU ScanSnap S1300 scanner
```

> NOTE: In my system, this worked just fine. You might need to reboot your system for the scanner drivers to be loaded properly.
{: .alert .alert-warning }

### Scanning

Open the scanning application (in my case `Document Scanner`). The application will search for scanners and a brief moment later will report your scanner at the bottom of the window. Put the documents in the scanner and click `Scan`. Voila!

### Conclusion

I did notice that the scanning application is not as powerful as the one offered for Windows environments. In the past, when I was using Google Drive, I could just scan any document and the ScanSnap application suite will send it directly to my drive. Additionally, the suite has scan to print, scan to folder and other handy features. 

The Linux application (Document Scanner) does not have all this functionality, which is perfectly fine with me. All I have to do is play a bit with the settings according to what I am scanning, and I will end up with a PDF in the end saved to where I need it to be.
