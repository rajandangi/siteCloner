	<div class="modal fade" id="modal_newClone" tabindex="-1" role="dialog" aria-labelledby="modal_newClone" aria-hidden="true">

		<div class="modal-dialog">
			
			<form action="" method="post" data-link="<?php echo site_url('clones/build')?>" id="form_newClone">
										
			<div class="modal-content">

				<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title"><span class="fui-plus"></span> Create New Clone</h4>
				</div>

				<div class="modal-body">
					
					<div class="alert alert-info">
						<button class="close fui-cross" data-dismiss="alert"></button>
						<h4>Configure events</h4>
						<p>
							Below you can configure what should be done when the clones is successfully built, or when the cloning process times out.
						</p>
					</div>
					
					<!-- Nav tabs -->
					<ul class="nav nav-tabs nav-append-content" role="tablist">
						<li role="presentation" class="active"><a href="#tab_onComplete" aria-controls="tab_onComplete" role="tab" data-toggle="tab">On Complete</a></li>
					    <li role="presentation"><a href="#tab_onTimeout" aria-controls="tab_onTimeout" role="tab" data-toggle="tab">On Timeout</a></li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">
	
					    <div role="tabpanel" class="tab-pane active" id="tab_onComplete">
							
							<div class="cloneReadyAction">
						
					            <div class="pull-right">
					              <input type="checkbox" data-toggle="switch" id="toggleEmail" />
					            </div>
								
								<input type="hidden" disabled name="doEmail" class="toggleEmail" value="yes">
						
								<b style="display: block; margin-bottom: 30px">Send an email</b>
						
								<div class="form-group">
									<input type="email" class="form-control toggleEmail" placeholder="Email address" name="field_email" id="field_email" disabled />
								</div>
								
								<div class="form-group">
									<input type="text" class="form-control toggleEmail" placeholder="Email subject" name="field_subject" id="field_subject" disabled />
								</div>
					
								<div class="form-group">
									<textarea class="form-control toggleEmail" placeholder="Your email message" name="textarea_message" id="textarea_message" rows="4" disabled></textarea>
								</div>
						
					            <label class="checkbox" for="checkbox_attachClone">
					            	<input type="checkbox" disabled value="yes" name="checkbox_attachClone" id="checkbox_attachClone" data-toggle="checkbox" class="toggleEmail">
					              	Attach clone as ZIP
					            </label>
						
							</div><!-- /.cloneReadyAction -->
							
							<div class="cloneReadyAction">
						
					            <div class="pull-right">
					              <input type="checkbox" data-toggle="switch" id="toggleFTP" />
					            </div>
								
								<input type="hidden" disabled name="doFTP" class="toggleFTP" value="yes">
						
								<b style="display: block; margin-bottom: 30px">Upload via FTP</b>
						
								<div class="form-group">
									<input type="text" class="form-control toggleFTP" placeholder="FTP Server" name="field_ftpserver" id="field_ftpserver" disabled />
								</div>
								
								<div class="form-group">
									<input type="text" class="form-control toggleFTP" placeholder="FTP User" name="field_ftpuser" id="field_ftpuser" disabled />
								</div>
					
								<div class="form-group">
									<input type="password" class="form-control toggleFTP" placeholder="FTP Password" name="field_ftpspassword" id="field_ftpspassword" disabled />
								</div>
								
								<div class="form-group">
									<input type="text" class="form-control toggleFTP" placeholder="FTP path - / by default, leave empty if in doubt" name="field_ftppath" id="field_ftppath" disabled />
								</div>
								
								<div class="form-group">
									<input type="text" class="form-control toggleFTP" placeholder="FTP port - 21 by default, leave empty if in doubt" name="field_ftpport" id="field_ftpport" disabled />
								</div>
						
					            <label class="checkbox" for="checkbox_ftpPassive">
					            	<input type="checkbox" disabled checked value="yes" name="checkbox_ftpPassive" id="checkbox_ftpPassive" data-toggle="checkbox" class="toggleFTP">
					              	Passive mode
					            </label>
						
							</div><!-- /.cloneReadyAction -->
							
					    </div><!-- /.tab-pane -->
	
					    <div role="tabpanel" class="tab-pane" id="tab_onTimeout">
							
							<div class="cloneReadyAction">
						
					            <div class="pull-right">
					              <input type="checkbox" data-toggle="switch" id="toggleEmail2" />
					            </div>
								
								<input type="hidden" disabled name="doEmail2" class="toggleEmail2" value="yes">
						
								<b style="display: block; margin-bottom: 30px">Send an email</b>
						
								<div class="form-group">
									<input type="email" class="form-control toggleEmail2" placeholder="Email address" name="field_email2" id="field_email2" disabled />
								</div>
								
								<div class="form-group">
									<input type="text" class="form-control toggleEmail2" placeholder="Email subject" name="field_subject2" id="field_subject2" disabled />
								</div>
					
								<div class="form-group">
									<textarea class="form-control toggleEmail2" placeholder="Your email message" name="textarea_message2" id="textarea_message2" rows="4" disabled></textarea>
								</div>
												
							</div><!-- /.cloneReadyAction -->
		
					    </div><!-- /.tab-pane -->
						
					</div><!-- /.tab-content -->
					
	      	  	</div><!-- /.modal-body -->

				<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fui-cross-circle"></span> Cancel & Close</button>
					<button type="submit" class="btn btn-primary"><span class="fui-check"></span> Continue, Create Clone</button>
	      	  	</div>
				
				</form>
				
			</div><!-- /.modal-content -->

		</div><!-- /.modal-dialog -->

	</div><!-- /.modal -->