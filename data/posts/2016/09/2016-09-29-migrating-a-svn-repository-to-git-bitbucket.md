Migrating a SVN repository to Git (Bitbucket)
=============================================

<img class="post-image" src="{{ cdnUrl }}/files/2016-09-29-svn.png" />
<img class="post-image" src="{{ cdnUrl }}/files/2016-09-29-git.jpg" />
### Preface
This article explains how to migrate a SVN repository to Git. Although this guide uses [BitBucket](https://bitbucket.org) as the Git repository, you can easily adjust the steps to migrate to a different Git repository provider.

I was recently tasked to migrate a repository from SVN to Git ([BitBucket](https://bitbucket.org)). I have tried the the importer from BitBucket but it failed due to corruption in our SVN repository.
 
So I had no other alternative than to do things by hand. Below is the process I have used and some gotchas.

### Authors

SVN stores just a username with every commit. So `nikos` could be one and `Nikos` could be another user. Git however stores also the email of the user and to make things work perfectly we need to create an `authors.txt` file which contains the mapping between the SVN users and the Git users.

**NOTE** The `authors.txt` file is not necessary for the migration. It only helps for the mapping between your current users (in your Git installation).

The format of the file is simple:

```bash
captain = Captain America <cap@avengers.org>
```

If you have the file ready, skip the steps below. Alternatively you can generate the `authors.txt` file by running the following command in your SVN project folder: 

```bash
svn log -q | \
    awk -F '|' '/^r/ {sub("^ ", "", $2); sub(" $", "", $2); print $2" = "$2" <"$2">"}' | \
    sort -u > authors.txt
```

### Conventions

* The source SVN repository is called `SVNSOURCE`
* The target GIT repository is called `GITTARGET`
* The SVN URL is `https://svn.avengers.org/svn`

### Commands

Create a work folder and `cd` into it

```bash
mkdir source_repo
cd source_repo/
```

Initialize the Git repository and copy the authors file in it

```bash
git svn init https://svn.avengers.org/svn/SVNSOURCE/ --stdlayout 
cp ../authors.txt .
```

Set up the authors mapping file in the config

```bash
git config svn.authorsfile authors.txt
```

Check the config just in case

```bash
git config --local --list
```

The output should be something like this:

```bash
core.repositoryformatversion=0
core.filemode=true
core.bare=false
core.logallrefupdates=true
svn-remote.svn.url=https://svn.avengers.org/svn/SVNSOURCE
svn-remote.svn.fetch=trunk:refs/remotes/trunk
svn-remote.svn.branches=branches/*:refs/remotes/*
svn-remote.svn.tags=tags/*:refs/remotes/tags/*
svn.authorsfile=authors.txt
```

Get the data from SVN (rerun the command if there is a timeout or proxy error)

```bash
git svn fetch
```

Check the status of the repository and the branches

```bash
git status
git branch -a
```

Create the new bare work folder

```bash
cd ..
mkdir source_bare
cd source_bare/
```

Initialize the bare folder and map the trunk

```bash
git init --bare .
git symbolic-ref HEAD refs/heads/trunk
```

Return to the work folder

```bash
cd ..
cd source_repo/
```

Add the bare repo as the remote and push the data to it

```bash
git remote add bare ../source_bare/
git config remote.bare.push 'refs/remotes/*:refs/heads/*'
git push bare
```

Return to the bare work folder and check the branches

```bash
cd ..
cd source_bare/
git branch
```

Rename trunk to master

```bash
git branch -m trunk master
```

Note all the branches that are prefixed `/tags/` and modify the lines below (as many times as necessary) to convert SVN tags to Git tags

```bash
git tag 3.0.0 refs/heads/tags/3.0.0
...
git branch -D tags/3.0.0
...
```

Alternatively you can put the following in a script and run it:

```bash
git for-each-ref --format='%(refname)' refs/heads/tags | \
cut -d / -f 4 | \
while read ref
do
  git tag "$ref" "refs/heads/tags/$ref";
  git branch -D "tags/$ref";
done
```

Check the branches and the new tags

```bash
git br
git tags
```

Check the authors

```bash
git log
```

Push the repository to BitBucket

```bash
git push --mirror git@bitbucket.org:avengers/GITTARGET
```

Enjoy