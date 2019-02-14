---
layout: post
title: Every microsecond counts!
date: 2015-10-02T23:45:00.000Z
tags:
  - php
  - performance
  - phalcon
  - metrics
image: '/assets/files/2015-10-02-clock.png'
---
One of the primary factors that always needs to be taken into consideration when designing and implementing an application is performance. In this day and age of information overload, everything is about speed. If your website is slow (more than a second or two) to appear on the browser, most likely your visitors will leave and go elsewhere. If the application you designed/implemented is slow, it will use more resources (memory/cpu) and thus cost more money to run. Time is money.

#### The Nanosecond

I have the highest admiration for [Grace Hopper](https://en.wikipedia.org/wiki/Grace_Hopper), a pioneer computer scientist who invented the first compiler for a computer language and paved the way for the evolution of programming languages in general. In a [video](https://www.youtube.com/watch?v=9eyFDBPk4Yw) she describes the value of a nanosecond in computer terms. I encourage you to click the link and watch it, it might give you a better perspective on why your application must be as fast as possible.

#### This Blog

As I wrote in a previous [post](/post/new-look-more-posts), a new version of this blog has been launched, based on work done for for the [Phalcon](https://phalconphp.com) [blog](https://github.com/phalcon/blog). While in development mode, metrics that are printed in the logger. The output looks something like this:

```sh
[Fri, 02 Oct 15 20:10:27 -0400][INFO] Shutdown completed [2.798s] - [4,134.16 KB] 
[Fri, 02 Oct 15 20:11:01 -0400][INFO] Shutdown completed [2.979s] - [4,134.00 KB] 
[Fri, 02 Oct 15 20:14:43 -0400][INFO] Shutdown completed [2.891s] - [4,142.60 KB] 
[Fri, 02 Oct 15 20:26:10 -0400][INFO] Shutdown completed [2.721s] - [1,075.02 KB] 
[Fri, 02 Oct 15 20:30:16 -0400][INFO] Shutdown completed [2.735s] - [1,002.25 KB] 
[Fri, 02 Oct 15 20:30:29 -0400][INFO] Shutdown completed [2.708s] - [1,002.29 KB] 
[Fri, 02 Oct 15 20:54:04 -0400][INFO] Shutdown completed [2.674s] - [1,003.43 KB] 
[Fri, 02 Oct 15 20:55:28 -0400][INFO] Shutdown completed [2.677s] - [1,003.31 KB] 
[Fri, 02 Oct 15 21:12:33 -0400][INFO] Shutdown completed [2.013s] - [913.81 KB] 
[Fri, 02 Oct 15 21:14:11 -0400][INFO] Shutdown completed [2.002s] - [895.35 KB] 
[Fri, 02 Oct 15 21:32:48 -0400][INFO] Shutdown completed [2.054s] - [894.71 KB] 
[Fri, 02 Oct 15 21:39:02 -0400][INFO] Shutdown completed [2.028s] - [894.04 KB]
[Fri, 02 Oct 15 21:44:19 -0400][INFO] Shutdown completed [2.046s] - [895.59 KB]
[Fri, 02 Oct 15 21:45:55 -0400][INFO] Shutdown completed [2.023s] - [893.75 KB]
```

As you can see there is room for improvement. Granted these results come from my local installation, where the `debugMode` is set to `1`, which means that there is no caching and everything gets recalculated on every request. Still, if I can make this local installation perform as fast as possible, then on the production server it will be even faster.

The first few lines show a relatively OK response (2.7-3.0 seconds) but a high usage in memory. This had to be rectified and looking at the code, I managed to refactor the [`PostFinder`](https://github.com/niden/niden-net/blob/0e0279a4f244d38a78a499401c2e33ad3396fa75/library/Kitsune/PostFinder.php) class and reduce the memory consumption significantly. Removing objects and referenced objects in them made a huge difference. Arrays work just fine for my purposes.
 
 Additional [optimizations](https://github.com/niden/blog/commit/e907099e716aea7589f4572ff592d5d446b8ccd9) led to dropping the execution time to just above 2.0 seconds and the memory consumption below 1Mb. 
 
 There are still a lot of [things](/post/fast-serialization-of-data-in-php) I can try (and will) both on the application level as well as the server level. I am aiming to reduce the execution time to below 1 second and I will for sure share the results and the tools/techniques used. 

I hope you enjoy the performance increase (however noticeable). More to come in the near future.

#### References

* [Phalcon Blog Github](https://github.com/phalcon/blog)
* [This Blog Github](https://github.com/niden/blog)