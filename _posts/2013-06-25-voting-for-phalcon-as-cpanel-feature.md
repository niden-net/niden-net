---
layout: post
title: Voting for Phalcon as a cPanel feature woes
tags: [php, phalcon, cPanel, rant]
---

Those that have been following my blog and posts on [Google+](https://google.com/+NikolaosDimopoulos-niden) know that for the last year or so I have been involved heavily in [PhalconPHP](http://www.phalconphp.com/), a C based PHP framework, which delivers its functionality as an extension loaded on the web server.
<img class="post-image" src="/files/phalcon-green.png" />

I was honored a few months ago when I became a member of the Phalcon [Team](http://www.phalconphp.com/team) and have since tried my best to evangelize Phalcon and to help as much as possible with scheduling NFRs for development, helping in the forum, unit tests, blog posts etc.

One of the most difficult issues that Phalcon is facing is helping developers installing it on their machines. This of course does not mean that it is really difficult to install. The installation is basically three commands (or if you are on a Windows box you just download the DLL, add the relevant directive in the php.ini and restart the web server).

However since a lot of people are using shared hosting, they do not have access to the command line where `sudo` or `su` are available so that that Phalcon can be installed. This is left to the hosting company and some are very reluctant to install anything at all. I used to own a hosting company and I can assure you that it is indeed a hassle if a handful of clients ask for a library or an installation that is not part of the "norm". You have to maintain it, you have to ensure that it will not interfere with other packages on the server or hinder other clients that reside on the same hosting box.

A few months ago I approached [cPanel](http://www.cpanel.net/) through their ticketing system, in an effort to make Phalcon an option for the extensions that can be installed and loaded through their EasyApache application. 

At the time I was granted a development account for the software so that I can try and create an installation script and also was prompted to go to [http://features.cpanel.net/](http://features.cpanel.net/) and open a new feature request regarding this (the request is [here](http://features.cpanel.net/responses/add-support-for-phalconphp-extension-apache-php)). The purpose of this exercise is for cPanel to get a feel of what features the community needs and address them. In my communications with them I received the following (emphasis mine):

> Our EasyApache has multiple locations to add includes, or post hooks to compile third party libraries/software during a build, or after a build has completed. That being said, the extensions that we ship to be configured are maintained by the PHP & PECL groups. To answer the question as far as integration on all servers running cPanel, **I would recommend creating a feature request for this, and allowing the community to vote for this**:
>
> [http://features.cpanel.net/](http://features.cpanel.net/)

And so I did. I created the feature request and we also advertised this in our community via our [forum](https://forum.phalconphp.com) and our [blog post](https://blog.phalconphp.com/post/help-the-community-to-make-phalcon-available-on-cpanel).

The [feature](http://features.cpanel.net/responses/add-support-for-phalconphp-extension-apache-php) received well over 180 votes, making it the third most requested feature in cPanel. Also if you follow the link, one of the engineers of cPanel requested additional information which was provided by myself and others.

As time went by, I visited the feature and requested an update but never got a reply back. More and more votes kept on coming in so it was really a waiting game at that time.

All of a sudden though a week ago, one of our users in the forum asked in our Forum why the feature request in cPanel has **only 7 votes**. This came to us as a surprise so I went and checked it out. Lo and behold the vote counter was at 7 and not at 180+. Assuming that this was some sort of a glitch, I opened a ticket with cPanel and inquired about this.

A short time later I received a reply from a Vice President of Operations stating (emphasis mine):

> Our feature request system was designed for features requests from **our customers** and in reviewing this feature it was determined that most (if not all of the votes) came from an outside source. **In an effort to validate this we polled a number of shared hosting providers that we work closely with and this was not a feature they wanted**.
>
> We traced most of the votes down to this single source:
>
> [https://blog.phalconphp.com/post/help-the-community-to-make-phalcon-available-on-cpanel](https://blog.phalconphp.com/post/help-the-community-to-make-phalcon-available-on-cpanel)
>
> **While cPanel wants feedback from the general community, our focus is to deliver new features that our Partners and customers are asking for**.
> 
> We always appreciate community support and will keep an eye on this feature. Decisions for new features are made by both reviewing this feature request system and talking with Partners and customers. **To date out of about 15 conversations, not a single Partner or customer told us this was something that was important to them and thus we adjusted the votes to what we felt was more in line with the community using our system**.

I have a couple of issues with the above reply.

* For starters there was **no communication and no warning** that would have indicated what was going to happen with our votes. cPanel decided on this on their own. They do have the right to do so, it's their software after all, but a little courtesy would have gone a long way.
* The definition of "*customers*" is different to me than it is to cPanel. As customers cPanel defines their *partners and hosting companies*, the ones that purchase their software. Myself on the other hand considers customers also the end user, me and you who visit a hosting company and purchase services from them. If we do not exist, then a hosting company and subsequently cPanel does not exist either.
* cPanel asked a number of their partners who have not heard of Phalcon before, and as such they acted based on that premise. What would have happened if their sample was a different one? Say they asked partner X instead of partner Y? The bias of statistics is based on the sample one chooses and at times (such as this one) it could lead to false results.

In my reply I pointed out the above, stating that a customer should effectively be the end user and if not, at least their voice should be heard. The reply that I got was as follows:

> Thanks for the reply and understanding. **In reviewing the votes that came in, most appeared to be brand advocates of Phalcon and it was very difficult to discern the legitimacy of the votes. Had the votes come from active users of forums.cpanel.net (where most of the users originate from) or we were able to relate them back to some sort of hosting entity, we would have left the votes.**
>
> From the outside looking in the votes just appeared to come from Phalcon users without ties to cPanel & WHM. What I would encourage you to do is the following:
>
> * Have your users ask their hosting provider about it.
> * Remove the blog links and tweets and allow the feature to grow it's own set of wheels.
>
> If this feature is truly in high demand from customers of cPanel, Inc. they will naturally vote for it.
>
> We do appreciate your support and ongoing efforts to get this in front of us. The massive amounts of votes and comments it received, put it on our developers radars and we will continue to monitor the situation.

So in essence, if we want to achieve our objective, i.e. get Phalcon as an available extension for cPanel, we need to advise the community (but be careful to *Remove the blog links and tweets and allow the feature to grow it's own set of wheels.*) to contact their hosting companies (that use cPanel) to in turn contact cPanel and request Phalcon to be included as an extension.

The fallacy of the above is that developers of Phalcon will not choose hosts that offer cPanel because they cannot install the application. If they cannot install the application they will not use hosts that offer cPanel, thus they cannot ask their hosts to include Phalcon as a cPanel extension, and so goes the chicken and egg situation.

I totally respect cPanel's decisions - I don't agree with them but I do respect them. It's their house their rules as they say. I am however saddened by the fact that we never got any communication or warning that our votes were removed. A bit of communication there would have definitely saved a lot of frustration at least for our community.

Concluding, if anyone has a cPanel hosting account and wants to see Phalcon available as an extension, feel free to contact your host and request it to be included as an available extension.

**2013-07-25 Update**: A great analogy and reply has been posted by [Andres](https://phalconphp.com/team) in the Phalcon [Forum](http://forum.phalconphp.com/discussion/488/what-happened-to-our-votes-for-including-phalcon-in-cpanel#C1988).
