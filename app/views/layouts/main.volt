<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ title is defined ? title|e ~ " - niden.net Blog" : "niden.net Blog" }}</title>

        <meta charset="utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <meta name="application-name" content="niden.net Blog" />
        <meta name="keywords"
              content="php, phalcon, phalcon php, php framework, mysql" />
        <meta name="description"
              content="Personal blog of Nikolaos Dimopoulos; Boldly goes where no coder has gone before... and other ramblings" />
        <meta name="viewport"
              content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

        <link rel="stylesheet"
              type="text/css"
              href="//netdna.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" />
        <link rel='stylesheet'
              type='text/css'
              href='//fonts.googleapis.com/css?family=Lato:800,400'>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <link rel="stylesheet"
              type="text/css"
              href="{{ cdnUrl }}/css/style.css"/>
        <link rel="stylesheet"
              type="text/css"
              href="{{ cdnUrl }}/css/prettify-dark.css"/>

        <link rel="alternate"
              type="application/rss+xml"
              href="https://niden.net/rss" />
    </head>
    <body>
        <div id="header" class="navbar navbar-default navbar-fixed-top">
            <div class="navbar-header">
                <button class="navbar-toggle collapsed"
                        type="button"
                        data-toggle="collapse"
                        data-target=".navbar-collapse">
                    <i class="icon-reorder"></i>
                </button>
                <a class="navbar-brand" href="/">niden.net</a>
            </div>
            <nav class="collapse navbar-collapse">
                <ul class="nav navbar-nav pull right">
                    <li><a href="/about">About</a></li>
                    <li><a href="/disclaimer">Disclaimer</a></li>
                </ul>
                <span class="pull-right">
                    Boldly goes where no coder has gone before... and other ramblings
                </span>
            </nav>
        </div>
        <div id="wrapper">
            <div id="sidebar-wrapper" class="col-md-2">
                <div id="sidebar">
                    <ul class="nav list-group">
                        {% for url, title in menuList %}
                        <li>
                            <a class="list-group-item" href="/post/{{ url }}">
                                {{ title|e }}
                            </a>
                        </li>
                        {% endfor %}
                    </ul>
                </div>
            </div>
            <div id="main-wrapper" class="col-md-10">
                <div id="main">
                    {{ content() }}
                </div>
            </div>
        </div>

        <script type="text/javascript"
                src="//cdn.jsdelivr.net/g/jquery@2.1,bootstrap@3.1,prettify@0.1(prettify.js+lang-css.js+lang-sql.js)"></script>
        <script type="text/javascript">prettyPrint();</script>
        <script src="https://apis.google.com/js/plusone.js"></script>
    </body>
</html>
