	<div class="modal fade" id="modal_account" tabindex="-1" role="dialog" aria-labelledby="modal_account" aria-hidden="true">

		<div class="modal-dialog">
			
			<form action="<?php echo site_url('account/edit')?>" method="post" id="form_account">
														
			<div class="modal-content">

				<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title"><span class="fui-gear"></span> My Account</h4>
				</div>
				
				<?php
					
					$user = $this->ion_auth->user()->row();
					
				?>

				<div class="modal-body">
					
					<div class="alert alert-success" id="alert_accountSuccess" style="display: none">
					  	<h4>Yay!</h4>
					  	<div class="alertContent"></div>
					</div>
					
					<div class="alert alert-danger" id="alert_accountError" style="display: none">
					  	<h4>Error:</h4>
					  	<div class="alertContent"></div>
					</div>
					
					<div class="form-group">
					    <input type="text" class="form-control" id="field_accountFirstname" name="field_accountFirstname" placeholder="Your first name*" value="<?php echo $user->first_name;?>">
					</div>
					<div class="form-group">
					    <input type="text" class="form-control" id="field_accountLastname" name="field_accountLastname" placeholder="Your last name*" value="<?php echo $user->last_name;?>">
					</div>
					
					<hr class="dashed">
					
					<div class="form-group">
					    <input type="email" class="form-control" id="field_accountEmail" name="field_accountEmail" placeholder="Your email address *" value="<?php echo $user->email;?>">
					</div>
					<div class="form-group">
					    <input type="password" class="form-control" id="field_accountPassword" name="field_accountPassword" placeholder="Your password *" value="">
					</div>
					<div class="form-group">
					    <input type="password" class="form-control" id="field_accountPasswordRepeat" name="field_accountPasswordRepeat" placeholder="Repeat your password *" value="">
					</div>
					
	      	  	</div><!-- /.modal-body -->

				<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fui-cross-circle"></span> Cancel & Close</button>
					<button type="submit" class="btn btn-primary" id="button_saveAccount"><span class="fui-check"></span> <span class="buttonText">Update Account</span></button>
	      	  	</div>
				
				</form>
				
			</div><!-- /.modal-content -->

		</div><!-- /.modal-dialog -->

	</div><!-- /.modal -->