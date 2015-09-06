## Downgrading PHPUnit from 3.6 to 3.5

<img class="post-image" src="{{ cdnUrl }}/files/2012-01-15-phpunit.png" />

Recently I had to rebuild my computer, and decided to install [Linux Mint](http://www.linuxmint.com/) 12 (Lisa), which is a very lean installation - for my taste that is.

Going through the whole process of reinstalling all the packages that I need or had, [PHPUnit](http://www.phpunit.de/) was one of them. Easy enough a couple commands did the trick

```sh
sudo apt-get install php-pear
sudo pear upgrade PEAR
sudo pear config-set auto_discover 1
sudo pear install pear.phpunit.de/PHPUnit
```

I wanted to run my tests after that, only to find an error in the execution:

```sh
PHP Fatal error: &nbsp;Call to undefined method PHPUnit_Util_Filter::addFileToFilter()
 in /home/www/project/library/PHPUnit/Framework.php on line 48
```

At first I thought that it was a path error, so I included the `/usr/share/php/PHPUnit` and others in the `php.ini` file but with no luck. With a little bit of Googling I found out that there have been some changes in the 3.6 version of PHPUnit and things don't work as they did before.

Effectively, 3.6 had some refactoring done and thus the line:

```php
PHPUnit_Util_Filter::addDirectoryToFilter("$dir/tests");
```

changed to

```php
PHP_CodeCoverage_Filter::getInstance()
        ->addDirectoryToBlacklist("$dir/tests");
```

Since I didn't want to change my whole test suite, I had to find a solution i.e. downgrade PHPUnit to 3.5.

Unfortunately specifying the version directly did not wok

```sh
sudo pear install phpunit/PHPUnit-3.5.15
```

since it would pull the latest version again and I would end up with the 3.6 files.

So I went one step further and installed specific versions of the relevant dependencies to satisfy the 3.5.15 version.

## Uninstallation of 3.6

```sh
pear uninstall phpunit/PHPUnit_Selenium
pear uninstall phpunit/DbUnit
pear uninstall phpunit/PHPUnit
pear uninstall phpunit/PHP_CodeCoverage
pear uninstall phpunit/PHP_Iterator
pear uninstall phpunit/PHPUnit_MockObject
pear uninstall phpunit/Text_Template
pear uninstall phpunit/PHP_Invoker
pear uninstall phpunit/PHP_Timer
pear uninstall phpunit/File_Iterator
pear uninstall pear.symfony-project.com/YAML
```

## Installation of 3.5.15

```sh
pear install pear.symfony-project.com/YAML-1.0.2
pear install phpunit/PHPUnit_Selenium-1.0.1
pear install phpunit/PHP_Timer-1.0.0
pear install phpunit/Text_Template-1.0.0
pear install phpunit/PHPUnit_MockObject-1.0.3
pear install phpunit/File_Iterator-1.2.3
pear install phpunit/PHP_CodeCoverage-1.0.2
pear install phpunit/DbUnit-1.0.0
pear install phpunit/PHPUnit-3.5.15
```

I hope you find the above useful :)

