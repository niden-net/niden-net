<?php

namespace Kitsune\Cli;

use Phalcon\CLI\Console as PhConsoleApp;
use Phalcon\DI\FactoryDefault\CLI as PhCliDI;

use Kitsune\Bootstrap as KBootstrap;

/**
 * Using require once because I want to get the specific bootloader class
 * here. The loader will be initialized in my bootstrap class
 */
$path = dirname(dirname(dirname(__FILE__)));

/**
 * Composer and Codeception Coverage
 */
require_once $path . '/vendor/autoload.php';
require_once $path . '/library/Kitsune/Bootstrap.php';

$options      = ['cli' => true];
$di_container = new PhCliDI();
$bootstrap    = new KBootstrap();
$console_app  = $bootstrap->run($di_container, $options);

/**
 * Put the console in the di_container because we need to use it in the
 * main task
 */
$di_container->setShared('console', $console_app);

/**
 * Process the console arguments
 */
$arguments = [];
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

// Define global constants for the current task and action
define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    // handle incoming arguments
    $console_app->handle($arguments);
} catch (\Exception $e) {
    print_r($e->getMessage());
    exit;
}
