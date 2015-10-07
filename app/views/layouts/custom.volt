<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="author" content="">

        {{ tag.getTitle() }}
        {% if 1 == config.debugMode %}

        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600'
              rel='stylesheet'
              type='text/css'>
        <link href="{{ cdnUrl }}/css/toolkit.css" rel="stylesheet">
        <link href="{{ cdnUrl }}/css/application.css" rel="stylesheet">
        <link href="{{ cdnUrl }}/css/prettify-dark.css" rel="stylesheet">
        <link href="{{ cdnUrl }}/css/style.css" rel="stylesheet">
        {% else %}

        <link href="{{ cdnUrl }}/css/prod.css" rel="stylesheet">
        {% endif %}

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
                            <a class="app-notifications" href="/search">
                                <span class="icon icon-magnifying-glass"></span>
                            </a>
                        </li>
                        <li>
                            <a class="app-notifications"
                               href="https://google.com/+NikolaosDimopoulos-niden">
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
                        <li><a href="/">Home</a></li>
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
                        {% include 'posts/custom.ads-below-nav.volt' %}
                        {{ content() }}
                    </ul>
                </div>

                <div class="col-md-3">
                    {% include 'posts/custom.profile.volt' %}
                    {% include 'posts/custom.ads-sidebar.volt' %}
                    {% include 'posts/custom.tag-cloud.volt' %}
                    {% include 'posts/custom.post-archive.volt' %}
                    {% include 'posts/custom.copyright.volt' %}
                </div>
            </div>
        </div>

        <script src="{{ cdnUrl }}/js/jquery.min.js"></script>
        {% if 1 == config.debugMode %}

        <script src="{{ cdnUrl }}/js/chart.js"></script>
        <script src="{{ cdnUrl }}/js/toolkit.js"></script>
        <script src="{{ cdnUrl }}/js/application.js"></script>
        <script src="{{ cdnUrl }}/js/prettify.js"></script>
        {% else %}

        <script src="{{ cdnUrl }}/js/prod.js"></script>
        {% endif %}

        <script>
            prettyPrint();
            // execute/clear BS loaders for docs
            $(function(){
                if (window.BS&&window.BS.loader&&window.BS.loader.length) {
                    while(BS.loader.length){(BS.loader.pop())()}
                }
            })
        </script>
        {% if config.blog.googleAnalytics|length > 0 %}
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '{{ config.blog.googleAnalytics }}', 'auto');
            ga('send', 'pageview');
        </script>
        {% endif %}


        {% if 'search' == action and config.blog.googleCSE|length > 0 %}
        <script>
            (function() {
                var cx = 'partner-pub-6325600846885391:sypil8-9bxo';
                var gcse = document.createElement('script');
                gcse.type = 'text/javascript';
                gcse.async = true;
                gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
                        '//cse.google.com/cse.js?cx=' + cx;
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(gcse, s);
            })();
        </script>
        {% endif %}

        <!-- Powered by Phalcon {{ version() }} - https://phalconphp.com -->
    </body>
</html>
