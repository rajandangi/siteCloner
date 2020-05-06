<input type="hidden" name="siteID" id="siteID" value="<?php echo $site->site_id?>">

<!-- Nav tabs -->
<ul class="nav nav-tabs nav-append-content" role="tablist">
	<li role="presentation" class="active"><a href="#tab_exlcudeKeywords" aria-controls="tab_exlcudeKeywords" role="tab" data-toggle="tab">Exclude Keyword</a></li>
    <li role="presentation"><a href="#tab_timeLimit" aria-controls="tab_timeLimit" role="tab" data-toggle="tab">Timeout Limit</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	
    <div role="tabpanel" class="tab-pane active" id="tab_exlcudeKeywords">
		<div class="alert alert-info">
			<button class="close fui-cross" data-dismiss="alert"></button>
			<h4>Info</h4>
			<p>
				URLs containing one ore more of the keywords below, will be excluded from the cloning process.
			</p>
		</div>
							
		<div class="tagsinput-primary">
			<input name="exludekeywords" id="exludekeywords" class="tagsinput" value="<?php echo $site->sites_excludekeywords;?>" data-role="tagsinput" placeholder="Type here, press ENTER when done" />
		</div>
    </div>
	
    <div role="tabpanel" class="tab-pane" id="tab_timeLimit">
		<div class="alert alert-info">
			<button class="close fui-cross" data-dismiss="alert"></button>
			<h4>Timeout Info</h4>
			<p>
				When cloning large sites; SiteCloner can possibly ran into certain server issues (with limitations on memory, time, etc). To deal with this possible issue, SiteCloner uses a Timeout Limit. This is the maximum number of seconds the cloning process will run. Don't worry, if the limit is exceeded, the cloning process simply stops and can be continued at any given time.
			</p>
			<p>
				We have set the default to <b>1800 seconds (30 minutes)</b> as we consider this save on the most basic shared hosting environments. Feel free to play around with the number though, as in many situations it would be save to set it to a higher value.
			</p>
		</div>
		
		<div class="form-group">
			<input type="text" class="form-control" placeholder="Timeout limit in seconds" name="field_timeout" id="field_timeout" value="<?php echo $site->sites_timeout?>" />
		</div>
		
    </div><!-- /.tab-pane -->
</div><!-- /.tab-content -->
