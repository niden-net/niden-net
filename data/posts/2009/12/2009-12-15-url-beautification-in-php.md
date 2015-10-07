#### Preface

The last few years I have been using Quicken to manage the home bank accounts and spending. My wife never had any problems with me doing so and it helps us manage our cash flow better. Of course like every couple we buy things that we need and always provision a small amount of money for personal expenses.
<img class="post-image" src="{{ cdnUrl }}/files/php.png" />

Some time ago when downloading the transactions from our bank, I noticed a rather odd name coming in for a very small charge (less than 20 dollars). So I asked my wife what that shop is. Her response was *Beautification*. My reply was *What do you mean? You don't need anything - you are gorgeous as is...*. Although this did earn me brownie points it also infuriated my wife since she was thinking I was auditing her. My sole purpose of that question was to assess the category of the expense and move on. I was not interested the least about the particulars of the expense. I got my reply - a detailed one - as to what the item was for (makeup really) and once my wife finished I explained to her that all this explanation was unnecessary and all I needed was *Personal Care*.... The night ended in laughter as you can imagine and since then we always refer as *Beautification* when we pass the makeup area in the grocery store.

The purpose of this post is not to try to make you go buy makeup. It is however a post that can show you how you can make your application's behavior as far as URLs are concerned *prettier*.

*Beautification* in URLs is also known as *Pretty URLs*. It is a method where `mod_rewrite` is used to make a URL look easier to remember. Although this process started in an attempt to increase SEO and make a site's URLs easier to index/follow, it has now become a must for every site that wants to create awareness and make people remember as much as possible so that they can revisit. Wordpress (this blog's blogging software) as well as other blogging software use this methodology to ensure that the posts are easily indexed and remembered (see the URL of this post and the title of this post).

#### Beautification

##### First Step

Instead of rewriting my whole application to have beautiful and easy to remember URLs, I started with changing the way I processed parameters. Imagine the scenario where I have a site with various pages and one script that processes everything. My URL could be something like:

```php
..../site.php?page=contact-us
..../site.php?page=about
```

Following discussions with other developers and reading the web the URL was beautified as:

```php
..../site.php?page/contact-us
..../site.php?page/about
```

This is one parameter, so in my bootstrap I have the following snippet:

```php
$getData      = array();
$params       = explode('/',$_SERVER['QUERY_STRING']);
$params[]     = '';
$paramsLength = sizeof($params);

for ($counter = 0; $counter < $paramsLength; $counter++) {
    $getData[$params[$counter]] = (isset($params[$counter + 1])) ? $params[$counter + 1] : '';
}

unset($_GET);
```

The `$getData` array contains the parameters that have been passed and allows all search engines to index the URL a lot easier since this is considered to be one parameter.

Please note that I am not going to expand on security here. In my production code there are multiple checks on the variables passed so as to ensure that there are no SQL injection vulnerabilities.

##### A better approach with .htaccess

In order to make the URL look a lot more *beautified*, I need to remove the script name and the question mark of the query string. So my URL can easily be like:

```php
..../page/contact-us
..../page/about</pre>
```

To achieve this, I need (in Apache) [mod_rewrite](http://httpd.apache.org/docs/2.0/mod/mod_rewrite.html) enabled and a couple of directives in `httpd.conf`

```html
Options +FollowSymLinks
RewriteEngine On
```

If your configuration is on a virtual host, you can add these directives in your `vhosts` file.

The `.htaccess` file that I have in the root folder of my site has the following directives:

```html
Options       +FollowSymLinks
RewriteEngine On
RewriteCond   %{SCRIPT_FILENAME} !-d
RewriteCond   %{SCRIPT_FILENAME} !-f
RewriteRule   .* site.php [L]</pre>
```

What the above file does is pass everything in the `site.php` script. The processing now falls in the `site.php` script. At the top of the script I have:

```php
<?php

// This is where we get all the parameters
$params = $_SERVER['REQUEST_URI'];

if (substr('/', $params) > 0) {
    switch ($params) {
        case '/page/contact-us':
            include 'contact_us.php';
            break;
        case '/page/about':
            include 'about.php';
            break;
        default:
            include 'filenotfound.php';
            break;
    }

// No parameters passed - display something default
} else {
   include 'filenotfound.php';
}
```

From here on the possibilities are endless. I can add more logic to the rewrite rules so that everything ends in `.html` for instance, making search engines think that they are visiting unique html pages. Discussion forum software uses this technique to make their content easily searchable. For instance a URL like:

```php
/showpost.php?p=557799
```

can be shown as:

```php
/show/post/post/557799
```

or

```php
/show/post/post-557799.html
```

Something like the above can be achieved with the following rule in `.htaccess`:

```html
Options       +FollowSymLinks
RewriteEngine On
RewriteRule   ^post-([0-9]+)+\.html$ /showpost.php?p=$1 [NC,L]</pre>
```

##### Zend Framework

If I want to push the envelope further I will need to use [Zend Framework](http://framework.zend.com/) to do all this effortlessly. Zend Framework's [front controller](http://framework.zend.com/manual/en/zend.controller.html) provides amazing flexibility in terms of how my application URLs can be displayed on the web.

[`Zend_Controller_Router_Route`](http://framework.zend.com/manual/en/zend.controller.router.html#zend.controller.router.routes.standard), [`Zend_Controller_Router_Route_Static`](http://framework.zend.com/manual/en/zend.controller.router.html#zend.controller.router.routes.static) and [`Zend_Controller_Router_Rewrite`](http://framework.zend.com/manual/en/zend.controller.router.html#zend.controller.router.default-routes) are some of the types of routes available in the front controller. Using a configuration file or issuing relevant directives in the bootstrap file are options that I can use to manipulate my application's URLs.

By default the front controller uses the controller/action scheme but I can easily change this using a router and adding that router to my controller.

```php
$router = $frontController->getRouter();

$router->addRoute(
    'post',
    new Zend_Controller_Router_Route(
        'post/:post',
        array('controller' => 'post',
              'action' => 'show'))
    );
```

This is a small example on how a simple controller/action sequence can still work as expected and the resulting URL is *beautified*.

#### Final thoughts

Having easy to remember URLs in an application is a must. It will not only help search engines crawl your site easier - thus making your site more easily discoverable - but it will also help your users remember key areas of your site.

[Zend Framework](http://framework.zend.com/) is by far one of the best solutions available, since it introduces a small learning curve in terms of routing and `mod_rewrite` while providing pretty URLs. However, for more a complicated rewriting scheme your application might need a very sophisticated [.htaccess](http://httpd.apache.org/docs/2.0/howto/htaccess.html) file.
