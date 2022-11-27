---
layout: post
title: Building a web app with PhalconPHP and AngularJS Update
date: 2012-11-25T23:45:00.000Z
tags:
  - php
  - phalcon
  - angularjs
  - how-to
image: '/assets/files/phalcon-logo.png'
image-alt: Phalcon
---
It's been a while since I last wrote a blog post, so I wanted to touch on the effort to upgrade the application that I [wrote](https://github.com/niden/phalcon-angular-harryhogfootball) for Harry Hog Fottball using [PhalconPHP](https://phalcon.io/) and [AngularJS](https://angularjs.org/)

If you haven't read it, the first two blog posts were [here](/post/building-web-app-with-phalconphp-and-angularjs-part-i) and [here](/post/building-web-app-with-phalconphp-and-angularjs-part-ii).

The application was written using the 0.4.5 version of [PhalconPHP](https://phalcon.io/). Since then there have been significant changes to the framework, such as the introduction of a DI container, injectable objects and lately interfaces (in 0.7.0, to be released in a couple of days), I had to make some changes.

There are a couple of things that I as a developer would like to see in [PhalconPHP](https://phalcon.io/), which I am pretty sure will appear later on, since let's face it the framework is still very young (not even 1.0 version yet). Despite its "*youth*" it is a robust framework with excellent support, features and a growing community. One of these features is behaviors which I had to implement myself, and this was something new that came with this upgrade.

Recently a new <a href="https://github.com/phalcon/incubator">repo</a> has been created on Github called the incubator, where developers can share implementations of common tasks, that act as drop ins to the framework and extend it. These implementations are all written in PHP so everyone can just download them and use them. The more submissions come in, the more the framework will grow and eventually these submissions will become part of the framework itself.

#### Converting the 0.4.x application to 0.5.x

The task of converting everything from 0.4 to 0.5 was a bit challenging. The reason behind it was the DI container and how best to use it to suit the needs of the current application. Now these challenges would not even be an issue if one started writing their application from scratch, but since I had everything in place, I ventured into upgrading vs. rewriting. Note that this kind of upgrade will most likely never happen again, since the framework has been changed accordingly so that future upgrades will not require developers to rewrite their code (like I did now). From 0.5.x onward the framework design has been kind of "*frozen*".

I decided to create a new library that will help me with my tasks. I therefore created a custom bootstrap class, that would instantiate everything I wanted in my code. A short snippet of the class is below (the full code of course is in my Github [repo](https://github.com/niden/phalcon-angular-harryhogfootball) which you are more than welcome to download and modify to suit your needs)

```php
namespace NDN;

use \Phalcon\Config\Adapter\Ini as PhConfig;
use \Phalcon\Loader as PhLoader;
....
use \Phalcon\Exception as PhException;

class Bootstrap
{
    private $_di;

    /**
     * Constructor
     * 
     * @param $di
     */
    public function __construct($di)
    {
        $this->_di = $di;
    }

    /**
     * Runs the application performing all initializations
     * 
     * @param $options
     *
     * @return mixed
     */
    public function run($options)
    {
        $loaders = array(
            'config',
            'loader',
            'environment',
            'timezone',
            'debug',
            'flash',
            'url',
            'dispatcher',
            'view',
            'logger',
            'database',
            'session',
            'cache',
            'behaviors',
        );


        try {
            foreach ($loaders as $service)
            {
                $function = 'init' . ucfirst($service);

                $this->$function($options);
            }

            $application = new PhApplication();
            $application->setDI($this->_di);

            return $application->handle()->getContent();

        } catch (PhException $e) {
            echo $e->getMessage();
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    // Protected functions

    /**
     * Initializes the config. Reads it from its location and
     * stores it in the Di container for easier access
     *
     * @param array $options
     */
    protected function initConfig($options = array())
    {
        $configFile = ROOT_PATH . '/app/var/config/config.ini';

        // Create the new object
        $config = new PhConfig($configFile);

        // Store it in the Di container
        $this->_di->set('config', $config);
    }

    /**
     * Initializes the loader
     *
     * @param array $options
     */
    protected function initLoader($options = array())
    {
        $config = $this->_di->get('config');

        // Creates the autoloader
        $loader = new PhLoader();

        $loader->registerDirs(
            array(
                ROOT_PATH . $config->app->path->controllers,
                ROOT_PATH . $config->app->path->models,
                ROOT_PATH . $config->app->path->library,
            )
        );

        // Register the namespace
        $loader->registerNamespaces(
            array("NDN" => $config->app->path->library)
        );

        $loader->register();
    }
    
    ....

    /**
     * Initializes the view and Volt
     *
     * @param array $options
     */
    protected function initView($options = array())
    {
        $config = $this->_di->get('config');
        $di     = $this->_di;

        $this->_di->set(
            'volt',
            function($view, $di) use($config)
            {
                $volt = new PhVolt($view, $di);
                $volt->setOptions(
                    array(
                        'compiledPath'      => ROOT_PATH . $config->app->volt->path,
                        'compiledExtension' => $config->app->volt->extension,
                        'compiledSeparator' => $config->app->volt->separator,
                        'stat'              => (bool) $config->app->volt->stat,
                    )
                );
                return $volt;
            }
        );
    }
    ....

    /**
     * Initializes the model behaviors
     *
     * @param array $options
     */
    protected function initBehaviors($options = array())
    {
        $session = $this->_di->getShared('session');

        // Timestamp
        $this->_di->set(
            'Timestamp',
            function() use ($session)
            {
                $timestamp = new Models\Behaviors\Timestamp($session);
                return $timestamp;
            }
        );
    }
}
```

I chose to show a few sections of this bootstrap which I will explain shortly. What this bootstrap class does is it initializes my whole environment and keeps my `index.php` file small.

```php
error_reporting(E_ALL);

try {

    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(dirname(__FILE__)));
    }

    // Using require once because I want to get the specific
    // bootloader class here. The loader will be initialized
    // in my bootstrap class
    require_once ROOT_PATH . '/app/library/NDN/Bootstrap.php';
    require_once ROOT_PATH . '/app/library/NDN/Error.php';

    // Instantiate the DI container
    $di  = new \Phalcon\DI\FactoryDefault();

    // Instantiate the boostrap class and inject the DI container 
    // in it so that services can be registered
    $app = new \NDN\Bootstrap($di);
   
    // Here we go!
    echo $app->run(array());

} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
}
```

As you can see the `index.php` is very small in terms of code.

Let's have a look at a couple of the functions that are in the bootstrap.

```php
    /**
     * Initializes the config. Reads it from its location and
     * stores it in the Di container for easier access
     *
     * @param array $options
     */
    protected function initConfig($options = array())
    {
        $configFile = ROOT_PATH . '/app/var/config/config.ini';

        // Create the new object
        $config = new PhConfig($configFile);

        // Store it in the Di container
        $this->_di->set('config', $config);
    }
```

Pretty straight forward. The config INI file is read from its location and stored in the DI container. I need to do this first, since a lot of the parameters of the application are controlled from that file.

```php
    /**
     * Initializes the loader
     *
     * @param array $options
     */
    protected function initLoader($options = array())
    {
        $config = $this->_di->get('config');

        // Creates the autoloader
        $loader = new PhLoader();

        $loader->registerDirs(
            array(
                ROOT_PATH . $config->app->path->controllers,
                ROOT_PATH . $config->app->path->models,
                ROOT_PATH . $config->app->path->library,
            )
        );

        // Register the namespace
        $loader->registerNamespaces(
            array("NDN" => $config->app->path->library)
        );

        $loader->register();
    }
```

The loader is what does all the discovery of classes for me. As you can see I store a lot of the paths in the config INI file, and I register my custom namespace NDN.

```php
    /**
     * Initializes the view and Volt
     *
     * @param array $options
     */
    protected function initView($options = array())
    {
        $config = $this->di->get('config');
        $di     = $this->_di;

        $this->_di->set(
            'volt',
            function($view, $di) use($config)
            {
                $volt = new PhVolt($view, $di);
                $volt->setOptions(
                    array(
                        'compiledPath'      => ROOT_PATH . $config->app->volt->path,
                        'compiledExtension' => $config->app->volt->extension,
                        'compiledSeparator' => $config->app->volt->separator,
                        'stat'              => (bool) $config->app->volt->stat,
                    )
                );
                return $volt;
            }
        );
    }
```

This is an interesting one. Registering the view and Volt. [Volt](https://docs.phalcon.io/latest/en/volt is the template engine that comes with [Phalcon](https://phalcon.io/). It is inspired by [Twig](https://twig.symfony.com) and written in C, thus offering maximum performance. I set the compiled path, extension and separator for the template files, and also I have a variable (set in the config of course) to allow the application to always create template files or not. In a production environment that variable (stat) will be set to false since templates do not change.

```php
    /**
     * Initializes the model behaviors
     *
     * @param array $options
     */
    protected function initBehaviors($options = array())
    {
        $session = $this->_di->getShared('session');

        // Timestamp
        $this->_di->set(
            'Timestamp',
            function() use ($session)
            {
                $timestamp = new Models\Behaviors\Timestamp($session);
                return $timestamp;
            }
        );
    }
```

The above is my implementation of behaviors. Of course it is far from perfect but it works the way I want to. A better implementation of this has been written by [Wojtek Gancarczyk](https://github.com/theDisco) and is available in the [incubator](https://github.com/phalcon/incubator). All I do here is go through the behaviors I have (Timestamp only for now) and register them in the DI container so that I can reuse them later on with any model that needs them.

#### Models

Every model I have that interacts with my database tables extends the `NDN\Model`. 

```php
class Model extends \Phalcon\Mvc\Model
{
    protected $behaviors = array();

    /**
     * Adds a behavior in the model
     *
     * @param $behavior
     */
    public function addBehavior($behavior)
    {
        $this->behaviors[$behavior] = true;
    }

    public function beforeSave()
    {
        $di   = Di::getDefault();

        foreach ($this->behaviors as $behavior => $active)
        {
            if ($active &amp;&amp; $di->has($behavior))
            {
                $di->get($behavior)->beforeSave($this);
            }
        }
    }

    /**
     * @param array $parameters
     *
     * @static
     * @return Phalcon_Model_Resultset Model[]
     */
    static public function find($parameters = array())
    {
        return parent::find($parameters);
    }

    /**
     * @param array $parameters
     *
     * @static
     * @return  Phalcon_Model_Base   Models
     */
    static public function findFirst($parameters = array())
    {
        return parent::findFirst($parameters);
    }
}
```

The class itself is pretty simple, offering `find` and `findFirst` to the class that extends this. The interesting thing is that it also registers behaviors and calls the relevant validator function. So for instance the `beforeSave` validator checks the registered behaviors (`$behaviors` array), checks if they are active, checks if they exist in the DI container and gets them from there and then calls the `beforeSave` in the behavior class.

The behavior class is equally simple:

```php
class Timestamp
{
    protected $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * beforeSave hook - called prior to any Save (insert/update)
     */
    public function beforeSave($record)
    {
        $auth     = $this->session->get('auth');
        $userId   = (isset($auth['id'])) ? (int) $auth['id'] : 0;
        $datetime = date('Y-m-d H:i:s');
        if (empty($record->created_at_user_id)) {
            $record->created_at         = $datetime;
            $record->created_at_user_id = $userId;
        }
        $record->last_update         = $datetime;
        $record->last_update_user_id = $userId;
    }
}
```

So effectively every time I call the `save()` function on a model, this piece of code will be executed, populating my fields with the date time and the user that created the record and/or updated it.

In order to get this functionality to work, all I have to do in my model is to register the behavior like so:

```php
class Episodes extends \NDN\Model
{
    /**
     * Initializes the class and sets any relationships with other models
     */
    public function initialize()
    {
        $this->addBehavior('Timestamp');
        $this->hasMany('id', 'Awards', 'episode_id');
    }
}
```

#### Controllers

Very little has changed in the controller logic, so that was the easiest part of the upgrade. Of course I tweaked a few things but the code works as is. I still extended my custom `NDN\Controller` class which takes care of my breadcrumbs (`NDN\Breadcrumbs`) as well as the construction of the top menu. The biggest difference with the previous version is that I stopped using [AngularJS](https://angularjs.org/) to populate the menu (so I am no longer sending a JSON array in the view) and used [Volt](https://docs.phalcon.io/latest/en/volt) instead. It was a matter of preference and nothing more.

#### Views

Quite a bit of work had to be done in the views to switch everything to use [Volt](https://docs.phalcon.io/latest/en/volt). Of course every view extension had to be changed to `.volt` but that was not the only change. I split the layout to use partials so that the header, navigation and footer are different sections (organizing things a bit better) and kept the master layout `index.volt`. 

I started using the built-in [Volt](https://docs.phalcon.io/latest/en/volt) functions to generate content as well as tags, and it was a nice surprise to see that everything was easy to use and it worked!

```html
{% raw %}
<!DOCTYPE html>
<html ng-app='HHF'>
    {{ partial('partials/header') }} 
    <body>
        <div id="spinner" style="display: none;">
            {{ image('img/ajax-loader.gif') }} Loading ...
        </div>
        
        {{ partial('partials/navbar') }}

        <div class='container-fluid'>
            <div class='row-fluid'>
                <ul class='breadcrumb'>
                    <li>
                        {% for bc in breadcrumbs %}
                        {% if (bc['active']) %}
                        {{ bc['text'] }}
                        {% else %}
                        <a href='{{ bc['link'] }}'>{{ bc['text'] }}</a> 
                        <span class='divider'>/</span>
                        {% endif %}
                        {% endfor %}
                    </li>
                </ul>
            </div>
        
            <?php echo $this->flash->output() ?>
        
            <div class="row-fluid">
                <?php echo $this->getContent() ?>
            </div> <!-- row -->
        
            {{ partial('partials/footer') }}
        </div>
        
        {{ javascript_include(config.app.js.jquery, config.app.js.local) }}
        {{ javascript_include(config.app.js.jquery_ui, config.app.js.local) }}
        {{ javascript_include(config.app.js.bootstrap, config.app.js.local) }}
        {{ javascript_include(config.app.js.angular, config.app.js.local) }}
        {{ javascript_include(config.app.js.angular_resource, config.app.js.local) }}
        {{ javascript_include(config.app.js.angular_ui, config.app.js.local) }}
        {{ javascript_include('js/utils.js') }}
        
    </body>
</html>
{% endraw %}
```

The above is the `index.volt`. As you can see I call on the `partials/header.volt`, then the `partials/navbar.volt` (where the menu is generated) and then I construct the breadcrumbs (note the `{% raw %}{% for bc in breadcrumbs %}{% endraw %}` block). After that the flash messenger comes into play, the main content displayed, the footer and finally the javascript includes that I need.

I am still using [AngularJS](https://angularjs.org/) to make the necessary AJAX calls so that the relevant controller to retrieve the data but also to display this data on screen (which is cached to avoid unnecessary database hits).

The Episodes view became

```html
{% raw %}
{{ content() }}

<div>
    <ul class='nav nav-tabs'>
        <li class='pull-right'>
            {{ addButton }}
        </li>
    </ul>
</div>

<div ng-controller='MainCtrl'>
    <table class='table table-bordered table-striped ng-cloak' ng-cloak>
        <thead>
        <tr>
            <th><a href='' ng-click="predicate='number'; reverse=!reverse">#</a></th>
            <th><a href='' ng-click="predicate='air_date'; reverse=!reverse">Date</a></th>
            <th><a href='' ng-click="predicate='outcome'; reverse=!reverse">W/L</a></th>
            <th><a href='' ng-click="predicate='summary'; reverse=!reverse">Summary</a></th>
        </tr>
        </thead>
        <tbody>
            <tr ng-repeat="episode in data.results | orderBy:predicate:reverse">
                <td>[[episode.number]]</td>
                <td width='7%'>[[episode.air_date]]</td>
                <td>[[episode.outcome]]</td>
                <td>[[episode.summary]]</td>
                {% if (addButton) %}
                <td width='1%'><a href='/episodes/edit/[[episode.id]]'><i class='icon-pencil'></i></a></td>
                <td width='1%'><a href='/episodes/delete/[[episode.id]]'><i class='icon-remove'></i></a></td>
                {% endif %}
            </tr>
        </tbody>
    </table>
</div>
{% endraw %}
```

The beauty of [AngularJS](https://angularjs.org/)! I only have to pass a JSON array with my results. `ng-repeat` with the `orderBy` filter allows me to present the data to the user and offer sorting capabilities per column. This is all done at the browser level **without** any database hits! Pretty awesome feature!

For those that have used [AngularJS](https://angularjs.org/) in the past, you will note that I had to change the interpolate provider (i.e. the characters that wrap a string or a piece of code that [AngularJS](https://angularjs.org/) understands). Usually these characters are the curly brackets `{% raw %}{{ }}{% endraw %}` but I changed them to `[[ ]]` to avoid collisions with [Volt](https://docs.phalcon.io/latest/en/volt).

This was done with a couple of lines of code in my definition of my [AngularJS](https://angularjs.org/) model:

```js
{% raw %}
var ngModule = angular.module(
        'HHF', 
        ['ngResource', 'ui']
    )
    .config(
        function ($interpolateProvider) {
            $interpolateProvider.startSymbol('[[');
            $interpolateProvider.endSymbol(']]');
        }
    )
{% endraw %}
```

#### Conclusion

I spent at most a day working on this mostly because I wanted to try various things and see how it works. The actual time to convert the application (because let's face it, it is a small application) was a couple of hours inclusive of the time it took me to rename certain fields, restructure the folder structure, compile the new extension on my server and upload the data upstream.

I am very satisfied with both [AngularJS](https://angularjs.org/), which helps tremendously in my presentation layer, as well as with [Phalcon](https://phalcon.io/). Phalcon's new design makes implementation a breeze, while [AngularJS](https://angularjs.org/) offers a lot of flexibility on the view layer.

As written before, you are more than welcome to download the [source code](https://github.com/niden/phalcon-angular-harryhogfootball) of this application here and use it for your own needs. Some resources are:

#### References

* [AngularJS main site](https://angularjs.org/)
* [AngularJS documentation](https://docs.angularjs.org/api)
* [AngularJS group](https://groups.google.com/g/angular)
* [AngularJS Github](https://github.com/angular)

* [Phalcon PHP main site](https://phalcon.io/)
* [Phalcon PHP documentation](https://docs.phalcon.io/)
* [Phalcon Discussions](https://phalcon.io/discussions)
* [Phalcon PHP Github](https://github.com/phalcon)
