<?php $this->load->view('shared/header')?>
		
		<?php if( $this->session->flashdata('error') != '' ):?>
		<div class="alert alert-danger">
			<button class="close fui-cross" data-dismiss="alert"></button>
		  	<p style="line-height: 24px">
				<span class="fui-info-circle text-warning pull-left" style="font-size: 22px; margin-right: 5px"></span><?php echo $this->session->flashdata('error');?>
			</p>
		</div>
		<?php endif;?>
		
		<?php if( $this->session->flashdata('success') != '' ):?>
		<div class="alert alert-success">
			<button class="close fui-cross" data-dismiss="alert"></button>
		  	<p style="line-height: 24px">
				<span class="fui-check-circle text-primary pull-left" style="font-size: 22px; margin-right: 5px"></span><?php echo $this->session->flashdata('success');?>
			</p>
		</div>
		<?php endif;?>
		
		<form action="<?php echo site_url('clones/removeClones')?>" method="post">
		<div class="row margin-bottom-30">
						
			<div class="col-md-12">
				
				<div class="clearfix">
					
					<select class="form-control select select-inverse select-block mbl select-md pull-right" id="select_sites">
						<option value="">Choose a URL</option>
				    	<?php foreach( $sites as $site ):?>
							<option value="<?php echo $site->site_id?>"><?php echo $site->site_url?></option>
						<?php endforeach;?>
					</select>
					
					<a href="#modal_newClone" data-toggle="modal" class="btn btn-primary btn-md disabled pull-right" style="margin-right: 10px" id="button_newClonePopup"><span class="fui-plus-circle"></span> Build A New Clone for <span class="fui-arrow-right"></span></a>	
					
				</div>
				
				<hr class="dashed">
				
			</div><!-- /.col -->
			
		</div><!-- /.row -->
		
		<div class="row">
			
			<div class="col-md-12">
				
				<?php if( $clones ):?>
					<table class="table table-bordered table-hover clonesTable dt-responsive" id="clonesTable">
	                	<thead>
							<tr>
	                    		<th class="text-center"><label class="checkbox no-label toggle-all" for="checkbox-table-0"><input type="checkbox" value="" id="checkbox-table-0" data-toggle="checkbox"></label></th>
	                    		<th>Domain</th>
	                    		<th>URL</th>
	                    		<th>Clone Date</th>
								<th>Clone Link</th>
								<th>Status</th>
								<th></th>
	                  	  	</tr>
	                	</thead>
	                	<tfoot>
							<tr>
	                    		<th class="text-center"><label class="checkbox no-label toggle-all" for="checkbox-table-0"><input type="checkbox" value="" id="checkbox-table-0" data-toggle="checkbox"></label></th>
	                    		<th>Domain</th>
	                    		<th>URL</th>
	                    		<th>Clone Date</th>
								<th>Clone Link</th>
								<th>Status</th>
								<th></th>
	                  	  	</tr>
	                	</tfoot>
	                	<tbody>
							<?php foreach( $clones as $clone ):?>
							<tr>
	                    	  	<td class="text-center"><label class="checkbox no-label" for="checkbox-table-<?php echo $clone->crawl_id?>"><input type="checkbox" name="toDel[]" value="<?php echo $clone->crawl_id?>" id="checkbox-table-<?php echo $clone->crawl_id?>" data-toggle="checkbox"></label></td>
	                    	 	<td><div class="urlWrapper"><?php echo $clone->site_domain?></div></td>
	                    	  	<td><div class="urlWrapper"><a href="http://<?php echo $clone->site_url;?>" target="_blank"><?php echo $clone->site_url?></a></div></td>
	                    	  	<td><?php echo ( $clone->timestamp != 0 )? date("Y-m-d", $clone->timestamp) : 'NA';?></td>
								<td><a href="<?php echo base_url('/sites/'.$clone->site_id."/".$clone->timestamp."/")?>" target="_blank" title="Click to open the cloned site in a new window" data-toggle="tooltip"><?php echo $clone->site_id?>/<?php echo $clone->timestamp?>&nbsp;<i class="fa fa-external-link"></i></a></td>
								<td class="statusItems">
									<span <?php if( $clone->status == 'complete' ):?>style="display: inline"<?php endif;?> class="label label-primary statusComplete" data-crawlid="<?php echo $clone->crawl_id?>">completed</span>
									<a <?php if( $clone->status == 'building' ):?>style="display: inline"<?php endif;?> href="#cloningProgressModal" title="Click to check the cloning progress" data-toggle="tooltip" data-crawlid="<?php echo $clone->crawl_id?>" id="link_checkProgress" class="label label-warning checkProgressLink statusBuilding" id="clone_<?php echo $clone->crawl_id?>">building...</a>
									<span <?php if( $clone->status == 'cancelled' ):?>style="display: inline"<?php endif;?> class="label label-danger statusCancelled" data-crawlid="<?php echo $clone->crawl_id?>">cancelled</span>
									<span <?php if( $clone->status == 'timed out' ):?>style="display: inline"<?php endif;?> class="label label-default statusTimedout" data-crawlid="<?php echo $clone->crawl_id?>" title="You can re-build this clone anytime, this will continue where it left the last time if it was not completed or start from scratch when it was completed" data-toggle="tooltip">timed out</span>									
								</td>
							  	<td class="text-right tableActions">
									<a href="<?php echo site_url('clones/getZip/'.$clone->site_id."/".$clone->timestamp);?>" target="_blank" title="Download clone as ZIP" data-toggle="tooltip"><i class="fa fa-arrow-circle-o-down"></i></a>
									<a href="#modal_sendCloneByEmail" data-toggle="tooltip" class="sendByEmailLink" data-siteid="<?php echo $clone->site_id?>" data-timestamp="<?php echo $clone->timestamp?>" title="Send clone as email attachment" data-toggle="tooltip"><i class="fa fa-envelope-o"></i></a>
									<a href="#modal_uploadClone" title="Upload clone to remote FTP account" data-toggle="tooltip" data-crawlid="<?php echo $clone->crawl_id?>" class="uploadCloneLink"><i class="fa fa-upload"></i></a>
									<a href="<?php echo base_url('/sites/'.$clone->site_id."/".$clone->timestamp."/")?>" title="Open clone in new window" data-toggle="tooltip" target="_blank"><i class="fa fa-external-link-square"></i></a>
									<a href="<?php echo site_url('clones/build/'.$clone->site_id."/".$clone->crawl_id);?>" title="Re-build this clone from scratch" data-toggle="tooltip"><i class="fa fa-repeat"></i></a>
									<a href="<?php echo site_url('clones/rbuild/'.$clone->site_id."/".$clone->crawl_id);?>" title="Continue with timed-out clone" style="display: <?php if( $clone->status == 'timed out' || $clone->status == 'cancelled' ):?>inline<?php else:?>none<?php endif?>" data-toggle="tooltip" class="continueCrawl" data-crawlid="<?php echo $clone->crawl_id?>"><i class="fa fa-refresh"></i></a>
								</td>
	                  		</tr>
							<?php endforeach;?>
						</tbody>
	   		 		</table>
				<?php else:?>
				<div class="alert alert-info">
					<button class="close fui-cross" data-dismiss="alert"></button>
				  	<h4>No Clones</h4>
				  	<p>
				  		Looks you haven't created any clones just yet. To start creating clones, start by selecting a site and then click the "Create A New Clone for" button.
				  	</p>
				</div>
				<?php endif;?>
				
			</div><!-- /.col -->
			
		</div><!-- /.row -->
		</form>
		
    </div><!-- /.container -->
	
	<div id="porgressModals">
	<?php if( $clones && $page == 'Clones' ):?>
	<?php foreach( $clones as $clone ):?>
		<?php if( $clone->status == 'building' ):?>
		<div class="modal fade" id="cloningProgressModal_<?php echo $clone->crawl_id?>" tabindex="-1" role="dialog" aria-labelledby="cloningProgressModal" aria-hidden="true">

			<div class="modal-dialog modal-lg">

				<div class="modal-content">

					<div class="modal-header">
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        		<h4 class="modal-title"><i class="fa fa-line-chart"></i> Cloning Progress</h4>
					</div>

					<div class="modal-body">

						<div class="cloningProgress" id="cloningProgress_<?php echo $clone->crawl_id?>">Loading cloning progress...<br></div>

		      	  	</div><!-- /.modal-body -->

					<div class="modal-footer">
		        		<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fui-cross-circle"></span> Close Window</button>
						&nbsp;&nbsp;&nbsp;or
						<a href="#" class="btn btn-danger button_cancelCrawl" id="" data-siteid="<?php echo $clone->site_id;?>" data-timestamp="<?php echo $clone->timestamp;?>" data-crawlid="<?php echo $clone->crawl_id?>"><span class="fui-cross"></span> <span class="buttonText">Cancel Cloning Process</span></a>
						<a href="<?php echo site_url('clones/rbuild/'.$clone->site_id."/".$clone->crawl_id."/")?>" class="btn btn-primary button_continueCrawl" data-siteid="<?php echo $clone->site_id;?>" data-timestamp="<?php echo $clone->timestamp;?>" data-crawlid="<?php echo $clone->crawl_id?>" style="display: none"><i class="fa fa-refresh"></i> Continue Cloning Process</a>
		      	  	</div>

				</div><!-- /.modal-content -->

			</div><!-- /.modal-dialog -->

		</div><!-- /.modal -->
		<?php endif;?>
	<?php endforeach?>
	<?php endif;?>
	</div>
	
	<div class="modal fade" id="modal_sendCloneByEmail" tabindex="-1" role="dialog" aria-labelledby="modal_sendCloneByEmail" aria-hidden="true">

		<div class="modal-dialog">
			
			<form method="post" action="<?php echo site_url('clones/sendByEmail')?>" id="form_sendCloneByEmail">
				
			<input type="hidden" name="siteID" value="" id="input_siteID">
			<input type="hidden" name="timestamp" value="" id="input_timestamp">
			
			<div class="modal-content">

				<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title"><span class="fui-mail"></span> Send Clone By Email (as attachment)</h4>
				</div>

				<div class="modal-body">
					
					<div class="alert alert-success" id="alert_sendCloneSuccess" style="display: none">
						<button class="close fui-cross" data-dismiss="alert"></button>
					  	<h4>Yay!</h4>
					  	<div class="alertContent"></div>
					</div>
					
					<div class="alert alert-error" id="alert_sendCloneError" style="display: none">
						<button class="close fui-cross" data-dismiss="alert"></button>
					  	<h4>Error:</h4>
					  	<div class="alertContent"></div>
					</div>

					<div class="form-group">
					    <label for="field_email">Please provide an email address below:</label>
						<input type="email" class="form-control" placeholder="you@something.com" name="field_email" id="field_email" />
					</div>
					
					<div class="form-group">
					    <label for="field_email">Please provide the email subject:</label>
						<input type="text" class="form-control" placeholder="Email subject" value="SiteCloner: clone attached to this email" name="field_subject" id="field_subject" />
					</div>
					
					<div class="form-group">
					    <label for="textarea_message">Provide an optional message below:</label>
						<textarea class="form-control" placeholder="Your email message" name="textarea_message" id="textarea_message" rows="4"></textarea>
					</div>

	      	  	</div><!-- /.modal-body -->

				<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fui-cross-circle"></span> Cancel & Close</button>
					<button type="submit" class="btn btn-primary disabled" id="button_sendByEmailSubmit"><span class="fui-check-circle"></span> <span class="buttonText">Send By Email</span></button>
	      	  	</div>
				
				</form>

			</div><!-- /.modal-content -->

		</div><!-- /.modal-dialog -->

	</div><!-- /.modal -->
	
	<div class="modal fade" id="modal_cloneLog" tabindex="-1" role="dialog" aria-labelledby="modal_cloneLog" aria-hidden="true">

		<div class="modal-dialog modal-lg">
										
			<div class="modal-content">

				<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title"><span class="fui-document"></span> Clone Log</h4>
				</div>

				<div class="modal-body">
					
					<div class="alert alert-info" style="display: none" id="alert_cloneLogError">
					  	<button class="close fui-cross" data-dismiss="alert"></button>
					  	<h4>Error:</h4>
					  	<div class="alertContent"></div>
					</div>
					
					<div class="cloneLog" id="div_cloneLog"></div>

	      	  	</div><!-- /.modal-body -->

				<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fui-cross-circle"></span> Cancel & Close</button>
	      	  	</div>
				
			</div><!-- /.modal-content -->

		</div><!-- /.modal-dialog -->

	</div><!-- /.modal -->
	
	
	<?php $this->load->view('partials/newclone.php');?>
	
	
	<div class="modal fade" id="modal_uploadClone" tabindex="-1" role="dialog" aria-labelledby="modal_uploadClone" aria-hidden="true">

		<div class="modal-dialog">
			
			<form action="<?php echo site_url('clones/upload')?>" method="post" id="form_uploadClone">
				
			<input type="hidden" name="crawlID" value="" id="field_uploadCrawlID">
										
			<div class="modal-content">

				<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title"><i class="fa fa-upload"></i> Upload Clone</h4>
				</div>

				<div class="modal-body">
					
					<!-- Nav tabs -->
					<ul class="nav nav-tabs nav-append-content" role="tablist">
						<li role="presentation" class="active"><a href="#tab_ftp" aria-controls="tab_ftp" role="tab" data-toggle="tab">Upload Via FTP</a></li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">
							
					    <div role="tabpanel" class="tab-pane active" id="tab_ftp">
							
							<div class="alert alert-info">
								<button class="close fui-cross" data-dismiss="alert"></button>
								<h4>Please note</h4>
								<p>
									Uploading your cloned site via FTP can take a <b>long time!</b> Navigation away from this page before the upload has completed can result in the uploading being cancelled.
								</p>
							</div>
							
							<div class="alert alert-danger" style="display: none">
								<button class="close fui-cross" data-dismiss="alert"></button>
							  	<h4>Error</h4>
							  	<div class="alertContent"></div>
							</div>
						
							<div class="alert alert-success" style="display: none">
								<button class="close fui-cross" data-dismiss="alert"></button>
							  	<h4>Yay!</h4>
							  	<div class="alertContent"></div>
							</div>
							
							<div class="form-group">
								<input type="text" class="form-control" placeholder="FTP server" name="ftp_server" id="ftp_server" />
							</div>
																				
							<div class="form-group">
								<input type="text" class="form-control" placeholder="FTP user" name="ftp_user" id="ftp_user" />
							</div>
								
							<div class="form-group">
								<input type="password" class="form-control" placeholder="FTP password" name="ftp_password" id="ftp_password" />
							</div>
							
							<div class="form-group">
								<input type="text" class="form-control" placeholder="FTP path - / by default, leave empty if in doubt" name="ftp_path" id="ftp_path" />
							</div>
					
							<div class="form-group">
								<input type="text" class="form-control" placeholder="FTP port - 21 by default, leave empty if in doubt" name="ftp_port" id="ftp_port" />
							</div>
						
					        <label class="checkbox" for="ftp_passive">
								<input type="checkbox" value="yes" name="ftp_passive" id="ftp_passive" data-toggle="checkbox" checked>
								Passive mode
							</label>
						
							
					    </div><!-- /.tab-pabe -->
						
					</div><!-- /.tab-content -->
					
	      	  	</div><!-- /.modal-body -->

				<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fui-cross-circle"></span> Cancel & Close</button>
					<button type="submit" class="btn btn-primary" id="button_uploadClone"><span class="fui-check"></span> <span class="buttonText">Upload Clone</span></button>
	      	  	</div>
				
				</form>
				
			</div><!-- /.modal-content -->

		</div><!-- /.modal-dialog -->

	</div><!-- /.modal -->
	
	<?php $this->load->view('partials/account');?>
	
<?php $this->load->view('shared/footer');?>