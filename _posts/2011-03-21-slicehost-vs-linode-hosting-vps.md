---
layout: post
title: Slicehost vs. Linode
date: 2011-03-21T23:45:00.000Z
tags:
  - gentoo
  - hosting
  - information
  - linux
  - vps
---
Through the years I have hosted my sites on various hosting companies. I had the really good experiences like [Vertexhost](http://www.vertexhost.com/) and really terrible ones - I don't remember the name of the host, but that kid, as it turned out to be later on, managed to lose 1.6GB of my data. You can safely say that I have been burned by bad hosting companies but also have enjoyed the services of good ones. In the case of [Vertexhost](http://www.vertexhost.com/), I part owned that company a few years back and I know that the current owner is a straight up guy and really cares for his customers.
<img class="post-image" src="/files/2011-03-21-vps.png" />

Since I moved my emails to [Google Apps](https://google.com/a/) I only need the hosting for my personal sites such as my blog, my wife's sites (burntoutmom.com, greekmommy.net) and a few other small sites.

I used to host those sites on one of my company's clusters. The bandwidth consumed was nothing to write home about (I think in total it was a couple of GB per month ~ 1.00 USD) so it didn't matter that I had them there. However, recent events forced me to move them out of that cluster. I was on the market for good and relatively cheap hosting. I did not want to purchase my own server or co-locate with someone else. My solution was going to be a VPS since I would be in control of what I install and what I need.

#### Slicehost

Without much thought, I signed up for [Slicehost](http://www.slicehost.com), which is a subsidiary of [Rackspace](http://www.rackspace.com), a very well known and reputable company.
<img class="post-image" src="/files/2011-03-21-slicehost.png" />

I got their 4GB package (250.00 USD per month) and installed <a href="http://www.gentoo.org/">Gentoo</a> on it. Apart from the price which was a bit steep, everything else was fine. I was happy to be able to host my sites in a configuration that I was comfortable with, under the understanding that if the VPS failed, then all my sites would go down. That however is the risk that everyone takes while hosting their sites on a single machine. The higher the availability and redundancy the higher the cost.

I must admit that signing up was not a very happy experience. I went and paid with my credit card, as they pro-rate your month based on your package. Almost immediately after signing up, came the email informing me that my credit card has been charged for the relevant amount. I got into the box through ssh, updated the `/etc/make.conf` file with the USE flags that I needed, run `emerge --sync` and then `emerge --update --deep --newuse --verbose world` so as to update the system.

It must have been around 5-10 minutes into the process that I received an email from [Slicehost](http://www.slicehost.com) saying that they are checking my account information and that I need to confirm my credit card details. I immediately replied to their email (gotta love the desktop notifications on GMail), with the information they needed.

After I sent the email, I noticed that the box was not responding. I tried to log back in and could not. I was also logged out (and could not log back in) to their management console on [Slicehost](http://www.slicehost.com) site. I was fuming! They severed the connection to the VPS in the middle of compilation to check my credit card information. I understand that they need to perform checks for fraud but two questions came to mind:

* Why did they have to sever the connection and not just send an email, and if I did not reply, just block access to the box? That would have been a heck of a lot of an inconvenience to myself i.e. the end user.
* Why did the initial email say that my credit card has been charged and it had not?

No more than 10 minutes later the whole thing had been resolved. I received an email saying that *"everything is OK and your account has been restored"*, at which point I logged back in to redo the compilations. I also received emails from their support/billing team apologizing but stating that although the initial email states that they charge the credit card, they don't. It is something they need to correct because it pisses people (like me) off.

There was nothing wrong with my setup - everything was working perfectly but the price was really what was bothering me. I would be able to support the sites for a few months, but since literally none of them is making money (maybe a dollar here or there from my blog but that is about it), I would have to pay out of pocket for the hosting every month. I had to find a different solution that would be:

* cheaper than Slicehost
* flexible in terms of setup
* easy to use in terms of controlling your VPS

After a lot of research I ended up with two winners: [Linode](http://www.linode.com) and [Prgrm](http://www.prgmr.com/). I opted for Linode, because although it was quite a bit more expensive than Prgmr, it had the better console in handling your VPS. I will, however, try out Prgmr's services in the near future so as to assess how good they are. They definitely cannot be beat in price.

#### Linode

Setting up an account with Linode was very easy. I didn't have any of the mini-saga I had with Slicehost. The account was created right there and then, my credit card charged and I was up and running in no time. Immediately I could see a difference in price. Linode's package for 4GB or RAM is 90.00 USD cheaper (159.00 USD vs. 250.00 USD for Slicehost). For the same package, the price difference is huge.
<img class="post-image" src="/files/2011-03-21-linode.png" />

I started testing the network, creating my VPS in the Atlanta, GA datacenter (Linode offers a number of data centers for you to create your own). The functionality that was available to me was identical and in some cases superior to that of Slicehost. There are a lot more distributions to choose from, and you can partition your VPS the way you want it to name a couple.


Shifting through the [documentation](http://library.linode.com/), I saw a few topics regarding high availability websites. The articles described using [DRDB](http://www.drbd.org/), [nginX](http://nginx.org/), [heartbeat](http://www.linux-ha.org/wiki/Main_Page) and pacemaker etc. to keep your sites highly available. I was intrigued by the information and set off to create a load balancer using two VPSs and nginX. I have documented the process and this is another blog post that will come later on this week.

While experimenting with the load balancer (and it was Saturday evening) I had to add a new IP address to one of the VPS instances. At the time my account would not allow such a change and I had to contact support. I did and got a reply in less than 5 minutes. I was really impressed by this. Subsequent tickets were answered within the 5 minute time frame. Kudos to Linode support for their speed and professionalism.

#### Conclusion

For a lot cheaper, Linode offered the same thing that Slicehost did. Moving my sites from one VPS to another was a matter of changing my DNS records to point  to the new IP address.

I have been using Linode for a week and so far so good. The support is superb and the [documentation](http://library.linode.com/) is full of how-to's that allows me to experiment with anything I want to - and the prices are not going to break me.

#### Resources

* [Vertexhost](http://www.vertexhost.com/)
* [Slicehost](http://www.slicehost.com)
* [Rackspace](http://www.rackspace.com)
* [Gentoo](http://www.gentoo.org/)
* [Linode](http://www.linode.com)
* [Linode documentation](http://library.linode.com/)
