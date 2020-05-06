    
	<footer>
		
		<div class="container">
			
			<div class="row">
				
				<div class="col-md-12">
					
					<hr class="dashed">
					
				</div>
				
			</div><!-- /.row -->
			
		</div><!-- /.container -->
	
	</footer>
	
	<!-- jQuery (necessary for Flat UI's JavaScript plugins) -->
    <script src="<?php echo base_url('js/vendor/jquery.min.js')?>"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo base_url('js/flat-ui-pro.js')?>"></script>
	<script src="<?php echo base_url('js/application.js')?>"></script>
	<script src="<?php echo base_url('js/jquery.dataTables.min.js')?>"></script>
	<script src="<?php echo base_url('js/dataTables.responsive.min.js')?>"></script>
	<script src="<?php echo base_url('js/dataTables.bootstrap.js')?>"></script>
	<script src="<?php echo base_url('js/custom.js')?>"></script>
	<script>
	
	var siteUrl = "<?php echo site_url();?>";
	var baseUrl = "<?php echo base_url();?>";
	
	</script>
	<?php if( isset($clones) && $clones && $page == 'Clones' ):?>
	<?php foreach( $clones as $clone ):?>
		<?php if( $clone->status == 'building' ):?>
		<script>

		$(function(){
			
			$('#cloningProgressModal_<?php echo $clone->crawl_id?>').modal('show');
			
			<?php if( $this->session->flashdata('continue') != '' && $this->session->flashdata('continue') == $clone->crawl_id ):?>
			$.ajax({
				url: "<?php echo site_url('clones/buildClone/'.$clone->crawl_id)."/true/"?>",
				method: 'get',
				timeout: 0
			}).success(function(ret){

				//alert(ret);

			});
			<?php else:?>
			$.ajax({
				url: "<?php echo site_url('clones/buildClone/'.$clone->crawl_id)?>",
				method: 'get',
				timeout: 0
			}).success(function(ret){

				//alert(ret);

			});
			<?php endif;?>

			setTimeout(function(){checkCloningProgress(<?php echo $clone->crawl_id;?>)}, 2500);

		})

		</script>
		<?php endif;?>
	<?php endforeach?>
	<?php endif;?>
	<?php if( isset($page) && $page == 'Clones' ):?>
	<script>
	$(function(){
		
		var cTable = $('#clonesTable').dataTable({
			"columnDefs": [
			    { "orderable": false, "targets": [0,6] }
			],
			"order": [[ 2, "asc" ]]
		});
				
		delButtonHTML = $('<div class="pull-left" style="margin-right: 40px"><span class="small">&nbsp;&nbsp;With selected:&nbsp;</span><button type="submit" class="btn btn-danger disabled btn-sm" id="delSites"><span class="fui-cross-circle"></span> Delete</button></div>')
		
		$('#clonesTable_wrapper .row > .col-sm-6:first').prepend( delButtonHTML );
		
		$('#clonesTable_filter input').attr('placeholder', 'Search...')
		
	})
	</script>
	<?php endif;?>
	<?php if( isset($page) && $page == 'Sites' ):?>
	<script>
	$(function(){
		
		var cTable = $('#sitesTable').dataTable({
			"columnDefs": [
			    { "orderable": false, "targets": [0,5] }
			],
			"order": [[ 1, "asc" ]]
		});
				
		delButtonHTML = $('<div class="pull-left" style="margin-right: 40px"><span class="small">&nbsp;&nbsp;With selected:&nbsp;</span><button type="submit" class="btn btn-danger disabled btn-sm" id="delSites"><span class="fui-cross-circle"></span> Delete</button></div>')
		
		$('#sitesTable_wrapper .row > .col-sm-6:first').prepend( delButtonHTML );
		
		$('#sitesTable_filter input').attr('placeholder', 'Search...')
		
	})
	</script>
	<?php endif;?>
</body>
</html>
