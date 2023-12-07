---
layout: post
title: PhpStorm cannot create scratch files
date: 2023-12-07T08:39:18.200Z
tags:
  - phpstorm
  - scratch
---
I have been using PhpStorm for a decade now I believe that it is the best IDE for PHP developers.

## TLDR
Linux: Check the permissions in `~/.config/JetBrains/PhpStorm2023.3/` for the `scratches` folder. (your PhpStorm version might be different so check the folder name also.
{:.alert .alert-info }

## Storytime
I have recently come across an error that took a bit to figure out. 

Whenever I would press `Ctrl+Shift+Alt Insert` in order to create a new scratch file, I would be presented with a message __could not create file scratch.php__. Clearly a permissions issue but where is the file created?

In my Linux station I first checked under `~/.local`

```sh
$ ls -la .local/share/
total 168
drwx------ 19 nikos nikos  4096 Dec  7 08:42 .
drwx------  4 nikos nikos  4096 Nov 29 13:18 ..
drwxr-xr-x  2 nikos nikos  4096 Nov 30 11:49 applications
...
drwxr-xr-x  3 nikos nikos  4096 Dec  7 08:34 JetBrains
...
drwx------  5 nikos nikos  4096 Dec  2 09:13 Trash
```
That is probably it, but checking all the files under the `JetBrains` folder showed no change in permissions or the owner.

After a bit of digging I opted to check the `~/.config` folder, which is usually the place where configuration files are stored.

```sh
$ ls -la .config/
total 108
drwx------ 22 nikos nikos 4096 Dec  7 08:23 .
drwx------ 30 nikos nikos 4096 Dec  7 08:34 ..
drwxr-xr-x  2 nikos nikos 4096 Nov 30 11:57 autostart
...
drwxr-xr-x  3 nikos nikos 4096 Nov 29 18:41 JetBrains
...
-rw-r--r--  1 nikos nikos   27 Nov 29 13:18 xdg-terminals.list
```

And there it is, the `JetBrains` folder. Looking a bit deeper:

```sh
$ ls -la .config/JetBrains/PhpStorm2023.3/
total 148
drwxr-xr-x 13 nikos nikos  4096 Dec  7 08:34 .
drwxr-xr-x  3 nikos nikos  4096 Nov 29 18:41 ..
-rw-r--r--  1 nikos nikos  3027 Dec  2 19:24 bundled_plugins.txt
drwxr-xr-x  2 nikos nikos  4096 Nov 29 18:48 codestyles
drwxr-xr-x  2 nikos nikos  4096 Nov 29 18:48 colors
-rw-r--r--  1 nikos nikos  1077 Nov 29 18:43 disabled_plugins.txt
-rw-r--r--  1 nikos nikos     0 Dec  5 10:42 disabled_update.txt
-rw-r--r--  1 nikos nikos    93 Dec  7 08:33 early-access-registry.txt
drwxr-xr-x  5 nikos nikos  4096 Nov 29 18:44 event-log-metadata
drwxr-xr-x  2 nikos nikos  4096 Nov 29 18:48 inspection
drwxr-xr-x  2 nikos nikos  4096 Nov 29 18:49 jdbc-drivers
-rw-r--r--  1 nikos nikos     6 Dec  7 08:34 .lock
drwxr-xr-x  2 nikos nikos  4096 Dec  7 08:42 options
-rw-r--r--  1 nikos nikos    10 Nov 29 18:42 phpstorm64.vmoptions
-rw-r--r--  1 nikos nikos 31076 Dec  2 19:23 phpstorm.key
-rw-r--r--  1 nikos nikos 31076 Nov 29 18:50 plugin_PCWMP.license
drwxr-xr-x  2 root  root   4096 Nov 30 21:41 scratches             <<<<<
drwxr-xr-x  8 nikos nikos  4096 Nov 29 18:43 settingsSync
drwxr-xr-x  2 nikos nikos  4096 Nov 29 18:41 ssl
drwxr-xr-x  2 nikos nikos  4096 Dec  5 09:06 tasks
-rw-r--r--  1 nikos nikos  6431 Dec  7 08:24 updatedBrokenPlugins.db
-rw-------  1 nikos nikos    36 Dec  5 09:06 user.web.token
drwxr-xr-x  2 nikos nikos  4096 Dec  7 08:42 workspace
```

After this, it was just a matter of setting the owner for the file:

```sh
sudo chown nikos:nikos -R ~/.config/JetBrains/PhpStorm2023.3/scratches
```

and now the scratches are working in PhpStorm.
