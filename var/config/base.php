<?php

return [
    'debugMode'  => 0,
    'baseUri'    => '/blog/',
    'cdnUrl'     => '',
    'blog'       => [
        'title'           => 'niden.net',
        'postsPerPage'    => 10,
        'customLayout'    => true,
        'googleAnalytics' => '',
        'googleCSE'       => '',
        'disqus'          => [
            'enabled'    => false,
            'url'        => 'https://phalconphp.com',
            'shortname'  => 'phalconphp',
            'idTemplate' => 'Phalcon Framework - %s',
            'oldUrl'     => 'http://phalconphp.tumblr.com/post/%s',
            'shortName'  => 'nidennetblog',
        ]
    ],
    'rss'        => [
        'title'       => 'Phalcon Framework Blog',
        'description' => 'We are an open source web framework for PHP ' .
                         'delivered as a C extension offering high ' .
                         'performance and lower resource consumption',
    ],
    'cache_data' => [
        'front' => [
            'adapter' => 'Data',
            'params'  => [
                'lifetime' => 86400
            ]
        ],
        'back'  => [
            'adapter' => 'File',
            'params'  => [
                'cacheDir' => K_PATH . '/var/data/cache'
            ]
        ]
    ],
    'cache_view' => [
        'front' => [
            'adapter' => 'Output',
            'params'  => [
                'lifetime' => 86400
            ]
        ],
        'back'  => [
            'adapter' => 'File',
            'params'  => [
                'cacheDir' => K_PATH . '/var/data/cache'
            ]
        ]
    ],
    'paths'      => [
        'controllersDir' => '',
        'viewsDir'       => '',
        'pluginsDir'     => '',
    ],
    'namespaces' => [
        'Kitsune'             => K_PATH . '/library/Kitsune',
        'Kitsune\Controllers' => K_PATH . '/app/controllers',
        'Kitsune\Cli'         => K_PATH . '/app/cli',
        'Kitsune\Cli\Tasks'   => K_PATH . '/app/cli/tasks',
        'Kitsune\Plugins'     => K_PATH . '/app/plugins',
    ],
    'routes'      => [
        '/rss' => [
            'controller' => 'posts',
            'action'     => 'rss'
        ],
        '/sitemap' => [
            'controller' => 'posts',
            'action'     => 'sitemap'
        ],
        '/search'  => [
            'controller' => 'posts',
            'action'     => 'search'
        ],
        '/post/{slug:[0-9a-zA-Z\-]+}' => [
            'controller' => 'posts',
            'action'     => 'view'
        ],
        '/tag/{tag:[0-9a-zA-Z\-\ \%\.]+}'  => [
            'controller' => 'posts',
            'action'     => 'tag'
        ],
        '/post/{timestamp:[0-9]+}/{slug:[0-9a-zA-Z\-]+}' => [
            'controller' => 'posts',
            'action'     => 'viewLegacy'
        ],
        '/'      => [
            'controller' => 'posts',
            'action'     => 'index'
        ],
        '/{page:[0-9]+}' => [
            'controller' => 'posts',
            'action'     => 'index'
        ],
        '/{page:[0-9]+}/{number:[0-9]+}' => [
            'controller' => 'posts',
            'action'     => 'index'
        ],
    ],
];
