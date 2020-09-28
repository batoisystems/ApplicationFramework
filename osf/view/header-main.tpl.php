<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php if(isset($APP->PAGEVARS['TITLE'])) print $APP->PAGEVARS['TITLE']; ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="<?php print $APP->BASEURL;?>/pub/images/favicon.ico">
    <link rel="apple-touch-icon" href="<?php print $APP->BASEURL;?>/view/images/apple-touch-icon.png">
	<link rel="stylesheet" href="https://www.batoi.com/themes/preview/pub/css/theme.php?theme=basic&dev=y&navbar=appfixed">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>
<body>
<!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]--><!-- Fixed navbar -->
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container osf-navbar-img-sm">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#"><img src="<?php print $APP->BASEURL;?>/pub/images/logo-inverse.png" alt="Logo">
			  </a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
				<li><a href="#">Menu 1</a></li>
                <li><a href="#">Menu 2</a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Other Menus <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="#">Sub Menu 1</a></li>
                    <li><a href="#">Sub Menu 2</a></li>
                    <li role="separator" class="divider"></li>
                    <li class="dropdown-header">Sub Menu Category</li>
                    <li><a href="#">Sub Menu 3</a></li>
                  </ul>
                </li>
              </ul>
			  <ul class="nav navbar-nav navbar-right">
		        <li><a href="#"><i class="fa fa-bell-o"></i></a></li>
		        <li class="dropdown">
		          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> <?php print $USER->USERNAME?> <span class="caret"></span></a>
		          <ul class="dropdown-menu">
		            <li><a href="#">Account Sub Menu 1</a></li>
		            <li><a href="#">Account Sub Menu 2</a></li>
		            <li role="separator" class="divider"></li>
		            <li><a href="<?php print $APP->BASEURL?>/login/index.php?ID=5">Logout</a></li>
		          </ul>
		        </li>
			</ul>
            </div><!--/.nav-collapse -->
          </div>
	  </nav>
	  
