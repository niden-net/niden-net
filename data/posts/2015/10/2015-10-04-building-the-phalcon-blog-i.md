<img class="post-image" src="{{ cdnUrl }}/files/phalcon-green.png" />
This is the first of a series of posts, describing how we built the Phalcon Blog (and this one of course). The intention is to showcase some of the features of Phalcon and discuss the reasons behind implementing the code in such a way. I will amend this post with the links of the future posts once I post them.

These series will focus initially on the Phalcon blog ([Github](https://github.com/phalcon/blog)) and will then expand on this blog ([Github](https://github.com/niden/blog)). In the very near future all the features available in this blog will be available in the Phalcon one :)

As I mentioned in a [previous post](/post/new-look-more-posts), [Andres](https://phalconphp.com/en/team) and I were not 100% satisfied with [Tumblr](http://tumblr.com), the blogging platform that we have used for a few years for the purposes of the [Phalcon blog](https://blog.phalconphp.com). So we decided that it would not only be beneficial for us to build something of our own, but also for the community, since the software is [open sourced](https://github.com/phalcon/blog) and available for everyone to use.

#### Bootstrapping process

<img class="post-image" src="{{ cdnUrl }}/files/2015-10-04-bootstrap.png" />
In this post I am going to concentrate on [bootstrapping](https://en.wikipedia.org/wiki/Bootstrapping) the application. By bootstrapping I do not mean using the [Bootstrap](https://getbootstrap.com) open source library, despite the probably misleading image on the right. 

Bootstrapping is the class (in our case) that handles pretty much everything that our application needs to run prior to executing actions. This entails

* Conditional execution between the normal app and the CLI one
* Application Paths
* Configuration Files
* Loader (and composer autoloader) setup
* Error handling
* Routes
* Dispatcher
* Url
* Views
 * Main View
 * Simple View (for emails, RSS/Sitemap etc.)
* Cache
* Utils
* Post Finder class 

Some of the above components are also registered in the DI container for further use in the application.

#### Implementation

In other applications we have open sourced such as [Vokuro](https://github.com/phalcon/vokuro), we have pretty much always included a couple of files in our `index.php`; one for the loader and one for the services as demonstrated [here](https://github.com/phalcon/vokuro/blob/master/public/index.php).

```php
<?php

error_reporting(E_ALL);

try {
    /**
     * Define some useful constants
     */
    define('BASE_DIR', dirname(__DIR__));
    define('APP_DIR', BASE_DIR . '/app');
	/**
	 * Read the configuration
	 */
	$config = include APP_DIR . '/config/config.php';
	/**
	 * Read auto-loader
	 */
	include APP_DIR . '/config/loader.php';
	/**
	 * Read services
	 */
	include APP_DIR . '/config/services.php';
	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application($di);
	echo $application->handle()->getContent();
} catch (Exception $e) {
	echo $e->getMessage(), '<br>';
	echo nl2br(htmlentities($e->getTraceAsString()));
}
```

There is nothing wrong with the above approach. We did however consider the fact that the particular index.php file has 3 different file inclusions and if we wanted to tinker with the setup of the application we would have to open all three.

We opted for one file containing all of our services and application bootstrap. In addition to that, we altered the design so that later on we can add a CLI application without much effort and heavy refactoring.

##### Note
The CLI application has been implemented on this blog and will very soon be merged to the Phalcon repository. We will cover that functionality in a future post.

#### `index.php`

Having one file that performs all the necessary initialization tasks a.k.a. bootstrapping our application allows us to have a much smaller `index.php` file. *(comments removed to preserve space)*

```php
<?php

use \Phalcon\Di\FactoryDefault as PhDI;
use \Kitsune\Bootstrap;

error_reporting(E_ALL);

try {
    require_once '../library/Kitsune/Bootstrap.php';

    $di = new PhDI();
    $bootstrap = new Bootstrap();

    echo $bootstrap->run($di, []);
} catch (\Exception $e) {
    if ($di->has('logger')) {
        $logger = $di->getShared('logger');
        $logger->error($e->getMessage());
        $logger->error('<pre>' . $e->getTraceAsString() . '</pre>');
    }
}
```

We create a new bootstrap application and pass in it a DI container. For this part of the application the `FactoryDefault` DI container is used. However we will be able to inject a Phalcon CLI DI container for the CLI application we will discuss later on.

#### `Bootstrap.php`

Our [bootstrap class](https://github.com/phalcon/blog/blob/master/library/Kitsune/Bootstrap.php) contains all the code we need to run the application. It is a bit shy of 400 lines which according to [PHP Mess Detector](http://phpmd.org/) is not something we want to be doing because it increases complexity and if we are not careful it will create a *mess* :). We opted to ignore that rule and left the file as is because once we had everything working as we wanted, we were not going to be messing with that file again.

##### Constants
 
We use several constants throughout the application.
 
* `K_PATH` - the top folder path of our installation
* `K_CLI` - whether this is a CLI application or not
* `K_DEBUG` - whether we are in debug/development mode. In this mode all volt templates are being created at every request and cache is not used.
* `K_TESTS` - whether we are running the test suite or not *(test suite is not implemented yet)*

##### Configuration

The configuration is split into two files. The git tracked `base.php` (under `/var/config/`) contains an array of elements that are needed throughout the application, such as cache settings, routes etc. The `config.php` located in the same folder is installation dependent and is not tracked in git. You can override every element that exists in `base.php`.

##### Loader

The loader uses the namespaces defined in the `base.php` and `config.php`. Additionally the composer autoloader is included to offer functionality needed from the composer components we have.

##### Logger

The logger is set to create a log file every day (with the date as the prefix).

##### Error handler

We decided to have no errors thrown in the application even if those are `E_NOTICE`. A simple `isset()` in most cases is more than enough to ensure that there are no `E_NOTICE` errors thrown in our log. Any errors thrown in the log files slow our application down, even if the errors are suppressed using the `php.ini` directives. We also set the timezone to `US/Eastern` in that file. That particular piece could become configurable and stored in the `config.php`. Finally we specify a custom error handler, to offer verbosity in errors thrown as well as log metrics when in debug mode.

##### Routes

Our routes are stored in the `base.php`. Additional routes can be set in the `config.php`. The router is not initialized if this is a CLI application.

##### Dispatcher

The dispatcher is instantiated with a listener, attaching to the `beforeException` event of the dispatcher. A custom plugin `NotFoundPlugin` is used to send output to the 404 page. Using the plugin allows us to reuse it anywhere in the application. This implementation is very beneficial when developing multi module applications.

##### Views

The views are being initialized using Volt as the template engine. The main view is set up with the expected options. We also initialize the [View Simple](https://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_View_Simple.html) component (again using [Volt](https://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_View_Engine_Volt.html)), to be used in the `/sitemap` functionality as well as email templates *(future functionality)*.

##### Cache

The cache component is configured using the `config.php`. We can define the parameters in that file and thus use say the `File` cache for our local/development machine and a more advanced cache (`Memcached` for instance) for the production system.

##### Posts Finder

This is a class we came up with, which is used to give us an easy way to get information about a specific post, the tag cloud, the index page etc. It is utilizing cache a lot!

#### Conclusion

In the next post of these series we will take a look at the router and discuss what each route means to our application.

Comments are more than welcome. If you have any questions on the implementation, feel free to ask in the comments below.

#### References

* [Phalcon Blog Github](https://github.com/phalcon/blog)
* [This Blog Github](https://github.com/niden/blog)