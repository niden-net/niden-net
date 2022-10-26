---
layout: post
title: Building the Phalcon Blog (i)
date: 2015-10-04T23:45:00.000Z
tags:
  - php
  - performance
  - phalcon
  - bootstrap
  - blog
  - series
image: '/assets/files/phalcon-logo.png'
image-alt: Phalcon
---
This is the first of a series of posts, describing how we built the Phalcon Blog (and this one of course). The intention is to showcase some of the features of Phalcon and discuss the reasons behind implementing the code in such a way. I will amend this post with the links of the future posts once I post them.

These series will focus initially on the Phalcon blog ([Github](https://github.com/phalcon/blog)) and will then expand on this blog ([Github](https://github.com/niden/blog)). In the very near future all the features available in this blog will be available in the Phalcon one :)

As I mentioned in a [previous post](/post/new-look-more-posts), [Andres](https://phalcon.io/en/team) and I were not 100% satisfied with [Tumblr](https://tumblr.com), the blogging platform that we have used for a few years for the purposes of the [Phalcon blog](https://blog.phalcon.io). So we decided that it would not only be beneficial for us to build something of our own, but also for the community, since the software is [open sourced](https://github.com/phalcon/blog) and available for everyone to use.

#### Bootstrapping process

In this post I am going to concentrate on [bootstrapping](https://en.wikipedia.org/wiki/Bootstrapping) the application. By bootstrapping I do not mean using the [Bootstrap](https://getbootstrap.com) open source library, despite the probably misleading image on the right. 
<img class="post-image" src="/assets/files/2015-10-04-bootstrap.png" />

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

*NOTE:* The CLI application has been implemented on this blog and will very soon be merged to the Phalcon repository. We will cover that functionality in a future post.

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

Our [bootstrap class]https://github.com/phalcon/blog/blob/v3.0.4p/app/library/AbstractBootstrap.php) contains all the code we need to run the application. It is a bit shy of 400 lines which according to [PHP Mess Detector](https://phpmd.org/) is not something we want to be doing because it increases complexity and if we are not careful it will create a *mess* :). We opted to ignore that rule and left the file as is because once we had everything working as we wanted, we were not going to be messing with that file again.

##### Constants
 
We use several constants throughout the application.
 
* `K_PATH` - the top folder path of our installation
* `K_CLI` - whether this is a CLI application or not
* `K_DEBUG` - whether we are in debug/development mode. In this mode all volt templates are being created at every request and cache is not used.
* `K_TESTS` - whether we are running the test suite or not *(test suite is not implemented yet)*


```php
        /**
         * The app path
         */
        if (!defined('K_PATH')) {
            define('K_PATH', dirname(dirname(dirname(__FILE__))));
        }
        
        ....
        
        /**
         * Check if this is a CLI app or not
         */
        $cli   = $utils->fetch($options, 'cli', false);
        if (!defined('K_CLI')) {
            define('K_CLI', $cli);
        }

        $tests = $utils->fetch($options, 'tests', false);
        if (!defined('K_TESTS')) {
            define('K_TESTS', $tests);
        }
        
        ....
        
        /**
         * Check if we are in debug/dev mode
         */
        if (!defined('K_DEBUG')) {
            $debugMode = boolval($utils->fetch($config, 'debugMode', false));
            define('K_DEBUG', $debugMode);
        }
```

##### Configuration

The configuration is split into two files. The git tracked `base.php` (under `/var/config/`) contains an array of elements that are needed throughout the application, such as cache settings, routes etc. The `config.php` located in the same folder is installation dependent and is not tracked in git. You can override every element that exists in `base.php`.

```php
        /**
         * The configuration is split into two different files. The first one
         * is the base configuration. The second one is machine/installation
         * specific.
         */
        if (!file_exists(K_PATH . '/var/config/base.php')) {
            throw new \Exception('Base configuration files are missing');
        }

        if (!file_exists(K_PATH . '/var/config/config.php')) {
            throw new \Exception('Configuration files are missing');
        }

        /**
         * Get the config files and merge them
         */
        $base     = require(K_PATH . '/var/config/base.php');
        $specific = require(K_PATH . '/var/config/config.php');
        $combined = array_replace_recursive($base, $specific);

        $config = new Config($combined);
        $di->set('config', $config, true);
```

##### Loader

The loader uses the namespaces defined in the `base.php` and `config.php`. Additionally the composer autoloader is included to offer functionality needed from the composer components we have.

```php
        /**
         * We're a registering a set of directories taken from the
         * configuration file
         */
        $loader = new Loader();
        $loader->registerNamespaces($config->namespaces->toArray());
        $loader->register();

        require K_PATH . '/vendor/autoload.php';
```

##### Logger

The logger is set to create a log file every day (with the date as the prefix).

```php
        /**
         * LOGGER
         *
         * The essential logging service
         */
        $format    = '[%date%][%type%] %message%';
        $name      = K_PATH . '/var/log/' . date('Y-m-d') . '-kitsune.log';
        $logger    = new LoggerFile($name);
        $formatter = new LoggerFormatter($format);
        $logger->setFormatter($formatter);
        $di->set('logger', $logger, true);
```

##### Error handler

We decided to have no errors thrown in the application even if those are `E_NOTICE`. A simple `isset()` in most cases is more than enough to ensure that there are no `E_NOTICE` errors thrown in our log. Any errors thrown in the log files slow our application down, even if the errors are suppressed using the `php.ini` directives. We also set the timezone to `US/Eastern` in that file. That particular piece could become configurable and stored in the `config.php`. Finally we specify a custom error handler, to offer verbosity in errors thrown as well as log metrics when in debug mode.

```php
        /**
         * ERROR HANDLING
         */
        ini_set('display_errors', boolval(K_DEBUG));

        error_reporting(E_ALL);

        set_error_handler(
            function ($exception) use ($logger) {
                if ($exception instanceof \Exception) {
                    $logger->error($exception->__toString());
                } else {
                    $logger->error(json_encode(debug_backtrace()));
                }
            }
        );

        set_exception_handler(
            function (\Exception $exception) use ($logger) {
                $logger->error($exception->getMessage());
            }
        );

        register_shutdown_function(
            function () use ($logger, $memoryUsage, $currentTime) {
                $memoryUsed = number_format(
                    (memory_get_usage() - $memoryUsage) / 1024,
                    3
                );
                $executionTime = number_format(
                    (microtime(true) - $currentTime),
                    4
                );
                if (K_DEBUG) {
                    $logger->info(
                        'Shutdown completed [Memory: ' . $memoryUsed . 'Kb] ' .
                        '[Execution: ' . $executionTime .']'
                    );
                }
            }
        );

        $timezone = $config->get('app_timezone', 'US/Eastern');
        date_default_timezone_set($timezone);
```

##### Routes

Our routes are stored in the `base.php`. Additional routes can be set in the `config.php`. The router is not initialized if this is a CLI application.

```php

        /**
         * Routes
         */
        if (!K_CLI) {
            $di->set(
                'router',
                function () use ($config) {
                    $router = new Router(false);
                    $router->removeExtraSlashes(true);
                    $routes = $config->routes->toArray();
                    foreach ($routes as $pattern => $options) {
                        $router->add($pattern, $options);
                    }

                    return $router;
                },
                true
            );
        }
```

##### Dispatcher

The dispatcher is instantiated with a listener, attaching to the `beforeException` event of the dispatcher. A custom plugin `NotFoundPlugin` is used to send output to the 404 page. Using the plugin allows us to reuse it anywhere in the application. This implementation is very beneficial when developing multi module applications.

*NOTE:* For the CLI application later on, we will need the [CLI dispatcher](https://docs.phalcon.io/latest/en/api/Phalcon_CLI_Dispatcher.html).

```php

        /**
         * We register the events manager
         */
        $di->set(
            'dispatcher',
            function () use ($di) {
                $eventsManager = new EventsManager;

                /**
                 * Handle exceptions and not-found exceptions using NotFoundPlugin
                 */
                $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);

                $dispatcher = new Dispatcher;
                $dispatcher->setEventsManager($eventsManager);

                $dispatcher->setDefaultNamespace('Kitsune\Controllers');

                return $dispatcher;
            }
        );
```

##### Views

The views are being initialized using [Volt](https://docs.phalcon.io/latest/en/api/Phalcon_Mvc_View_Engine_Volt.html) as the template engine. The main view is set up with the expected options.

```php
        $di->set(
            'view',
            function () use ($config) {
                $view = new View();
                $view->setViewsDir(K_PATH . '/app/views/');
                $view->registerEngines([".volt" => 'volt']);
                return $view;
            }
        );

        /**
         * Setting up volt
         */
        $di->set(
            'volt',
            function ($view, $di) {
                $volt = new VoltEngine($view, $di);
                $volt->setOptions(
                    [
                        "compiledPath"  => K_PATH . '/var/cache/volt/',
                        'stat'          => K_DEBUG,
                        'compileAlways' => K_DEBUG,
                    ]
                );
                return $volt;
            },
            true
        );

```
##### Cache

The cache component is configured using the `config.php`. We can define the parameters in that file and thus use say the `File` cache for our local/development machine and a more advanced cache (`Memcached` for instance) for the production system.

```php
        /**
         * Cache
         */
        $frontConfig = $config->cache_data->front->toArray();
        $backConfig  = $config->cache_data->back->toArray();
        $class       = '\Phalcon\Cache\Frontend\\' . $frontConfig['adapter'];
        $frontCache  = new $class($frontConfig['params']);
        $class       = '\Phalcon\Cache\Backend\\' . $backConfig['adapter'];
        $cache       = new $class($frontCache, $backConfig['params']);
        $di->set('cache', $cache, true);

        /**
         * viewCache
         */
        $frontConfig = $config->cache_view->front->toArray();
        $backConfig  = $config->cache_view->back->toArray();
        $class       = '\Phalcon\Cache\Frontend\\' . $frontConfig['adapter'];
        $frontCache  = new $class($frontConfig['params']);
        $class       = '\Phalcon\Cache\Backend\\' . $backConfig['adapter'];
        $cacheView   = new $class($frontCache, $backConfig['params']);
        $di->set('viewCache', $cacheView, true);
```

##### Markdown Renderer

We use Ciconia for the rendering of markdown with several plugins, existing and user defined. The registration is pretty straight forward.

```php
        /**
         * Markdown renderer
         */
        $di->set(
            'markdown',
            function () {
                $ciconia = new Ciconia();
                $ciconia->addExtension(new FencedCodeBlockExtension());
                $ciconia->addExtension(new TaskListExtension());
                $ciconia->addExtension(new InlineStyleExtension());
                $ciconia->addExtension(new WhiteSpaceExtension());
                $ciconia->addExtension(new TableExtension());
                $ciconia->addExtension(new UrlAutoLinkExtension());
                $ciconia->addExtension(new MentionExtension());

                $extension = new IssueExtension();
                $extension->setIssueUrl(
                    '[#%s](https://github.com/phalcon/cphalcon/issues/%s)'
                );
                $ciconia->addExtension($extension);

                $extension = new PullRequestExtension();
                $extension->setIssueUrl(
                    '[#%s](https://github.com/phalcon/cphalcon/pull/%s)'
                );
                $ciconia->addExtension($extension);
                return $ciconia;
            },
            true
        );

```

##### Posts Finder

This is a class we came up with, which is used to give us an easy way to get information about a specific post, the tag cloud, the index page etc. It is utilizing cache a lot!

```php
        /**
         * Posts Finder
         */
        $di->set(
            'finder',
            function () use ($utils, $cache) {
                $key        = 'post.finder.cache';
                $postFinder = $utils->cacheGet($key);
                if (null === $postFinder) {
                    $postFinder = new PostFinder();
                    $cache->save($key, $postFinder);
                }
                return $postFinder;
            },
            true
        );
```

#### Conclusion

In the next post of these series we will take a look at the router and discuss what each route means to our application.

Comments are more than welcome. If you have any questions on the implementation, feel free to ask in the comments below.

#### References

* [Phalcon Blog Github](https://github.com/phalcon/blog)
* [This Blog Github](https://github.com/niden/blog)
