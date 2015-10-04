<?php

namespace Kitsune\Cli\Tasks;

use Phalcon\CLI\Task as PhTask;
use Kitsune\PostFinder as KPostFinder;

/**
 * Class RebuildCacheTask
 *
 * @package Kitsune\Cli\Tasks
 */
class RebuildCacheTask extends PhTask
{
    /**
     * Rebuilds the posts cache
     */
    public function mainAction()
    {
        $this->output('Clearing Volt Templates');
        foreach (glob(K_PATH . '/var/cache/volt/*.php') as $file) {
            $this->output('Deleting file: ' . $file);
            unlink($file);
        }

        $this->output('Emptying Cache');
        foreach (glob(K_PATH . '/var/cache/data/*.cache') as $file) {
            $this->output('Deleting file: ' . $file);
            unlink($file);
        }

        $this->output('Rebuilding Cache');
        $postFinder = new KPostFinder();
        $posts = $postFinder->getPosts();
        foreach ($posts as $post) {
            $this->output('Rebuilding Cache for ' . $post['title']);
            $postFinder->get($post['slug']);
        }

        $this->output('Rebuilding Tag Cloud');
        $postFinder->getTagCloud();

        $this->output('Rebuilding Archive');
        $postFinder->getArchive();

        $this->output('Rebuilding Latest Pages Cache');
        $pages = $postFinder->getTotalPages();
        for ($counter = 1; $counter <= $pages; $counter++) {
            $postFinder->getLatest($counter);
        }

        $this->output('Rebuild completed.');
    }

    private function output($message)
    {
        echo $message . PHP_EOL;
    }
}
