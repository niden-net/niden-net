---
layout: post
title: Removing a git submodule
date: 2018-12-21T23:45:00.000Z
tags:
  - submodules
  - git
  - remove
image: '/assets/files/git-logo.jpg'
image-alt: Git
---
I have been using git for quite a while now and am very comfortable with the `bread and butter` commands such as `git clone`, `git commit`. `git pull`, etc. I love the command line so using some aliases cuts down on typing and speeds up my commit workflow. My motto is commit often and commit small :)

#### Git Submodules
One of the things that I had really no idea about was the git submodules. I knew of the use of them but never ventured into the maze that git submodules can be. According to the git [bible](https://git-scm.com/book/en/v2/Git-Tools-Submodules):

> Submodules allow you to keep a Git repository as a subdirectory of another Git repository. This lets you clone another repository into your project and keep your commits separate.

#### The project
So lately I have been toying with the idea to introduce the [Zephir documentation](https://github.com/phalcon/zephir-docs) repository as a submodule to the main repository. This would allow me to pull the code from the main repo easily. The reason for the two repositories if anyone is curious, is that we keep all the versions (as branches) in the `zephir-docs` repository and also have an intergration with the excellent translation platform [Crowdin](https://crowdin.com). They handle all the translations to various languages that Phalcon/Zephir contributors submit, and after that they issue pull requests through their integration, to our Github repository. This way we get the translated documents often and then need to just update our website.

In an effort to reduce maintenance and allow for a much faster deployment method, I have been experimenting with Jekyll and [Github Pages](https://pages.github.com). To achieve what I wanted I had to have a main site which would serve all the content and also have one submodule per version from the `zephir-docs` repository.

#### Messing up
I added the submodule using 

```sh
$ mkdir 0.11
$ cd 0.11
$ git submodule add -b 0.11 git@github.com:niden/zephir-docs.git .
```
Sadly (for me) I fat fingered the command and now I am left with an unusable submodule. 

#### Solution
So how about removing that. It's not as easy as one might think. With some experimentation and the use of [DuckDuckGo](https://duckduckgo.com) I managed to figure out the steps needed to get rid of the unwanted submodule. Note `0.11` is where my submodule lives.

##### `.gitmodules`
You will notice that a `.gitmodules` file is present in the root of your folder. Open it with your favorite editor (cough cough `nano` not `vim`) and you will see a section similar to this one:

```sh
[submodule "0.11"]
    path = 0.11
    url = git://github.com/niden/zephir-docs.git
```
Remove that section. If you have other submodules that you need to remove, remove those sections also. Save the file and exit the editor.

##### Stage the file
```sh
git add .gitmodules
```
This is important since the commands below will start issuing warnings if you do not.

##### `.git/config`
Open `.git/config` with your favorite editor and you will see a section similar to this one:

```sh
[submodule "0.11"]
    url = git://github.com/niden/zephir-docs.git
```
Remove it, save the file and exit the editor.

##### `git rm`
Type the following command in your terminal (at the root of your project):

```sh
$ git rm --cached 0.11 
```
Ensure that there is no trailing slash. Also if you haven't staged the `.gitmodules` file from the step above, it will complain again.

##### `rm -fR`
Remove the files from the local git reposityro
```sh
$ rm -rR .git/modules/0.11
```

##### `commit`
Commit the change (remember the staged `.gitmodules`):
```sh
$ git commit -m "Removed unwanted submodule"
```

Now you can remove the files of the submodule from your file system. 
```sh
$ rm -fR 0.11/
```

Enjoy!
