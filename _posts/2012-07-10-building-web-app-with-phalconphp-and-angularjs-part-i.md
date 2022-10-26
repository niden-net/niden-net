---
layout: post
title: Building a web app with PhalconPHP and AngularJS Part I
date: 2012-07-10T23:45:00.000Z
tags:
  - php
  - phalcon
  - angularjs
  - how-to
image: '/assets/files/phalcon-logo.png'
image-alt: Phalcon
---
There are ample frameworks on the Internet, most free, that a programmer can use to build a web application. Two of these frameworks are [PhalconPHP](https://phalcon.io) and [AngularJS](https://angularjs.org).

I decided to use those two frameworks and build a simple application which will keep track of the Game Balls and Kick in the Balls awards of [Harry Hog Football](http://www.harryhogfootball.com/).

[Harry Hog Football](http://www.harryhogfootball.com/) is a podcast that has been going strong since 2005, created by Redskins fans for Redskins Fans. (for those that do not know, [Washington Redskins](https://www.redskins.com/) is a team on the [National Football League](https://www.nfl.com/) in the USA).

Every week during the regular season, Aaron, Josh and John create a podcast, where they discuss the recent game, the injuries, the cuts, the new signings and they offer their Game Balls to the best players of the week as well as the Kick in the Balls awards for the ones that (according to the podcasters) '*suck*'.

I therefore created an application to record all those game balls and kick in the balls awards, so that we can all see, who is the most valuable player and who is the least valuable player for the Redskins throughout the years (the term *valuable* is used loosely here).

As a starting point I used the [INVO](https://github.com/phalcon/invo) application that [PhalconPHP](https://phalcon.io) showcases as an easy application to get you started. I modified it significantly to address my needs, refactoring classes as much as possible to get the least amount of code with maximum usability.

After building the application, I listened to all the episodes I could find, and entered the game balls and kick in the balls in the database. The models use the Phalcon_Model_Base class to handle data, while the rest of the application is handled by the Phalcon_Controller (and view of course).

The data transfer between the application and the relevant sections is primarily handled by [AngularJS](https://angularjs.org), which is dominant in the view layer. [AngularJS](https://angularjs.org) controllers handle menu creation, breadcrumbx as well as displaying results on screen.

Twitter's [Bootstrap CSS](https://twitter.github.com/bootstrap/)> is used to put the final touches for the application.

In subsequent posts I will explain each layer in turn, starting with [PhalconPHP](https://phalcon.io) and continuing with [AngularJS](https://angularjs.org).

This of course is by no means the perfect implementation. It has been a fun project for me, working on it on my own free time. You are more than welcome to fork the project and make any modifications you need. For those that are interested in getting straight to the code, it is available on Github [here](https://github.com/niden/phalcon-angular-harryhogfootball).

NOTE: The Github repository contains code that works with nginx. If you are having problems with Apache, check the `public/index.php` - there is a note there for nginx (probably will need to remove it)

#### References

* [AngularJS main site](https://angularjs.org/)
* [AngularJS documentation](https://docs.angularjs.org/api)
* [AngularJS group](https://groups.google.com/forum/#!forum/angular)
* [AngularJS Github](https://github.com/angular)

* [Phalcon PHP main site](https://phalcon.io/)
* [Phalcon PHP documentation](https://docs.phalcon.io/)
* [Phalcon Forum](https://forum.phalcon.io)
* [Phalcon PHP Github](https://github.com/phalcon)
