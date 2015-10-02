<?php

namespace Kitsune\Cli\Tasks;

use Phalcon\CLI\Task as PhTask;

/**
 * Class MainTask
 *
 * @package Kitsune\Cli\Tasks
 */
class MainTask extends PhTask
{
    /**
     * Executes the main action of the cli mapping passed parameters to tasks
     */
    public function mainAction()
    {
        echo '01110000011010000110000101101100011000110110111101101110' . PHP_EOL;
        echo ' Kitsune' . PHP_EOL;
        echo '01110000011010000110000101101100011000110110111101101110' . PHP_EOL;
        echo PHP_EOL;
        echo 'Usage: cli command';

        echo PHP_EOL . PHP_EOL;

        $commands = [
            '    -rebuild-cache ',
            '        rebuilds all cached content',
            '',
        ];

        echo 'Commands:' .  PHP_EOL;

        foreach ($commands as $command) {
            echo $command . PHP_EOL;
        }

        echo PHP_EOL;
    }
}
