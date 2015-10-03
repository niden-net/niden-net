<?php
/*
 +------------------------------------------------------------------------+
 | Kitsune                                                                |
 +------------------------------------------------------------------------+
 | Copyright (c) 2015-2015 Phalcon Team and contributors                  |
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
 * PostFinder.php
 * \Kitsune\PostFinder
 *
 * Allows faster searching of blog posts
 */
namespace Kitsune;

use Phalcon\Mvc\User\Component as PhComponent;
use Kitsune\Markdown as KMarkdown;
use Kitsune\Exceptions\Exception as KException;

/**
 * Class PostFinder
 *
 * @package Kitsune
 */
class PostFinder extends PhComponent
{
    private $data     = [];
    private $pages    = [];
    private $markdown = null;

    /**
     * The constructor. Reads the posts JSON file and creates the necessary
     * mapping indexes
     *
     * @throws KException
     */
    public function __construct()
    {
        $this->markdown = new KMarkdown();
        $this->data = [
            'data'  => [],
            'tags'  => [],
            'links' => [],
            'dates' => [],
        ];

        /**
         * Cached data - Pages
         */
        $this->pages = $this->readPagesJson();

        /**
         * Cached data - Posts
         */
        $this->data = $this->readPostsJson();
    }

    /**
     * Gets the latest number of posts for the blog. Used in the first page
     *
     * @param  int $page   The page
     *
     * @return array
     */
    public function getLatest($page = 1)
    {
        $key   = sprintf('posts-latest-%s.cache', $page);
        $posts = $this->utils->cacheGet($key);

        if (null === $posts) {
            $page  = ($page < 1) ? 1 : $page;
            $dates = $this->utils->fetch($this->data['dates'], $page, null);
            if (!is_null($dates)) {
                foreach ($dates as $date) {
                    $posts[] = $this->data['data'][$date];
                }
                $this->cache->save($key, $posts);
            } else {
                $this->response->redirect('', false, 301);
            }
        }

        return $posts;
    }

    public function getLatestByTag($tag, $number)
    {
        $tag   = strtolower(urldecode($tag));
        $key   = "posts-tags-{$tag}-{$number}.cache";
        $posts = $this->utils->cacheGet($key);

        if (null === $posts) {
            foreach ((array) $this->data['tags'][$tag] as $key) {
                $posts[strtotime($this->data['data'][$key]['date'])] = $this->data['data'][$key];
            }
            ksort($posts);
            $posts = array_slice(array_reverse($posts), 0, $number);
            $this->cache->save($key, $posts);
        }

        return $posts;
    }

    /**
     * Gets the tag cloud for the sidebar
     *
     * @return array
     */
    public function getTagCloud()
    {
        $cacheKey = 'tag-cloud.cache';
        $tagCloud = $this->utils->cacheGet($cacheKey);

        if (null === $tagCloud) {
            $max  = 0;
            $tags = [];

            foreach ($this->data['tags'] as $key => $items) {
                $tags[$key] = count($items);
                $max        = ($max > count($items)) ? $max : count($items);
            }

            foreach ($tags as $key => $count) {
                $percent = floor(($count / $max) * 100);
                if ($percent < 20) {
                    $class = 'x-small';
                } elseif ($this->utils->between($percent, 21, 40)) {
                    $class = 'small';
                } elseif ($this->utils->between($percent, 41, 60)) {
                    $class = 'medium';
                } elseif ($this->utils->between($percent, 61, 80)) {
                    $class = 'large';
                } else {
                    $class = 'larger';
                }
                $tags[$key] = $class;
            }

            $tagCloud = $this->utils->shuffle($tags);

            $this->cache->save($cacheKey, $tagCloud);
        }

        return $tagCloud;
    }

    /**
     * Gets the the archive posts
     *
     * @return array
     */
    public function getArchive()
    {
        $cacheKey   = 'post-archive.cache';
        $postArchive = $this->utils->cacheGet($cacheKey);

        if (null === $postArchive) {
            $thisYear    = date('Y');
            $postArchive = [];
            foreach ($this->data['data'] as $post) {
                $date = $post['date'];

                /**
                 * Check which year and month this post was made and add it
                 * to the postArchive array. If it is not this year, then
                 * add it to the year entry for the year the post was posted
                 */
                $year      = date('Y', strtotime($date));
                $month     = date('m', strtotime($date));
                $monthText = date('F', strtotime($date));
                $key       = ($thisYear === $year) ? $month     : $year;
                $text      = ($thisYear === $year) ? $monthText : $year;

                if (false === isset($postArchive[$key])) {
                    $postArchive[$key] = [
                        'key'   => $text,
                        'value' => 0,
                    ];
                }
                $postArchive[$key]['value'] = $postArchive[$key]['value'] + 1;
            }

            krsort($postArchive);

            $this->cache->save($cacheKey, $postArchive);
        }

        return $postArchive;
    }

    /**
     * Returns all the posts
     *
     * @return array
     */
    public function getPosts()
    {
        return $this->data['data'];
    }

    /**
     * Returns the menu list i.e. posts (url/title)
     *
     * @param bool|true $reverse
     *
     * @return array
     */
    public function getList($reverse = true)
    {
        $reverseKey = (true === boolval($reverse)) ? 'desc' : 'asc';
        $cacheKey   = sprintf('menu-list-%s.cache', $reverseKey);
        $menuList   = $this->utils->cacheGet($cacheKey);
        if (null === $menuList) {
            foreach ($this->data as $url => $post) {
                $menuList[$url] = $post['title'];
            }
            if (true === boolval($reverse)) {
                $menuList = array_reverse($menuList, true);
            }
            $this->cache->save($cacheKey, $menuList);
        }

        return $menuList;
    }

    /**
     * Gets a post from the internal collection based on a slug. If the slug is
     * numeric, this is a Disqus link. The function will find it and return the
     * correct post.
     *
     * @param  string $slug The slug of the post
     *
     * @return mixed
     */
    public function get($slug)
    {
        $key  = 'post-' . $slug . '.cache';
        $post = $this->utils->cacheGet($key);

        if (null === $post) {
            if (array_key_exists($slug, $this->data['data'])) {
                $post = $this->data['data'][$slug];
                $this->cache->save($key, $post);
            }
        }

        return $post;
    }

    public function getPage($slug)
    {
        $key  = 'page-' . $slug . '.cache';
        $page = $this->utils->cacheGet($key);

        if (null === $page) {
            if (array_key_exists($slug, $this->pages)) {
                $contents = file_get_contents(
                    sprintf(
                        '%s/data/pages/%s.md',
                        K_PATH,
                        $slug
                    )
                );

                $page = [
                    'page'  => $this->markdown->render($contents),
                    'title' => $this->pages[$slug],
                ];

                $this->cache->save($key, $page);
            }
        }

        return $page;
    }

    public function getPages($page = 1)
    {
        $return = [
            'previous' => $page - 1,
            'next'     => $page + 1
        ];

        $totalPages = count($this->data['dates']);

        if (($page + 1) > $totalPages) {
            $return['next'] = 0;
        }

        if (($page - 1) < 1) {
            $return['previous'] = 0;
        }

        return $return;
    }

    /**
     * Returns the array of posts
     *
     * @return array
     * @throws \Kitsune\Exceptions\Exception
     */
    private function readPostsJson()
    {
        $key  = 'post.finder.data.cache';
        $data = $this->utils->cacheGet($key);

        if (null === $data) {
            $sourceFile = K_PATH . '/data/posts.json';

            if (!file_exists($sourceFile)) {
                throw new KException('Posts JSON file cannot be located');
            }

            $contents = file_get_contents($sourceFile);
            $posts    = json_decode($contents, true);

            if (false === $posts) {
                throw new KException('Posts JSON file is potentially corrupted');
            }

            $data = $this->processPostData($posts);

            $this->cache->save($key, $data);
        }

        return $data;
    }

    /**
     * Processes posts from the JSON file and arranges them for internal use
     *
     * @param array $posts
     *
     * @return array
     */
    private function processPostData($posts)
    {
        $data      = [];
        $tags      = [];
        $links     = [];
        $dates     = [];

        foreach ($posts as $item) {
            $post = $this->loadPost($item);

            /**
             * Add the element in the master array
             */
            $data[$post['slug']] = $post;

            /**
             * Tags
             */
            foreach ($post['tags'] as $tag) {
                $key          = strtolower(trim($tag));
                $tags[$key][] = $post['slug'];
            }

            /**
             * Links
             */
            $links[$post['link']] = $post['slug'];

            /**
             * Dates (sorting)
             */
            $dates[$post['date']] = $post['slug'];
        }

        /**
         * Sort the dates array
         */
        krsort($dates);
        $postsPerPage = intval($this->config->blog->postsPerPage);
        $postsPerPage = ($postsPerPage < 0) ? 10 : $postsPerPage;
        $dates        = array_chunk($dates, $postsPerPage);

        /**
         * Adding one element to the beginning of the array to deal with the
         * 0 based array index since the array keys correspond to the pages
         */
        array_unshift($dates, []);

        return [
            'data'  => $data,
            'tags'  => $tags,
            'links' => $links,
            'dates' => $dates,
        ];
    }

    /**
     * Returns the array of pages
     *
     * @return array|mixed
     */
    private function readPagesJson()
    {
        $key   = 'post.finder.pages.cache';
        $pages = $this->utils->cacheGet($key);

        if (null === $pages) {
            /**
             * If there is a pages json
             */
            $pages      = [];
            $sourceFile = K_PATH . '/data/pages.json';
            if (file_exists($sourceFile)) {
                $contents = file_get_contents($sourceFile);
                $data     = json_decode($contents, true);

                foreach ($data as $page) {
                    $pages[$page['slug']] = $page['title'];
                }
            }
            $this->pages = $pages;
            $this->cache->save($key, $pages);
        }

        return $pages;
    }

    public function loadPost($post)
    {
        $data          = $post;
        $data['tags']  = [];
        $dateParts     = explode("-", $post['date']);

        if ($data['link']) {
            $data['disqusUrl'] = $data['link'];
            $data['disqusId']  = sprintf(
                $this->config->blog->disqus->idTemplate,
                $data['title']
            );
        } else {
            $data['disqusUrl'] = $this->config->blog->disqus->url
                . '/post/'
                . $data['slug'];
            $data['disqusId']  = sprintf(
                $this->config->blog->disqus->idTemplate,
                str_replace(['"', "''"], ['', ''], $data['title'])
            );
        }

        $file = sprintf(
            '%s/%s/%s-%s.md',
            $dateParts[0],
            $dateParts[1],
            $data['date'],
            $data['slug']
        );

        $tags = explode(',', $post['tags']);
        foreach ($tags as $tag) {
            $data['tags'][] = trim($tag);
        }

        /**
         * Get the cdnUrl
         */
        $cdnUrl = $this->config->cdnUrl;

        /**
         * Get the post itself
         */
        $fileName = K_PATH . '/data/posts/' . $file;
        if (file_exists($fileName)) {
            $raw     = file_get_contents($fileName);
            $raw     = str_replace('{{ cdnUrl }}', $cdnUrl, $raw);
            $data['content'] = $this->markdown->render($raw);
        }

        return $data;
    }
}
