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
		<div class="alert alert-danger">
			<button class="close fui-cross" data-dismiss="alert"></button>
		  	<p style="line-height: 24px">
				<span class="fui-check-circle text-primary pull-left" style="font-size: 22px; margin-right: 5px"></span><?php echo $this->session->flashdata('success');?>
			</p>
		</div>
		<?php endif;?>
		
		<form action="<?php echo site_url('sites/removeSites')?>" method="post">
		<div class="row margin-bottom-30">
						
			<div class="col-md-12">
				
				<div class="clearfix">
					<a class="btn btn-primary btn-md btn-wide pull-right" href="#newSiteModal" data-toggle="modal"><span class="fui-plus-circle"></span> Add New Site</a>
				</div>
				
				<hr class="dashed">
				
			</div><!-- /.col -->
			
		</div><!-- /.row -->
		
		<div class="row">
			
			<div class="col-md-12">
								
				<?php if( $sites ):?>
					<table class="table table-bordered table-hover sitesTable" id="sitesTable">
	                	<thead>
							<tr>
	                    		<th class="text-center"><label class="checkbox no-label toggle-all" for="checkbox-table-0"><input type="checkbox" value="" id="checkbox-table-0" data-toggle="checkbox"></label></th>
	                    		<th>Domain</th>
	                    		<th>URL</th>
	                    		<th>Added</th>
								<th>Last Clone</th>
								<th></th>
	                  	  	</tr>
	                	</thead>
	                	<tfoot>
							<tr>
	                    		<th class="text-center"><label class="checkbox no-label toggle-all" for="checkbox-table-0"><input type="checkbox" value="" id="checkbox-table-0" data-toggle="checkbox"></label></th>
	                    		<th>Domain</th>
	                    		<th>URL</th>
	                    		<th>Added</th>
								<th>Last Clone</th>
								<th></th>
	                  	  	</tr>
	                	</tfoot>
	                	<tbody>
							<?php foreach( $sites as $site ):?>
							<tr>
	                    	  	<td class="text-center"><label class="checkbox no-label" for="checkbox-table-<?php echo $site->site_id?>"><input type="checkbox" name="toDel[]" value="<?php echo $site->site_id?>" id="checkbox-table-<?php echo $site->site_id?>" data-toggle="checkbox"></label></td>
	                    	 	<td><div class="urlWrapper"><?php echo $site->site_domain?></div></td>
	                    	  	<td><div class="urlWrapper"><?php echo $site->site_url?></div></td>
	                    	  	<td><?php echo ( $site->sites_created != 0 )? date("Y-m-d", $site->sites_created) : 'NA';?></td>
							  	<td><?php echo ( $site->sites_lastcrawl != 0 )? date("Y-m-d", $site->sites_lastcrawl) : 'NA';?></td>
								<th class="text-right">
									<a href="#siteSettings" title="Configure the settings for this site" data-toggle="tooltip" class="link_siteSettings" data-siteid="<?php echo $site->site_id?>"><i class="fa fa-cog"></i></a>&nbsp;&nbsp;
									<a href="<?php echo site_url('clones/build/'.$site->site_id);?>" title="Create a new clone for this site" data-toggle="tooltip"><i class="fa fa-files-o"></i></a>
								</th>
	                  		</tr>
							<?php endforeach;?>
						</tbody>
	   		 		</table>
				<?php else:?>
				<div class="alert alert-info">
					<button class="close fui-cross" data-dismiss="alert"></button>
				  	<h4>No Sites</h4>
				  	<p>
				  		Looks you haven't added any sites yet. Click the "Add New Site" button to start adding sites; you will need to sites before you can start creating clones.
				  	</p>
				</div>
				<?php endif;?>
				
			</div><!-- /.col -->
			
		</div><!-- /.row -->
		</form>
		
    </div><!-- /.container -->
	
	<!-- new site modal -->
	<div class="modal fade" id="newSiteModal" tabindex="-1" role="dialog" aria-labelledby="newSiteModal" aria-hidden="true">
		
		<div class="modal-dialog">
	    	
			<form method="post" action="<?php echo site_url('sites/create')?>">
			
			<div class="modal-content">
	      		
				<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title" id="myModalLabel"><span class="fui-plus-circle"></span> Add New Site</h4>
				</div>
	      	  	
				<div class="modal-body">
					
					<div class="form-group">
					    <label for="exampleInputEmail1">Please provide the URL of the site you'd like to add (after entering the URL, you'll need to click the verify button first)</label>
						<div class="input-group">
							<span class="input-group-addon">http://</span>
							<input type="text" class="form-control" placeholder="mywebsite.com" name="field_siteUrl" id="field_siteUrl" />
							<div class="input-group-btn">
								<button type="button" class="btn btn-default" id="button_verifyUrl"> <span class="c">verify</span> <span class="fui-arrow-right"></span></button>
							</div>
						</div>
					</div>
					
					<div class="alert" id="alert_urlCheck" style="display: none">
						<button class="close fui-cross" data-dismiss="alert"></button>
					  	<p></p>
					</div>					
					
	      	  	</div>
	      	  	
				<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fui-cross-circle"></span> Cancel & Close</button>
	        		<button type="submit" class="btn btn-primary disabled" id="button_newSiteSubmit"><span class="fui-check-circle"></span> Continue, Create Site</button>
	      	  	</div>
	    	
			</div><!-- /.modal-content -->
						
			</form>
	  	
		</div><!-- /.modal-dialog -->
	
	</div><!-- /.modal -->
	
	
	<!-- site settings modal -->
	<div class="modal fade" id="siteSettings" tabindex="-1" role="dialog" aria-labelledby="siteSettings" aria-hidden="true">
		
		<div class="modal-dialog">
	    	
			<form action="<?php echo site_url('sites/updateSettings');?>" method="post">
			
			<div class="modal-content">
	      		
				<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        		<h4 class="modal-title"><span class="fui-gear"></span> Site Settings</h4>
				</div>
	      	  	
				<div class="modal-body">
					
					<div class="alert alert-error" style="display: none" id="alert_settingsLoadError">
						<button class="close fui-cross" data-dismiss="alert"></button>
					  	<h4>Error</h4>
					  	<p></p>
					</div>
					
					<div id="div_siteSettings"></div>
					
	      	  	</div><!-- /.modal-body -->
	      	  	
				<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fui-cross-circle"></span> Cancel & Close</button>
	        		<button type="submit" class="btn btn-primary" id=""><span class="fui-check-circle"></span> Save Settings</button>
	      	  	</div>
					    	
			</div><!-- /.modal-content -->
			
			</form>
				  	
		</div><!-- /.modal-dialog -->
	
	</div><!-- /.modal -->
	
	<?php $this->load->view('partials/account');?>
	
<?php $this->load->view('shared/footer');?>