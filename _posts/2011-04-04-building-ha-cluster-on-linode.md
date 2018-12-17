---
layout: post
title: Building a HA Cluster on Linode
tags: [ha, hosting, how-to]
---

I have recently blogged about [Slicehost vs. Linode](/post/slicehost-vs-linode-hosting-vps) and my decision to move my sites to the latter. Since then I can safely say that I made the right choice about the move. [Linode](http://www.linode.com)'s support is phenomenal. There has never been a ticket unanswered more than 5 minutes and all the tickets have been resolved. Even when I asked about a configuration issue regarding Heartbeat, which was clearly not in the realm of support, the support engineers did look at my configuration and did identify the error area. That alone saved me hours of troubleshooting and trying to find where the error was.

#### The Task

Your website must be available - all the time. Why? Because if you have something to say (a blog, a journal, a rant page, a forum etc.) you need your audience to be able to read your material all the time. Discussion forums, websites with custom applications or services, even information based websites need to have as close to 100% uptime as possible.. How can this be achieved? The solution is a High Availability (or HA) cluster.</span>
<img class="post-image" src="/files/2011-04-04-ha.jpg" />

Linux has been used for HA tasks for many years. The [Linux-HA](http://www.linux-ha.org/wiki/Main_Page)'s wiki has a lot of valuable information as well as guides and resources that one can use to increase the redundancy of their websites. In addition to this, Google is your friend. There are numerous bloggers that have shared their experiences with the public on how to create HA resources. Finally, one can check Linode's [Library](http://library.linode.com/) - a set of guides to allow you to create HA clusters with your Linodes.

My task for the last few weeks has been to create a High Availability cluster of services to serve PHP and MySQL and also have the ability to grow infinitely (well close to that is). In the next few weeks I will post a series of blog posts outlining how to achieve a HA cluster for your sites.

#### Architecture

The cluster will be build using CentOS as the OS of choice. I have also experimented with Gentoo and Ubuntu. You can do everything I do here with Ubuntu if you wish. There are slight differences in certain commands and steps which the blog posts will not cover. As far as Gentoo is concerned, at the moment there is a block between Pacemaker and Heartbeat. Once that is resolved, I will try to redo the whole thing using Gentoo - as it is my OS of preference.

We will build two boxes to serve as load balancers. The boxes (or Linodes) will have Heartbeat, Pacemaker and nginx installed on them. A "floating" IP address will be used to move from one node to another in the case of a failure. Each Linode uses nginx as a proxy to forward all requests to a set of web servers using nginX's proxy functionality.

We will also build a web server. This again is a CentOS box running PHP and nginx. The web server will store the data locally and connect to the database cluster. Once the last part of this How-To is completed (creation of a HA NFS) then we will be able to add as many web servers as we need.

The next set of boxes form the database cluster. The setup is two servers with Heartbeat, Pacemaker and MySQL setup with a Master/Master replication. Again a "floating" IP address is used to move from one server to another in the case of failure.

The NFS is also a 2 box setup. Again a "floating" IP address is used to connect to the file system. [DRBD](http://www.drbd.org/) is installed on them to cater for the replication.

#### Administration

All Linodes are located in the same data center. At the moment there is no way to create Linodes in different data centers and implement the above mentioned setup in an effort to achieve geographical redundancy.&nbsp;The whole setup is using 9 Linodes the last one being used for administrative tasks (note in my count I used 2 boxes as web servers).

All Linodes have active iptables configurations. All nodes have been configured to work with 322 as the SSH port to avoid the novice hacker. Every port is blocked from communicating with the Internet apart from the ones needed for essential services. For instance the Load Balancers allow connections on ports 80 and 443 (http and https). However, they only communicate with each other on the Heartbeat port. Equally the web servers do not allow connections to their 80/443 ports to any machine other than the Load Balancers. The Database Servers allow connections only from the Web servers etc.

The administrative node resides on a different data center. It runs Nagios and it monitors all the nodes of our HA cluster. Naturally the iptables setup of each node is adjusted to allow connections from this particular node.


#### Conclusion

I hope that these blog posts will serve as a learning exercise/guide to those who want to delve in High Availability websites. The primary reason I built this cluster is to offer these services to customers that have busy sites that require maximum availability. I am currently setting up the final touches of the hosting service I am going to offer so stay tuned for the details.
