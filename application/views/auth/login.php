<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo isset($title)? $title : "FailSwitch CP";?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="<?php echo base_url('css/vendor/bootstrap.min.css');?>" rel="stylesheet">

    <!-- Loading Flat UI Pro -->
    <link href="<?php echo base_url('css/flat-ui-pro.css');?>" rel="stylesheet">
	<link href="<?php echo base_url('css/style.css');?>" rel="stylesheet">


    <link rel="shortcut icon" href="<?php echo base_url('img/favicon.png');?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="<?php echo base_url('js/vendor/html5shiv.js');?>"></script>
      <script src="<?php echo base_url('js/vendor/respond.min.js')?>"></script>
    <![endif]-->
</head>
<body>

    <div class="container">
        
		<div class="row">
			
			<div class="col-md-4 col-md-offset-4">
				
				<div class="text-center" style="margin-top: 70px; margin-bottom: 10px;">
					<a href="<?php echo site_url();?>"><img src="<?php echo base_url('img/logo.png')?>" alt="SiteCloner" style="width: 100px"></a>
				</div>
				
				<h3 class="text-center" style="margin-bottom: 50px; font-weight: 400">
					<span class="text-primary">Site</span>Cloner
				</h3>
				
				<?php if( isset($message) && $message != '' ):?>
	            <div class="alert alert-danger">
	            	<button type="button" class="close fui-cross" data-dismiss="alert"></button>
	              	<?php echo $message;?>
	            </div>
				<?php endif;?>
				
				<?php if( isset($_GET['ready']) ):?>
	            <div class="alert alert-success">
	            	<button type="button" class="close fui-cross" data-dismiss="alert"></button>
					<h4>Hooray!</h4>
	              	<p>
						Your new <b>FailSwitch</b> account is now ready! Login below using your account details. We have sent you an email with your account details and some more information. If you need any help, please feel to contact us at <a href="mailto:support@failswitch.com">support@failswitch.com</a> or use the live chat box at the bottom right corner of this window.
					</p>
	            </div>
				<?php endif;?>
				
				<form action="<?php echo site_url('login')?>" method="post">
					
					<div class="form-group">
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><span class="fui-user"></span></span>
							<input type="email" name="identity" id="identity" class="form-control input-lg" value="<?php echo isset($_POST['identity'])? $_POST['identity'] : '' ?>" placeholder="Email">
						</div>
					</div>
					
					<div class="form-group">
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><span class="fui-lock"></span></span>
							<input type="password" name="password" class="form-control input-lg" value="" placeholder="Password">
						</div>
					</div>
					
					<div class="form-group clearfix">
			            <label class="checkbox" for="remember">
			            	<input type="checkbox" value="1" id="remember" name="remember" data-toggle="checkbox">
			              	Remember me
			            </label>
					</div>
					
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-block btn-embossed btn-lg"><span class="fui-check"></span> Log me in</button>
					</div>
										
					<div class="form-group text-center">
						<a href="<?php echo site_url('forgot_password')?>" class="small"><?php echo lang('login_forgot_password');?></a> 
					</div>
					
				</form>
				
			</div><!-- /.col -->
			
		</div><!-- /.row -->
	
    </div><!-- /.container -->

    <!-- jQuery (necessary for Flat UI's JavaScript plugins) -->
    <script src="<?php echo base_url('js/vendor/jquery.min.js')?>"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo base_url('js/flat-ui-pro.min.js')?>"></script>
	
    <script src="<?php echo base_url('js/prettify.js')?>"></script>
    <script src="<?php echo base_url('js/application.js')?>"></script>
	
	<script>
	$(function(){
		
		$('#identity').focus();
		
	})
	</script>
	
</body>
</html>