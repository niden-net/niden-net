<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ config.blog.title }}</title>

    <link rel="stylesheet" type="text/css"
          href="{{ cdnUrl }}/css/style.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">{{ config.blog.title }}</a>
        </div>
        <!-- /.navbar-header -->

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a href="/about">
                    <i class="fa fa-question-circle fa-fw"></i> About
                </a>
            </li>
            <li class="dropdown">
                <a href="/disclaimer">
                    <i class="fa fa-institution fa-fw"></i> Disclaimer
                </a>
            </li>
            <li class="dropdown">
                <a href="https://github.com/niden/">
                    <i class="fa fa-github fa-fw"></i>
                </a>
            </li>
            <li class="dropdown">
                <a href="http://l.niden.net/nikos-g+">
                    <i class="fa fa-google-plus"></i>
                </a>
            </li>
            <li class="dropdown">
                Nikolaos Dimopoulos
            </li>
        </ul>

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="sidebar-search">
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                        <!-- /input-group -->
                    </li>
                    {% for url, title in menuList %}
                    <li>
                        <a href="/post/{{ url }}"> {{ title|e }}</a>
                    </li>
                    {% endfor %}
                </ul>
            </div>
        </div>
    </nav>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-10">
            {{ content() }}
            </div>
            <div class="col-lg-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Ads
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        Ads body
                    </div>
                    <!-- /.panel-body -->
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <i>
                            Boldly goes where no coder has gone before... and
                            other ramblings.
                        </i>
                        <br />
                        <br />
                        Personal blog of Nikolaos Dimopoulos.
                    </div>
                    <!-- /.panel-body -->
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Tag Cloud
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                    {% for tag, class in tagCloud %}
                        <span style="font-size: {{ class }}">
                            <a href='/tag/{{ tag }}'>{{ tag }}</a>
                        </span>
                    {% endfor %}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

    <!-- Custom Theme JavaScript -->
    <script type="text/javascript"
            src="{{ cdnUrl }}/js/js.min.js"></script>
    <script type="text/javascript">prettyPrint();</script>

</body>
</html>
