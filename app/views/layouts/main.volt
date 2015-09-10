<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
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
          href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css" />
    <link rel="stylesheet"
          type="text/css"
          href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" />

    <link rel="stylesheet"
          type="text/css"
          href="{{ cdnUrl }}/css/style.css"/>

    <link rel="stylesheet"
          type="text/css"
          href="{{ cdnUrl }}/css/prettify-dark.css"/>

    <link rel="alternate"
          type="application/rss+xml"
          href="https://niden.net/rss" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    <!-- Main wrapper -->
    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Top level navigation -->
            <div class="navbar-header">
                <button type="button"
                        class="navbar-toggle"
                        data-toggle="collapse"
                        data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">niden.net</a>
            </div>

            <!-- Top menu bar -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-user"></i> Nikolaos Dimopoulos <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="/about"><i class="fa fa-fw fa-user"></i> About</a>
                        </li>
                        <li>
                            <a href="/disclaimer"><i class="fa fa-fw fa-envelope"></i> Disclaimer</a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- Sidebar menu  -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    {% for url, title in menuList %}
                    <li> <!-- class="active" -->
                        <a href="/post/{{ url }}">{{ title }}</a>
                    </li>
                    {% endfor %}
                </ul>
            </div>
            <!-- /Sidebar menu  -->
        </nav>
        <!-- /Navigation  -->

        <div id="page-wrapper">
            <div class="container-fluid">

                {{ content() }}

            </div>
        </div>
    </div>

    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
    <script type="text/javascript">prettyPrint();</script>
    <script type="text/javascript"
            src="//apis.google.com/js/plusone.js"></script>
</body>
</html>
