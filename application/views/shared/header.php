<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo (isset($page))? "SiteCloner &raquo; ".$page : 'SiteCloner'?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>

    <!-- Loading Bootstrap -->
    <link href="<?php echo base_url('css/vendor/bootstrap.min.css');?>" rel="stylesheet">

    <!-- Loading Flat UI Pro -->
    <link href="<?php echo base_url('css/flat-ui-pro.css')?>" rel="stylesheet">

	<link href="<?php echo base_url('css/style.css')?>" rel="stylesheet">
	
	<link href="<?php echo base_url('css/font-awesome.min.css')?>" rel="stylesheet">
		
    <link rel="shortcut icon" href="<?php echo base_url('img/favicon.png')?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="js/vendor/html5shiv.js"></script>
      <script src="js/vendor/respond.min.js"></script>
    <![endif]-->
</head>
<body>
	  	  
    <div class="container">
		
		<header>
        
			<nav role="navigation" class="navbar navbar-default navbar-inverse navbar-embossed navbar-lg">
			
				<div class="navbar-header">
					<button data-target="#navbar-collapse-02" data-toggle="collapse" class="navbar-toggle" type="button">
						<span class="sr-only">Toggle navigation</span>
					</button>
					<a href="#" class="navbar-brand"><img src="<?php echo base_url('img/logo.png')?>"><span class="text-primary">Site</span>Cloner</a>
				</div><!-- /.navbar-header -->

				<div id="navbar-collapse-02" class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li <?php if( $page == 'Sites' ):?>class="active"<?php endif;?>><a href="<?php echo site_url('sites')?>"><span class="fui-window"></span> Sites</a></li>
						<li <?php if( $page == 'Clones' ):?>class="active"<?php endif;?>><a href="<?php echo site_url('clones')?>"><span class="fui-windows"></span> Clones</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<?php
							$user = $this->ion_auth->user()->row();
							?>
		 					<a data-toggle="dropdown" class="dropdown-toggle" href="#"><span class="fui-user"></span> Hi <?php echo $user->first_name." ".$user->last_name;?>  <b class="caret"></b></a>
					   		<ul class="dropdown-menu">
								<li><a href="#modal_account" data-toggle="modal"><span class="fui-gear"></span> My Account</a></li>
						   	 	<li class="divider"></li>
						   	 	<li><a href="<?php echo site_url('logout')?>"><span class="fui-power"></span> Logout</a></li>
					   	 	</ul>
				   	 	</li>
			   	 	</ul>
		  		</div><!-- /.navbar-collapse -->
			
			</nav>
		
		</header>