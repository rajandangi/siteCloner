<?php

error_reporting(E_NONE); //Setting this to E_ALL showed that that cause of not redirecting were few blank lines added in some php files.

$db_config_path = '../application/config/database.php';

// Only load the classes in case the user submitted the form
if($_POST) {

	// Load the classes and create the new objects
	require_once('includes/core_class.php');
	require_once('includes/database_class.php');

	$core = new Core();
	$database = new Database();


	// Validate the post data
	if($core->validate_post($_POST) == true)
	{

		// First create the database, then create tables, then write config file
		if($database->create_database($_POST) == false) {
			$message = $core->show_message('error',"The database could not be created, please verify your settings.");
		} else if ($database->create_tables($_POST) == false) {
			$message = $core->show_message('error',"The database tables could not be created, please verify your settings.");
		} else if ($core->write_config($_POST) == false) {
			$message = $core->show_message('error',"The database configuration file could not be written, please chmod application/config/database.php file to 777");
		}
		
		sleep(8);
		
		//create admin user
		//$database->create_admin($_POST, $_POST['email'], $_POST['password_admin']);

		// If no errors, redirect to registration page
		if(!isset($message)) {
		  $redir = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
      $redir .= "://".$_SERVER['HTTP_HOST'];
      $redir .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
      $redir = str_replace('install/','',$redir); 
			header( 'Location: done.php') ;
		}
		
		

	}
	else {
		$message = $core->show_message('error','Not all fields have been filled in correctly. <b>All fields below are required to install SiteCloner.</b>');
	}
}

?>
<!DOCTYPE html>
<html lang="en">
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
	    body.login form .input-group .input-group-btn button.btn {
		    height: 42px;
	    }
	    h5.smaller {
	    	font-size: 20px;
	    	margin-bottom: 20px;
	    }
	    </style>
	</head>
	<body class="login">
	
	<div class="container">
	
		<div class="row">
		
			<?php if(is_writable($db_config_path)){?>
		
			<div class="col-md-4 col-md-offset-4">
			
				<div class="text-center" style="margin-top: 70px; margin-bottom: 10px;">
					<img src="../img/logo.png" alt="SiteCloner" style="width: 100px">
				</div>
				
				<?php if( isset($message) ):?>
				<div class="alert alert-danger">
					<button type="button" class="close fui-cross" data-dismiss="alert"></button>
				  	<?php echo $message;?>
				</div>
				<?php endif?>
						
				<form role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
				
					<h5 class="smaller"><span class="fui-gear"></span> Database Configuration</h5>
					
					<div class="form-group">
    					<div class="input-group input-group-lg">
    						 <span class="input-group-addon"><span class="fui-user"></span></span>
							 <input type="text" class="form-control" id="hostname" name="hostname" value="<?php if( isset($_POST['hostname']) ){echo $_POST['hostname'];}else{echo "localhost";}?>" placeholder="Hostname">
    					</div>
   					</div>
				
   					<div class="form-group">
    					<div class="input-group input-group-lg">
    						 <span class="input-group-addon"><span class="fui-user"></span></span>
							 <input type="text" class="form-control" id="username" name="username" value="<?php if( isset($_POST['username']) ){echo $_POST['username'];}?>" placeholder="Username">
    					</div>
   					</div>
   					
   					<div class="form-group">
    					<span class="input-group input-group-lg">
    						 <span class="input-group-addon"><span class="fui-lock"></span></span>
							 <input type="password" class="form-control" id="password" name="password" value="<?php if( isset($_POST['username']) ){echo $_POST['password'];}?>" placeholder="Password">
    					</span>
   					</div>
   					
   					<div class="form-group">
    					<span class="input-group input-group-lg">
    						 <span class="input-group-addon"><span class="fui-list"></span></span>
							 <input type="text" class="form-control" id="database" name="database" value="<?php if( isset($_POST['database']) ){echo $_POST['database'];}?>" placeholder="Database name">
    					</span>
   					</div>
   							  	
				  	<button type="submit" class="btn btn-primary btn-embossed btn-block"><span class="fui-check"></span> Install <b>SiteCloner</b></button>
				  	
				  	<br><br>
				  	
				</form>
							
			</div><!-- /.col-md-6 -->
			
			<?php } else { ?>
			<div class="col-md-6 col-md-offset-3">
			
				<div class="text-center" style="margin-top: 70px; margin-bottom: 10px;">
					<img src="../img/logo.png" alt="SiteCloner" style="width: 100px">
				</div>
				
				<div class="alert alert-error">
					<button type="button" class="close fui-cross" data-dismiss="alert"></button>
					<p>
					Please make the application/config/database.php file writable.
					<br><strong>Example</strong>:<br /><code>chmod 777 application/config/database.php</code></p>
				</div>
			</div>
			<?php } ?>
		
		</div><!-- /.row -->
	
	</div><!-- /.container -->
	
    <!-- jQuery (necessary for Flat UI's JavaScript plugins) -->
    <script src="../js/vendor/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/flat-ui-pro.min.js"></script>
	
    <script src="../js/prettify.js"></script>
    <script src="../js/application.js"></script>
	
	<script>
	$(function(){
		
		$('#hostname').focus();
		
	})
	</script>
	
	</body>
</html>