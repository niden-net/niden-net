---
layout: post
title: LibreOffice Auto Update
date: 2011-04-21T23:45:00.000Z
tags:
  - libreoffice
  - oracle
  - openoffice
  - ubuntu
  - how-to
image: '/assets/files/2011-04-21-libreoffice.png'
image-alt: LibreOffice
---
We all knew it was coming. Ever since [Oracle](https://www.oracle.com/) took an astounding turn towards the non-community orientation of [OpenOffice](https://www.openoffice.org/), a lot of users on the Internet were curious (and concerned) as to what will happen with the whole OpenOffice saga.

Thankfully the project was forked and [LibreOffice](https://www.libreoffice.org/) was born. Within a few months the same (and better) quality product was released for the community. As the project entered its alpha stage, then the beta stage and then the RC stages, I was waiting anxiously to install it on my notebook (which is running [Ubuntu](https://www.ubuntu.com/) 10.10 at the moment).

A couple of months ago, I was finally happy with the package and the release, so I downloaded it on my notebook and started the installation. I don't have a problem with getting my hands dirty and working with the command prompt, but I am lazy - so I want my packages to update easily (say though the package manager). At the time I could not use the package manager, so it was all terminal work :)

The installation was pretty easy using the `dpkg` command. I run literally two commands and the application was installed :)

There was a recent announcement about OpenOffice from Oracle. I posted a link at [HackerNews](https://news.ycombinator.com/item?id=2451079) for an article I read in MarketWire, regarding this *"Oracle to move OpenOffice.org to a Community-Based Project"*. Unfortunately the link does not work any more but you might be able to read a bit about the subject [here](https://www.pcworld.com/businesscenter/article/225459/oracles_openoffice_move_may_be_too_little_too_late.html). In short:

> OpenOffice is dead, long live LibreOffice

Time for me to start getting serious about updating LibreOffice.

First of all I wanted to make sure that I did not have any cruft remaining from OpenOffice on my system.

```sh
sudo apt-get remove openoffice*.*
```

Surprisingly there were some packages that were still there.

The second step was to uninstall the current version of LibreOffice

```sh
sudo apt-get remove libreoffice*.*
```

And now comes the easy part. I am going to use a PPA, to ensure that I will get notified about pending updates and upgrade easily. To do so all I had to do was run the following commands on a terminal window:

```sh
sudo add-apt-repository ppa:libreoffice/ppa
```

Output:

```sh
Executing: gpg --ignore-time-conflict --no-options --no-default-keyring
--secret-keyring /etc/apt/secring.gpg --trustdb-name /etc/apt/trustdb.gpg
--keyring /etc/apt/trusted.gpg --primary-keyring /etc/apt/trusted.gpg
--keyserver keyserver.ubuntu.com --recv 36E81C9267FD1383FCC4490983FBA1751378B444gpg:
requesting key 1378B444 from hkp server keyserver.ubuntu.com
gpg: key 1378B444: public key "Launchpad PPA for LibreOffice Packaging" imported
gpg: no ultimately trusted keys found
gpg: Total number processed: 1
gpg:               imported: 1  (RSA: 1)
```

Then update the packages again:

```sh
sudo apt-get update
```

And now install LibreOffice:

```sh
sudo apt-get install libreoffice
sudo apt-get install libreoffice-gnome
```

Note: KUbuntu users will need to run

```sh
sudo apt-get install libreoffice-kde
```

For those that prefer the graphical interface, all you will have to do is add `ppa:libreoffice/ppa` to your software sources.

Enjoy your LibreOffice installation!
