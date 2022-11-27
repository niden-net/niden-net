---
layout: post
title: Git pre-commit - Another check to ensure clean code
date: 2011-11-13T23:45:00.000Z
tags:
  - git
  - how-to
image: '/assets/files/git-logo.jpg'
image-alt: Git
---
Throughout my career I have been using various [revision control systems](https://en.wikipedia.org/wiki/Revision_control). I started off with Visual SourceSafe which I thought at the time was great, primarily because of the small size of our team and the ease of use though the Visual Basic's IDE.

Later on I switched to SVN which is a great version control system and it fulfilled all my needs for proper version control.

When I moved to the US and started working here, I introduced SVN to the company that I was working at the time. We adopted the technique of having one branch per project, since we were working on different projects at any given time. Using the plugin through Eclipse or in Windows the integrated Explorer plugin, I was checking in code, switching through projects etc.

[Recently](/posts/new-beginnings-sleep6566400) I changed jobs and although we did use Subversion for a few months, we did switch to Git. The reasons behind it are too many to count - and I will probably go through them in another blog post.

One really good feature that Git has is its hooks. Although Subversion also supports hooks, I have only been exposed to the ones from Git and have used them - hence this blog post.

Git comes with some predefined hooks that one can use as a starting point. The code is checked before it is being committed and if it does pass whatever the `pre-commit` (for instance) hook does, it will allow you to commit; alternatively it will stop until you correct the mistakes made.

One very popular `pre-commit` hook is the one [here](https://github.com/ReekenX) by Remigijus Jarmalavičius. It checkes the files that have been modified/added and runs the `php -l` on it to ensure that whatever will be committed does not have PHP syntax errors.

I have downloaded that code and easily added it to my repository so everything is being checked prior to any of my commits.

In our MemberFuse&trade; platform, we have - like any other developer - many helper functions that are used solely for development. One of the mostly used one is the `vdd($message);`. What this function does is a `var_dump` of the `$message` variable, echoes out the file that it was called as well as the line it is in, and dies. It is great for quick and dirty debugging and inspecting a variable as it passes through the code. Granted it is not very TDD but I am sure that all developers have little things like these to aid them with their daily programming and debugging.

From time to time, as we fix bugs and explore certain behaviors that sometimes it is difficult to assess, we use this function and we output the data on the browser. However this is just a development/debugging function and **must never** be used (or enabled) in the production environment.

I have been the culprit of using the function, forgot that I had it in a certain part of the code, and committed that code in our development branch. That caused some colleagues to experience inconsistent behavior with errors showing up on the screen and time was wasted.

To combat this scenario, I wrote a `pre-commit` hook that will allow you to check for the existence of certain functions in the code and ensure that those functions (or strings for that matter since I am using [grep])https://en.wikipedia.org/wiki/Grep)) do not exist in what is to be committed.

The `pre-commit hook` that I wrote is listed below and the code has been heavily based on Remigijus Jarmalavičius's [pre-commit syntax checker](https://github.com/ReekenX).

```sh
#!/bin/bash
# Author: Nikolaos Dimopoulos <nikos niden.net="niden.net">
# Based on code by Remigijus Jarmalavičius <remigijus jarmalavicius.lt="jarmalavicius.lt"> 
# Checks the files to be committed for the presence of print_r(), 
# var_dump(), die()
# The array below can be extended for further checks

checks[1]="var_dump("
checks[2]="print_r("
checks[3]="die"

element_count=${#checks[@]}
let "element_count = $element_count + 1"

ROOT_DIR="$(pwd)/"
LIST=$(git status | grep -e '\#.*\(modified\|added\)')
ERRORS_BUFFER=""
for file in $LIST
do
    if [ "$file" == '#' ]; then
        continue
    fi
    if [ $(echo "$file" | grep 'modified') ]; then
        FILE_ACTION="modified"
    elif [ $(echo "$file" | grep 'added') ]; then
        FILE_ACTION="added"
    else 
        EXTENSION=$(echo "$file" | grep ".php$")
        if [ "$EXTENSION" != "" ]; then

            index=1
            while [ "$index" -lt "$element_count" ]
            do
                echo "Checking $FILE_ACTION file: $file [${checks[$index]}]" 
                ERRORS=$(grep "${checks[$index]}" $ROOT_DIR$file &gt;&amp;1)
                if [ "$ERRORS" != "" ]; then
                    if [ "$ERRORS_BUFFER" != "" ]; then
                        ERRORS_BUFFER="$ERRORS_BUFFER\n$ERRORS"
                    else
                        ERRORS_BUFFER="$ERRORS"
                    fi
                    echo "${checks[$index]} found in file: $file "
                fi
                let "index = $index + 1"
            done
        fi
    fi
done
if [ "$ERRORS_BUFFER" != "" ]; then
    echo 
    echo "These errors were found in try-to-commit files: "
    echo -e $ERRORS_BUFFER
    echo 
    echo "Can't commit, fix errors first."
    exit 1
else
    echo "Commited successfully."
fi
```

If you want to check for any kind of string, just add one extra line (or modify the existing ones) of the `checks` array. The code will loop through the array and `grep` the modified/added files for that entry. If it is found the commit will not be allowed and you will have to manually go and change whatever needs to be changed.

I hope you find this hook useful. You can download the file from my [github repository](https://github.com/niden/Git-Pre-Commit-Hook-for-certain-words).

**Note**: If you wish to run more than one pre-commit hooks, you don't need to merge them all in the same file. You can create a `pre-commit` file which will have the following contents:

```sh
#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

$DIR/pre-commit.l
$DIR/pre-commit.output
```

The `*.l` file is the one that runs the PHP syntax check, while the `*.output` one is the one mentioned in this blog post. You can extend the list to your liking and usage.

#### Update
* [For Drupal](https://github.com/geraldvillorente/drupal-pre-commit) by Gerald Vilorente 
