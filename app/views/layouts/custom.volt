<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="author" content="">

        <title>{{ config.blog.title }}</title>

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        <link href="{{ cdnUrl }}/css/toolkit.css" rel="stylesheet">
        <link href="{{ cdnUrl }}/css/application.css" rel="stylesheet">
        <link href="{{ cdnUrl }}/css/prettify-dark.css" rel="stylesheet">
        <link href="{{ cdnUrl }}/css/style.css" rel="stylesheet">

        <style>
            /* note: this is a hack for ios iframe for bootstrap themes shopify page */
            /* this chunk of css is not part of the toolkit :) */
            body {
                width: 1px;
                min-width: 100%;
                *width: 100%;
            }
        </style>
    </head>

    <body class="with-top-navbar">
        <nav class="navbar navbar-inverse navbar-fixed-top app-navbar">
            <div class="container">
                <div class="navbar-header">
                    <button type="button"
                            class="navbar-toggle collapsed"
                            data-toggle="collapse"
                            data-target="#navbar-collapse-main">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">
                        <strong>niden.net</strong>
                    </a>
                </div>
                <div class="navbar-collapse collapse"
                     id="navbar-collapse-main">
                    <ul class="nav navbar-nav hidden-xs">
                        <li class="active">
                            <a href="/">Home</a>
                        </li>
                        <li>
                            <a href="/about">About</a>
                        </li>
                        <li>
                            <a href="/disclaimer">Disclaimer</a>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right m-r-0 hidden-xs">
                        <li>
                            <a class="app-notifications"
                               href="http://l.niden.net/nikos-g+">
                                <span class="icon icon-google-plus-with-circle"></span>
                            </a>
                        </li>
                        <li>
                            <a class="app-notifications"
                               href="https://www.linkedin.com/in/nikolaosdimopoulos">
                                <span class="icon icon-linkedin-with-circle"></span>
                            </a>
                        </li>
                    </ul>
                    {#
                    <form class="navbar-form navbar-right app-search"
                          role="search">
                        <div class="form-group">
                            <input type="text"
                                   class="form-control"
                                   data-action="grow"
                                   placeholder="Search">
                        </div>
                    </form>
                    #}
                    <ul class="nav navbar-nav hidden-sm hidden-md hidden-lg">
                        <li><a href="index.html">Home</a></li>
                        <li><a href="/about">About</a></li>
                        <li><a href="/disclaimer">Disclaimer</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container p-t-md">
            <div class="row">
                <div class="col-md-9">
                    <ul class="list-group media-list media-list-stream">
                        {{ content() }}
                    </ul>
                </div>

                <div class="col-md-3">
                    {% include 'posts/custom.profile.volt' %}
                    {# {% include 'posts/custom.ads-sidebar.volt' %} #}
                    {% include 'posts/custom.tag-cloud.volt' %}
                    {% include 'posts/custom.post-archive.volt' %}
                    {% include 'posts/custom.copyright.volt' %}
                </div>
            </div>
        </div>

        <script src="{{ cdnUrl }}/js/jquery.min.js"></script>
        <script src="{{ cdnUrl }}/js/chart.js"></script>
        <script src="{{ cdnUrl }}/js/toolkit.js"></script>
        <script src="{{ cdnUrl }}/js/application.js"></script>
        <script src="{{ cdnUrl }}/js/prettify.js"></script>
        <script src="{{ cdnUrl }}/js/lang-css.js"></script>
        <script src="{{ cdnUrl }}/js/lang-sql.js"></script>
        <script>
            prettyPrint();
            // execute/clear BS loaders for docs
            $(function(){
                if (window.BS&&window.BS.loader&&window.BS.loader.length) {
                    while(BS.loader.length){(BS.loader.pop())()}
                }
            })
        </script>
        {% if not empty config.blog.googleAnalytics %}
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '{{ config.blog.googleAnalytics }}', 'auto');
            ga('send', 'pageview');
        </script>
        {% endif %}
    </body>
</html>
