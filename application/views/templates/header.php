<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <!--<link rel="icon" href="../../favicon.ico">-->

    <title>Expositions</title>

    <?php //site_url('system/application/libraries/jquery.js');
    $this->load->helper('url');
    $templateJs = base_url() . 'assets/js/template/';
    $templateCss = base_url() . 'assets/css/template/';
    ?>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="<?php echo $templateCss ?>bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="<?php echo $templateCss ?>bootstrap-theme.min.css">

    <script src="<?php echo $templateJs ?>jquery.min.js"></script>
    <script src="<?php echo $templateJs ?>bootstrap.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo $templateJs ?>html5shiv.min.js"></script>
    <script src="<?php echo $templateJs ?>respond.min.js"></script>
    <![endif]-->
    <style>
        body {
            padding-top: 70px;
        }
    </style>
</head>

<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $this->config->base_url();?>index.php">vtExposition</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="<?php if($this->uri->uri_string()=="about"){echo "active";}?>">
                    <a href="<?php echo $this->config->base_url();?>index.php/about">About</a>
                </li>
                <!--<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>-->
            </ul>
            <!--<ul class="nav navbar-nav navbar-right">
                <li><a href="#">Fixed top</a></li>
            </ul>-->
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">







