---
layout: post
title: Chromium OS Part I
date: 2009-11-24T23:45:00.000Z
tags:
  - chromium
  - 'chromium os'
  - google
  - installation
  - linux
  - how-to
image: '/assets/files/2009-11-24-chromium-os.png'
image-alt: Chromium OS
---
A lot of hype has been generated on the Internet a few months back regarding Google's [announcement](https://googleblog.blogspot.com/2009/07/introducing-google-chrome-os.html) that they are building a new operating system. The announcement was met with skepticism but also enthusiasm/anticipation by a lot of people who are puzzled as to what direction Google is taking and where are they looking themselves positioned in the computer industry.

Google has already established themselves in the mobile operating system with [Android](https://www.android.com/) which for many is better than Apple's iPhone. What managed to get Google very high up in user satisfaction with Android was the fact that it is open source. It is backed by Google and they are putting a lot of effort into this but the fact that anyone can download the source code and build the operating system themselves is amazing. Giving people the freedom to choose should be something that every company should do (personal opinion).

On Friday I watched the Chromium OS Webcast. A lot of people have been waiting for a presentation from Google of the new operating system. Google provided just that but with a small twist. The presenters outlined the features of the new operating system: Fast, Fast, Fast.

Although the presenters clearly stated that they will not provide any links to hardware, what is supported, where Chromium OS runs etc. they made sure to address one of the core features of Chromium OS. **It is Open Sourced**! That alone gives people again freedom. Freedom to choose what they want to have as their operating system in their computer.

The website for the new operating system is [located here](https://dev.chromium.org/chromium-os) and there are instructions on how you can [build](https://dev.chromium.org/chromium-os) this new OS even in its current state - which is not production ready.

Curious (as usual) I tried installing the Chromium OS on a virtual machine. My experience installing the new OS and other comments is outlined below:

#### Prerequisites

My DELL Studio 17 has two hard drives and because I really really do not like Windows Vista, I have installed Ubuntu 9.10 32bit on the second partition. The notebook enjoys a 2.4GHz Intel processor and 6GB RAM.

I applied all the relevant updates (just to be on the safe side) and a couple of reboots later I am ready to venture in the unknown. The documentation outlines the minimum required packages:&nbsp;

Building on Linux requires the following software.

- Python >= 2.4
- Perl >= 5.x
- gcc/g++ >= 4.2
- g++-multilib >=4.2
- bison >= 2.3
- flex >= 2.5.34
- gperf >= 3.0.4
- pkg-config >= 0.20
- libnss3-dev >= 3.12
- libasound2-dev
- libgconf2-dev
- libglib2.0-dev
- libgtk2.0-dev
- libnspr4-0d >= 4.7.1+1.9-0ubuntu0.8.04.5 (ubuntu0.8.04.1 causes duplicate dtoa references)
- libnspr4-dev >= 4.7.1+1.9-0ubuntu0.8.04.5
- msttcorefonts (Microsoft fonts)
- freetype-dev
- libcairo2-dev
- libdbus-1-dev

Optional (currently, all of these are only used by layout tests):

- wdiff
- lighttpd
- php5-cgi
- sun-java6-fonts (needed for Lucida)

Because I didn't want to go and check the version of every package mentioned above I run the following command (mentioned also in the documentation)

```sh
sudo apt-get install subversion pkg-config python perl g++ g++-multilib bison flex gperf libnss3-dev libgtk2.0-dev libnspr4-0d libasound2-dev libnspr4-dev msttcorefonts libgconf2-dev libcairo2-dev libdbus-1-dev
```

And then the optional extras:

```sh
sudo apt-get install wdiff lighttpd php5-cgi sun-java6-fonts
```

A few minutes later the necessary packages had been downloaded and installed. One step closer to my goal :)

#### Getting the code

Now that all the prerequisites have been satisfied. I need to get the code!

Navigating to [this link](https://dev.chromium.org/chromium-os) in the chromium.org wiki, I get all the instructions on how to get the code. There are some prerequisites there (i.e. your system needs to be able to uncompress tar files) but nothing out of the ordinary.

I installed the [depot_tools](https://dev.chromium.org/chromium-os) which was really a two step process - svn checkout the tools and add the tools path in the current path. After that I installed the `git-core`:

```sh
sudo apt-get install git-core
```

I will pull the source code from the SVN repository. I can just as easy download the tarball and unzip it. The instructions in the chromium.org wiki explain both options.

I am ready to get the source code for the Chromium OS package. You can get the code with the Chromium browser or without it. I am opting to get it with the Chromium browser. The following commands retrieve the necessary files for the OS as well as dependencies for the browser:

```sh
mkdir ~/chroomiumos
cd ~/chromiumos
gclient config https://src.chromium.org/git/chromiumos.git
gclient sync --deps="unix,chromeos" --force
```

The download takes a bit of time since there are a lot of files that we need to retrieve. In situations like these, browsing, reading a book, going to get something to eat or working on the other computer are some of the activities you can engage yourself in so as to kill time. That is of course if you do not have a T1 or FIOS at which point this step will be finished by the time you read this sentence :) (/sigh I miss FIOS).

I open another terminal window in order to retrieve the Chromium source code (the browser now).

```sh
mkdir ~/chromium
cd ~/chromium
gclient config https://src.chromium.org/svn/trunk/src
gclient sync
```

and the wait continues....

Continued in [Part II](/post/chromium-os-part-ii)
