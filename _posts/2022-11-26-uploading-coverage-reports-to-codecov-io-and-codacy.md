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
C﻿ode coverage is a very informative feature of testing. IFor PHP, PHPUnit offers functionality to run tests as well as gather coverage statistics. 

<﻿!--more-->

C﻿ode coverage shows a report of all the code that has been executed and how many times. This way a developer can write additional tests, to cover different paths of the application. Having high code coverage in an application offers a sense of security that the application will be relatively bug free. However, one can fall in the trap of *only* checking the code coverage and not following DRY or SOLID coding techniques, which will streamline and simplify the said application.

F﻿or Phalcon, we have been using Codeception for a long time now, and have been utilizing the library's ability to run the tests and also produce code coverage reports.

The problem we have is that Phalcon is an extension and as such, code coverage reports are not available as one would expect. There is a way to collect this data and push it to Codecov (showing coverage for C files) but that has been quite cumbersome to setup and use.

![Codacy Code Quality](/assets/files/20221126-codacy-quality.svg)

![Codacy Code Coverage](/assets/files/20221126-codacy-coverage.svg)

S﻿ince I am also working on Phalcon v6, which is a pure PHP implementation, I have enabled Code coverage for the project and so far we are in a good path. The code is mostly rated at "A" and the code coverage is at 81%. When time allows, I will work more to increase the code coverage.

