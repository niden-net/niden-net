<?php
/*
 +------------------------------------------------------------------------+
 | Kitsune                                                                |
 +------------------------------------------------------------------------+
 | Copyright (c) 2015 Phalcon Team and contributors                       |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

/**
 * Bootstrap.php
 * \Kitsune\Bootstrap
 *
 * Bootstraps the application
 */
namespace Kitsune;

use Phalcon\Cli\Console as PhCliConsole;
use Phalcon\Cli\Dispatcher as PhCliDispatcher;
use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Di\FactoryDefault as PhDI;
use Phalcon\Loader;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as LoggerFile;
use Phalcon\Logger\Formatter\Line as LoggerFormatter;

use Phalcon\Mvc\Application;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\View\Simple as ViewSimple;
use Phalcon\Events\Manager as EventsManager;


use Kitsune\PostFinder;
use Kitsune\Plugins\NotFoundPlugin;
use Kitsune\Utils;

/**
 * Class Bootstrap
 */
class Bootstrap
{
    private $diContainer = null;

    public function run(DiInterface $diContainer, array $options = [])
    {
        $memoryUsage       = memory_get_usage();
        $currentTime       = microtime(true);
        $this->diContainer = $diContainer;

        /**
         * The app path
         */
        if (!defined('K_PATH')) {
            define('K_PATH', dirname(dirname(dirname(__FILE__))));
        }

        /**
         * We will need the Utils class
         */
        require_once K_PATH . '/library/Kitsune/Utils.php';

        /**
         * Utils class
         */
        $utils = new Utils();
        $this->diContainer->set('utils', $utils, true);

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
    
        /**********************************************************************
         * CONFIG
         **********************************************************************/
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
        $this->diContainer->set('config', $config, true);

        /**
         * Check if we are in debug/dev mode
         */
        if (!defined('K_DEBUG')) {
            $debugMode = boolval($utils->fetch($config, 'debugMode', false));
            define('K_DEBUG', $debugMode);
        }

        /**
         * Access to the debug/dev helper functions
         */
        if (K_DEBUG) {
            require_once K_PATH . '/library/Kitsune/Debug.php';
        }
    
        /**********************************************************************
         * LOADER
         **********************************************************************/
        /**
         * We're a registering a set of directories taken from the
         * configuration file
         */
        $loader = new Loader();
        $loader->registerNamespaces($config->namespaces->toArray());
        $loader->register();

        require K_PATH . '/vendor/autoload.php';
    
        /**********************************************************************
         * LOGGER
         **********************************************************************/
        /**
         * The essential logging service
         */
        $format    = '[%date%][%type%] %message%';
        $name      = K_PATH . '/var/log/' . date('Y-m-d') . '-kitsune.log';
        $logger    = new LoggerFile($name);
        $formatter = new LoggerFormatter($format);
        $logger->setFormatter($formatter);
        $this->diContainer->set('logger', $logger, true);
    
        /**********************************************************************
         * ERROR HANDLING
         **********************************************************************/
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
            function () use ($logger, $utils, $memoryUsage, $currentTime) {
                $memoryUsed    = memory_get_usage() - $memoryUsage;
                $executionTime = microtime(true) - $currentTime;
                if (K_DEBUG) {
                    $logger->info(
                        sprintf(
                            'Shutdown completed [%s]s - [%s]',
                            round($executionTime, 3),
                            $utils->bytesToHuman($memoryUsed)
                        )
                    );
                }
            }
        );

        $timezone = $config->get('app_timezone', 'US/Eastern');
        date_default_timezone_set($timezone);
    
        /**********************************************************************
         * ROUTES
         **********************************************************************/
        if (false === K_CLI) {
            $router = new Router(false);
            $router->removeExtraSlashes(true);
            $routes = $config->routes->toArray();
            foreach ($routes as $pattern => $options) {
                $router->add($pattern, $options);
            }

            $this->diContainer->set('router', $router, true);
        }

        /**********************************************************************
         * DISPATCHER
         **********************************************************************/
        if (false === K_CLI) {
            /**
             * We register the events manager
             */
            $eventsManager = new EventsManager;

            /**
             * Handle exceptions and not-found exceptions using NotFoundPlugin
             */
            $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);

            $dispatcher = new Dispatcher;
            $dispatcher->setEventsManager($eventsManager);

            $dispatcher->setDefaultNamespace('Kitsune\Controllers');
        } else {
            $dispatcher = new PhCliDispatcher();
            $dispatcher->setDefaultNamespace('Kitsune\Cli\Tasks');
        }

        $this->diContainer->set('dispatcher', $dispatcher);
    
        /**********************************************************************
         * URL
         **********************************************************************/
        /**
         * The URL component is used to generate all kind of urls in the application
         */
        $url = new UrlProvider();
        $url->setBaseUri($config->baseUri);
        $this->diContainer->set('url', $url);

        /**********************************************************************
         * VIEW
         **********************************************************************/
        $view = new View();
        $view->setViewsDir(K_PATH . '/app/views/');
        $view->registerEngines(
            [
                ".volt" => function ($view) {
                    return $this->setVoltOptions($view);
                },
            ]
        );
        $this->diContainer->set('view', $view);

        /**********************************************************************
         * VIEW SIMPLE
         **********************************************************************/
        $viewSimple = new ViewSimple();
        $viewSimple->setViewsDir(K_PATH . '/app/views/');
        $viewSimple->registerEngines(
            [
                ".volt" => function ($view) {
                    return $this->setVoltOptions($view);
                },
            ]
        );
        $this->diContainer->set('viewSimple', $viewSimple);

        /**********************************************************************
         * CACHE
         **********************************************************************/
        $frontConfig = $config->cache_data->front->toArray();
        $backConfig  = $config->cache_data->back->toArray();
        $class       = '\Phalcon\Cache\Frontend\\' . $frontConfig['adapter'];
        $frontCache  = new $class($frontConfig['params']);
        $class       = '\Phalcon\Cache\Backend\\' . $backConfig['adapter'];
        $cache       = new $class($frontCache, $backConfig['params']);
        $this->diContainer->set('cache', $cache, true);

        /**********************************************************************
         * POSTS FINDER
         **********************************************************************/
        $this->diContainer->set('finder', new PostFinder(), true);

        /**********************************************************************
         * DISPATCH 17.5s
         **********************************************************************/
        if (K_CLI) {
            return new PhCliConsole($this->diContainer);
        } else {
            $application = new Application($this->diContainer);

            if (K_TESTS) {
                return $application;
            } else {
                return $application->handle()->getContent();
            }
        }
    }

    /**
     * Sets Volt options for the various views
     *
     * @param \Phalcon\Mvc\View $view
     *
     * @return VoltEngine
     */
    private function setVoltOptions($view)
    {
        $di   = $this->diContainer;
        $volt = new VoltEngine($view, $di);
        $volt->setOptions(
            [
                "compiledPath"  => K_PATH . '/var/cache/volt/',
                'stat'          => true,
                'compileAlways' => K_DEBUG,
            ]
        );

        return $volt;
    }
}
