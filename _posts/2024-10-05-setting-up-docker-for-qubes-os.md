---
layout: post
title: Setting up Docker for Qubes OS
date: 2024-10-05T08:39:18.200Z
tags:
  - docker
  - qubes-os
---
I have always wanted to have a machine that is reasonably secure. I am not talking NSA level security here, but secure enough so that I do not have any headaches with viruses, malware etc. On top of that, I wanted to reduce the information that advertisers and marketing companies have on me.

## Qubes-OS
The solution came with Qubes OS. I have been following the project for quite a while now and finally decided to give it a try. So far, very impressed and very satisfied with my system. Everything is isolated as it should and it is working as expected.

## Docker
One thing that troubled me though was how I was going to install docker on my `code` VM. Since I do rely on `docker` and `docker compose` for many projects, I wanted to have that available for my `code` VM. 

My initial attempts worked reasonable. I installed docker on my `code` VM, run my commands, and started coding as usual. However once my `code` VM restarted the changes were lost and that was a bummer.

I then went to install `docker` in the template that my `code` VM is based on (`debian-12-xfce`).

Success!

Everything worked perfectly fine and I am up and running.

## Isolation
Well, it did work, but had a small side effect. When I decided to create a new VM to do some different coding, the docker environments from my `code` VM interfered with my new one.

I had to find a way where although `docker` in installed in the template, any children VMs would have their docker images in the `/home` folder, where we would have persistence but also isloation.

The solution was quite simple. All I had to do is open a terminal to my template VM (`debian-12-xfce`) and create the file `/etc/docker/daemon.json` with the following contents:

```json
{
    "data-root": "/home/user/.docker-images",
    "group": "user"
}
```

With the above, any images in my `code` VM will be created under `/home/user/.docker-images` and reside there persistent. 

> NOTE: The reason for the `group` entry being `user`, is because we need to ensure that the `user` group owns docker's sock file. If not, then we will not have permissions to access it and docker will not work.
{:.alert .alert-info }

I hope this helps.
