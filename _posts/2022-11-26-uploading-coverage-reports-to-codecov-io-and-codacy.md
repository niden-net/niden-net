---
layout: post
title: Uploading coverage reports to Codecov.io and Codacy
date: 2022-11-26T21:07:57.806Z
tags:
  - php
  - codecov
  - codacy
  - testing
---
C﻿ode coverage is a very informative feature of testing. For PHP, PHPUnit offers functionality to run tests as well as gather coverage statistics.
<﻿!--more-->

F﻿or Phalcon, we have been using Codeception for a long time now, and have been utilizing the library's ability to run the tests and also produce code coverage reports.

The problem we have is that Phalcon is an extension and as such, code coverage reports are not available as one would expect. There is a way to collect this data and push it to Codecov (showing coverage for C files) but that has been quite cumbersome to setup and use.

S﻿ince I am also working on Phalcon v6, which is going to be a 