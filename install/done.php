<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8">
	    <title>SiteCloner Installation</title>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	    <!-- Loading Bootstrap -->
	    <link href="../css/vendor/bootstrap.min.css" rel="stylesheet">
	
	    <!-- Loading Flat UI -->
	    <link href="../css/flat-ui-pro.css" rel="stylesheet">
	    <link href="../css/style.css" rel="stylesheet">
		<link href="style.css" rel="stylesheet">
	    	
	    <link rel="shortcut icon" href="../img/favicon.png">
	
	    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
	    <!--[if lt IE 9]>
	      <script src="../js/html5shiv.js"></script>
	      <script src="../js/respond.min.js"></script>
	    <![endif]-->
	    <style>
	    h5.smaller {
	    	font-size: 20px;
	    	margin-bottom: 20px;
	    }
	    </style>
	</head>
	<body class="login">
	
	<div class="container">
	
		<div class="row">
		
			<div class="col-md-6 col-md-offset-3">
			
				<div class="text-center" style="margin-top: 70px; margin-bottom: 10px;">
					<img src="../img/logo.png" alt="SiteCloner" style="width: 100px">
				</div>
				
				<?php
				
					$redir = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
					$redir .= "://".$_SERVER['HTTP_HOST'];
					$redir .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
					$redir = str_replace('install/','',$redir); 
				
				?>
				
				<div class="alert alert-success">
					<button type="button" class="close fui-cross" data-dismiss="alert"></button>
				  	<h4>All done, yeah!</h4>
				  	<p>
				  		You're all set! SiteCloner has successfully been installed. You can now continue to login using the following details (you can change these after logging in):
				  	</p>
					<ul>
						<li><b>username</b>: admin@admin.com</li>
						<li><b>password</b>: password</li>
					</ul>
				  	<p>
				  		Please don't forget to <b>DELETE</b> the /install folder.
 				  	</p>
				  	<a href="../index.php/login" class="btn btn-primary btn-wide btn-embossed"><span class="fui-lock"></span>&nbsp;&nbsp;Log into SiteCloner</a>
				</div>
			
			</div><!-- /.col-md-6 -->
		
		</div><!-- /.row -->
	
	</div><!-- /.container -->

	</body>
</html>