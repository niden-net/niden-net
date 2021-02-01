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
I have been a core developer of [Phalcon PHP](https://phalcon.io) for many years and have been contributing to the project since 2012.

Last year (before v4 was released) we made a considerable effort to reduce the maintenance required for side or sample projects. This was documented in a [blog post](https://blog.phalcon.io/post/recent-repository-reorganization) back in January 2019.

In short, we archived repositories/projects that we no longer had time to maintain, and moved some of our projects to [Netlify](https://netlify.com), converting them to [Jekyll](https://jekyll.org) based ones. This reduced our maintenance tasks significantly and allowed for more time to be routed to developing the framework vs. maintaining applications and performing DevOps.

Recently we decided to stop maintaining the [Forum](https://forum.phalcon.io) and are using [GitHub Discussions](https://github.com/phalcon/phalcon/discussions) to achieve the same result. This allows us, as mentioned above, to not worry about servers, application maintenance, database maintenance and any other task required to keep the Forum running smoothly. Those that have used our forums and our [Discord](https://phalcon.io/discord) have seen the issues we had with beanstalk not updating the Discord channel numerous times, which resulted in 500x errors on the server.

By replacing the forum with GitHub Discussions, we were also faced with the fact that we have a wealth of information in the current forum, loads of questions and answers that are a valuable resource for the community. Therefore, we could not switch the forum off and be done with it. The forum needs to stay as is, in a read only mode, so that search engines can keep on indexing it and the community can have this resource available.

Although the current server is more than capable of serving the content as long as we need to, we needed to remove that dependency and potential maintenance of it from out task list. 

The solution? Netlify and Jekyll again. 

By creating a static website of the current forum, we will not only stop maintaining a web server, database etc. but we will keep what we have active for as long as we need to.

One option was to write a script that would query the database, create the `discussions` view for instance, save the resulting HTML and then continue through the pagination, saving each page in a different file. The script would have to do the same for every aspect of the forum such as activity, hot, unanswered, users etc.

An easier approach was to use `wget` and make a clone of the website with HTML files. The `wget` command used is:

```shell
wget -k -K -E -r -l 100 -p -N -F -nH http://forum.phalcon.io/
```

The options used are:
* `-k` : all links are converted to relative
* `-K` : do not make any conversions - keep original files
* `-E` : rename html files to `.html`
* `-r` : recursive (important)
* `-l 100` : recurse 100 levels deep (it should be enough)
* `-p` : download all assets for a page (css, js, images)
* `-N` : Time stamp on
* `-F` : Force reading inputs as HTML files
* `-nH` : Add all contents in the same directory

At the time of this writing, the command is still running and getting data from the current Forum site.

I took a sample of the output and started creating the skeleton of the Jekyll based site. I managed to split the header/footer and edited the `help` files/endpoints to be served by Jekyll. The result was very satisfactory and everything is kept as it should.

In the next few days, when the Forum is completely mirrored, I will clean up all the files from the header/footer sections, thus reducing the size of the actual site, and run some tests to ensure that it is working as expected.

If you are interested in mirroring a site, you are more than welcome to use the above `wget` command, adjusting it to your needs.

> **NOTE**: Please do not be _that guy_ that tries to index a site without the owner knowing about it. It is not nice!
{: .alert .alert-warning }