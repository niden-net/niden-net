## Variables in `php.ini`

<img class="post-image" src="{{ cdnUrl }}/files/php.png" />

In my workplace we have been using [Zend Framework](http://framework.zend.com/) for more than a year primarily as a glue framework. We have managed to integrate a lot of components from the framework to suit our needs and slowly we are moving towards the full MVC pattern.

In the meantime our own framework, or collection of code if you prefer, is slowly evolving with every new project. New classes are added while others are enhanced more to provide more flexible input/output. Add to that the continuous evolution of [Zend Framework](http://framework.zend.com/) and we are faced with at least one version of our own codebase per project.

Lately we have been following a more [Test Driven Development](/post/test-driven-development) approach, where we create tests for every component we create or alter. After all this is the purpose of [TDDevelopment](/post/test-driven-development) - to make sure that your code has 100% coverage and that it works!

The same thing applies when a new version of Zend Framework is released. We download the latest version on the development boxes and run our tests against it. If something has changed and/or is broken we just fix it and our framework is ready for the next project.

Following this approach, the bulk of our projects are running the latest code (Zend Framework and our own framework). There are however a number of projects that we need to run against an older version of Zend Framework. The reasons for not are *not enough money for updating*, *not enough time*, *it works so don't tinker with it* to name a few.

So how do we deal with the issue of having different versions of Zend Framework on our servers and including the correct version to the relevant project? The solution that we use is variables in `php.ini`.

For starters we have a network share mounted on all of our development servers (the ones that are in house). This share we call it /resources. In there we have all the needed Zend Framework versions i.e.:

```sh
gemini ~ # ls -la /resources/ZendFramework/
total 0
drwxrwxr-x 8 webuser webdev 192 Nov  5 05:48 .
drwxr-xr-x 7 webuser webdev 168 Jul 17 10:55 ..
drwxrwxr-x 3 webuser webdev  72 Jun 23 13:48 1.0.3
drwxrwxr-x 5 webuser webdev 216 Apr 21  2009 1.5.2
drwxrwxr-x 7 webuser webdev 280 Apr 21  2009 1.6.2
drwxrwxr-x 8 webuser webdev 304 Apr 21  2009 1.7.8
drwxrwxr-x 8 webuser webdev 296 Jun  8  2009 1.8.3
drwxrwxr-x 8 webuser webdev 296 Oct 26 12:52 1.9.5
```

Since this is a network share, all we do is update one location and all servers have access to the same pool of files.

We also modify the php.ini file of each server, adding the following directives at the end of the file:

```sh
[ZendFramework]
ZF_LATEST="/resources/ZendFramework/1.9.5/library"
ZF_1_9_5="/resources/ZendFramework/1.9.5/library"
ZF_1_8_3="/resources/ZendFramework/1.8.3/library"
ZF_1_7_8="/resources/ZendFramework/1.7.8/library"
ZF_1_6_2="/resources/ZendFramework/1.6.2/library"
ZF_1_5_2="/resources/ZendFramework/1.5.2/library"
ZF_1_0_3="/resources/ZendFramework/1.0.3/library"
```

Restarting apache gives us access to those variables using the `get_cfg_var` PHP function. So in our bootstrap file (called `init.php`) we have the following:

```php
$zfPath = get_cfg_var('ZF_1_9_5');

$root = dirname(__FILE__);

set_include_path(
    $zfPath           . PATH_SEPARATOR .
    $root    . '/lib' . PATH_SEPARATOR .
    $root             . PATH_SEPARATOR .
    get_include_path()
);
```

Changing to a different version of the Zend Framework can be as easy as changing the path of the `ZF_*` directive in the `php.ini` file (and restarting Apache) or by changing the variable we need to use in the `init.php` file of each project.

This approach allows us to keep our `include_path` free from any Zend Framework paths since the server has projects that use different versions of the Zend Framework as well as ours. It also helps us in testing, since we can easily check whether a particular project works as expected in a newer or older version of each framework.

We opted out from using constants inside `.htaccess` files, due to the fact that we have many projects and this would have meant that we had to change every `.htaccess` file, should we wanted to change the version of Zend Framework used.

[php.ini](http://php.net/manual/en/ini.core.php) has a lot of directives that can prove useful for the PHP developer. Extending those directives to each project or collection of projects can save you time and headaches.
