---
layout: post
title: Composer cache
date: 2020-09-18T16:21:40.485Z
tags:
  - php
  - composer
  - cache
image: '/assets/files/php.png'
image-alt: PHP
---
When working with repositories that utilize composer packages, sometimes you might get into a situation, where you expect a package to be present, but a different version is what exists in your `vendor` folder. Even if you run 

```php
composer update
```

things still do not update.

Composer implements a cache, where packages are stored for a period of time, so that any `install`, `update` etc. commands do not download packages that have already been downloaded. It speeds things up and saves on bandwidth.

To clear the composer cache, all you have to do is run:

```php
composer clearcache
```
It has only happened to me once, but you might encounter the same problem yourself. 

Clearing the cache ensures that you are downloading the packages that are listed in your `composer.json` file.
