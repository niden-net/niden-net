---
layout: post
title: Making a static copy of a website
date: 2021-02-01T17:21:11.121Z
tags:
  - copy
  - static
  - website
  - wget
---
Sometimes, it is necessary to retrieve a full static copy of a website (hopefully one you own). There are tools that help you do this such as [httrack](https://www.httrack.com/).

<!--more-->
However, for Linux systems primarily, using the [wget](https://www.gnu.org/software/wget/manual/wget.html) command can achieve the same result.

Assuming I want to mirror the `https://forum.phalcon.io` site, the `wget` command used is:

```shell
wget -E -F -k -K -l 100 -N -nH -p -r -v http://forum.phalcon.io/
```

The options used are:
* `-E` : rename html files to `.html` (adjust extensions)
* `-F` : Force reading inputs as HTML files
* `-k` : Convert links to relative (local viewing)
* `-K` : Backup converted files
* `-l 100` : Recurse 100 levels deep (it should be enough)
* `-N` : Time stamp on
* `-nH` : Disable generation of host-prefixed directories.
* `-p` : Download all assets for a page (css, js, images)
* `-r` : Recursive (important)
* `-v` : Verbose

If you are interested in mirroring a site, you are more than welcome to use the above `wget` command, adjusting it to your needs.

> **NOTE**: Please do not be _that guy_ that tries to index a site without the owner knowing about it. It is not nice!
{: .alert .alert-warning }