$(function(){
	
	$("select").select2({dropdownCssClass: 'dropdown-inverse'});
	
	$('#sitesTable :checkbox').on('click', function () {
		
		if( $(this).closest('.table').find('tbody :checkbox:checked').size() > 0 ) {
			
			$('#delSites').removeClass('disabled');
			
		} else {
			
			$('#delSites').addClass('disabled');
			
		}
		
	})
	
	$('#clonesTable :checkbox').on('click', function () {
		
		if( $(this).closest('.table').find('tbody :checkbox:checked').size() > 0 ) {
			
			$('#delSites').removeClass('disabled');
			
		} else {
			
			$('#delSites').addClass('disabled');
			
		}
		
	})
	
	
	$('#newSiteModal').on('shown.bs.modal', function (event) {
		
		$('input#field_siteUrl').focus();
		
	})
	
	
	$('#field_siteUrl').on('blur', function(){
		
		$('#field_siteUrl').val( $('#field_siteUrl').val().replace('http://', '') );
		$('#field_siteUrl').val( $('#field_siteUrl').val().replace('https://', '') )
		
	})
	
	var _button;
	
	//verify URL
	$('#button_verifyUrl').click(function(){
		
		$('#newSiteModal .alert').hide();
		
		_button = $(this)
		_button.find('.c').text('verifying...');
		
		theData = $('#newSiteModal form').serialize();
				
		$.ajax({
			url: siteUrl+"/sites/checkUrl",
			method: 'post',
			dataType: 'json',
			data: theData
		}).done(function(ret){
			
			_button.find('.c').text('verify');
			
			if( ret.code == 0 ) {
				
				$('#newSiteModal #alert_urlCheck').removeClass('alert-success').addClass('alert-danger').find('p').text( ret.message );
				$('#newSiteModal #alert_urlCheck').fadeIn();
				
				$('#newSiteModal #button_newSiteSubmit').addClass('disabled');
				
			} else if( ret.code == 1 ) {
				
				$('#newSiteModal #alert_urlCheck').removeClass('alert-danger').addClass('alert-success').find('p').text( ret.message );
				$('#newSiteModal #alert_urlCheck').fadeIn();
				
				$('#newSiteModal #button_newSiteSubmit').removeClass('disabled');
				
			}
			
		})
		
	})
	
	
	//new site form
	$('#newSiteModal form').submit(function(){
		
		if( $('input#field_siteUrl').val() == '' ) {
			
			alert('Please enter a URL')
			//return false;
			
		} else {
		
			return true;
		
		}
		
	})
	
	
	$('select#select_sites').change(function(){
		
		if( $(this).val() != '' ) {
			
			$('#button_newClonePopup').removeClass('disabled');
			$('#form_newClone').attr('action', $('#form_newClone').attr('data-link')+"/"+$(this).val())
			
		} else {
			
			$('#button_newClonePopup').addClass('disabled');
			('#button_newClone').attr('action', '');
			
		}
		
	});
	
	$('#toggleEmail').on('switchChange.bootstrapSwitch', function(event, state) {
		
		if( state ) {
			$(this).closest('form').find('.toggleEmail').attr('disabled', false);
		} else {
			$(this).closest('form').find('.toggleEmail').attr('disabled', true);
		}
  	  
	});
	
	$('#toggleEmail2').on('switchChange.bootstrapSwitch', function(event, state) {
		
		if( state ) {
			$(this).closest('form').find('.toggleEmail2').attr('disabled', false);
		} else {
			$(this).closest('form').find('.toggleEmail2').attr('disabled', true);
		}
  	  
	});
	
	$('#toggleFTP').on('switchChange.bootstrapSwitch', function(event, state) {
		
		if( state ) {
			$(this).closest('form').find('.toggleFTP').attr('disabled', false);
		} else {
			$(this).closest('form').find('.toggleFTP').attr('disabled', true);
		}
  	  
	});
	
	
	//new clone button
	$('#button_newClone').click(function(){
		
		$(this).addClass('disabled')
		
	})
	
	
	//site settings popup
	$('a.link_siteSettings').click(function(e){
		
		e.preventDefault();
		
		//load data
		$.ajax({
			url: siteUrl+"/sites/loadSiteData/"+$(this).attr('data-siteid'),
			method: 'get',
			dataType: 'json'
		}).done(function(ret){
			
			if( ret.code == 0 ) {
				
				$('#alert_settingsLoadError').show().find('p').text( ret.message )
				
			} else {
				
				$('#alert_settingsLoadError').hide();
				
				$('#div_siteSettings').html( ret.message );
				
				//create the pretty tags input element
				$("#div_siteSettings #exludekeywords").tagsinput();
				
				//activate tabs
				$('#siteSettingsTabs a').click(function (e) {
				  e.preventDefault()
				  $(this).tab('show')
				})
				$('#siteSettingsTabs a:first').tab('show');
				
			}
			
		})
		
		$('#siteSettings').modal('show');
		
	});
	
	
	
	//cloning progress modal popup
	$('a.checkProgressLink').click(function(e){
		
		e.preventDefault();
		
		crawlID = $(this).attr('data-crawlid');
		
		$('#cloningProgressModal_'+crawlID).modal('show');
		
	});
	
	
	//send clone by email
	
	var _buttonSendByEmail;
	
	$('.sendByEmailLink').on('click', function(e){
		
		e.preventDefault();
		
		_buttonSendByEmail = $(this);
		
		$('#modal_sendCloneByEmail').modal('show');
		
	})

	
	$('input#field_email').keyup(function(){
		
		var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		
		if( re.test($(this).val()) ) {
			
			$(this).parent().addClass('has-success').removeClass('has-error');
			
			$('#button_sendByEmailSubmit').removeClass('disabled');
			
		} else {
			
			$(this).parent().removeClass('has-success').addClass('has-error');
			
			$('#button_sendByEmailSubmit').addClass('disabled');
			
		}
		
	});
	
	$('#modal_sendCloneByEmail').on('show.bs.modal', function (event) {
				
	  	$('input#input_siteID').val( _buttonSendByEmail.attr('data-siteid') );
		$('input#input_timestamp').val( _buttonSendByEmail.attr('data-timestamp') );
	  
	})
		
	$('form#form_sendCloneByEmail').submit(function(){
		
		var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		
		if( !re.test($('input#field_email').val()) ) {
			
			alert('Please enter a valid email address');
			return false;
			
		}
		
		theData = $('form#form_sendCloneByEmail').serialize();
		
		//button status
		$('#button_sendByEmailSubmit .buttonText').text('Sending email...');
		$('#button_sendByEmailSubmit').addClass('disabled');
		
		//hide alerts
		$('#alert_sendCloneSuccess, #alert_sendCloneError').hide();
				
		$.ajax({
			url: $(this).attr('action'),
			dataType: 'json',
			method: 'post',
			data: theData
		}).done(function(ret){
			
			$('#button_sendByEmailSubmit').removeClass('disabled');
			$('#button_sendByEmailSubmit .buttonText').text('Send By Email');
			
			if( ret.code == 0 ) {
				
				$('#alert_sendCloneSuccess').hide();
				$('#alert_sendCloneError .alertContent').html( ret.message );
				$('#alert_sendCloneError').show();
				
			} else if( ret.code == 1 ) {
				
				$('#alert_sendCloneError').hide();
				$('#alert_sendCloneSuccess .alertContent').html( ret.message );
				$('#alert_sendCloneSuccess').show();
				
			}
			
		});
		
		return false;
		
	});
	
	
	
	//cancel cloning process
	
	$('a.button_cancelCrawl').click(function(e){
				
		crawlID = $(this).attr('data-crawlid');
		
		_button = $(this);
		_button.addClass('disabled').find('.buttonText').text('Cancelling cloning process...');
		
		e.preventDefault();
		
		$.ajax({
			url: siteUrl+'/clones/cancelCloning/'+crawlID,
			method: 'post',
			dataType: 'json'
		}).done(function(ret){
						
			if( ret.code == 0 ) {
				
				_button.removeClass('disabled').find('.buttonText').text('Cancel Cloning Process');
				alert( ret.message );
				
			} else if( ret.code == 1 ) {
				
				_button.find('.buttonText').text( 'Cancel Cloning Process' );
				
			}
			
		})
		
	});
	
	
	
	//upload clones
	$('a.uploadCloneLink').click(function(e){
		
		e.preventDefault();
		
		$('input#field_uploadCrawlID').val( $(this).attr('data-crawlid') )
		
		$('#modal_uploadClone').modal('show');
		
	});
	
	
	$('form#form_uploadClone').submit(function(){
		
		$('#modal_uploadClone .alert-error, #modal_uploadClone .alert-success, #modal_uploadClone .alert-info').fadeOut();
		
		//basic form checking
		
		goAhead = true;
		
		if( $('#ftp_server').val() == '' ) {
			
			$('#ftp_server').parent().addClass('has-error');
			goAhead = false;
			
		} else {
			
			$('#ftp_server').parent().removeClass('has-error');
			goAhead = true;
			
		}
		
		if( $('#ftp_user').val() == '' ) {
			
			$('#ftp_user').parent().addClass('has-error');
			goAhead = false;
			
		} else {
			
			$('#ftp_user').parent().removeClass('has-error');
			goAhead = true;
			
		}
		
		if( $('#ftp_password').val() == '' ) {
			
			$('#ftp_password').parent().addClass('has-error');
			goAhead = false;
			
		} else {
			
			$('#ftp_password').parent().removeClass('has-error');
			goAhead = true;
			
		}
		
		if( goAhead ) {
			
			$('#button_uploadClone').addClass('disabled').find('.buttonText').text('Uploading clone; please wait...');
			
			$.ajax({
				url: $('form#form_uploadClone').attr('action'),
				method: 'post',
				dataType: 'json',
				data: $('form#form_uploadClone').serialize()
			}).done(function(ret){
				
				$('#modal_uploadClone .alert-error, #modal_uploadClone .alert-success, #modal_uploadClone .alert-info').fadeOut();
								
				$('#button_uploadClone').removeClass('disabled').find('.buttonText').text('Upload Clone');
				
				if( ret.code == 0 ) {
					
					$('#modal_uploadClone .alert-danger').show().find('.alertContent').html( ret.message );
					
				} else {
					
					$('#modal_uploadClone .alert-success').show().find('.alertContent').html( ret.message );
					
				}
				
			})
			
		}
		
		
		return false;
		
	});
	
	
	//update account
	$('form#form_account').on('submit', function(){
		
		allGood = true;
		
		if( $('#field_accountEmail').val() == '' ) {
			
			$('#field_accountEmail').parent().addClass('has-error');
			allGood = false;
			
		} else {
			
			$('#field_accountEmail').parent().removeClass('has-error');
			allGood = true;
			
		}
		
		if( $('#field_accountPassword').val() == '' ) {
			
			$('#field_accountPassword').parent().addClass('has-error');
			allGood = false;
			
		} else {
			
			$('#field_accountPassword').parent().removeClass('has-error');
			allGood = true;
			
		}
		
		if( allGood ) {
			
			$('#button_saveAccount').addClass('disabled').find('.buttonText').text('Saving details...');
									
			$.ajax({
				url: $('form#form_account').attr('action'),
				method: 'post',
				dataType: 'json',
				data: $('form#form_account').serialize()
			}).done(function(ret){
				
				$('#button_saveAccount').removeClass('disabled').find('.buttonText').text('Update Account');
				
				if( ret.code == 0 ) {
					
					$('#alert_accountSuccess').hide();
					
					$('#alert_accountError').find('.alertContent').html( ret.message );
					$('#alert_accountError').fadeIn(500);
					
					setTimeout(function(){ $('#alert_accountError').fadeOut() }, 5000);
					
				} else if( ret.code == 1 ) {
					
					$('#alert_accountError').hide();
					
					$('#alert_accountSuccess').find('.alertContent').html( ret.message );
					$('#alert_accountSuccess').fadeIn(500);
					
					setTimeout(function(){ $('#alert_accountSuccess').fadeOut() }, 5000);
					
				}
 				
			})
			
		}
		
		return false;
		
	})
	
})


function checkCloningProgress(cloneID) {
	
	$.ajax({
		url: siteUrl+"/clones/checkCrawlProgress/"+cloneID,
		method: 'get',
		dataType: 'json',
		timeout: 0
	}).success(function(ret){
		
		$('#cloningProgress_'+cloneID).append( $(ret.progress) ).scrollTop( $('#cloningProgress_'+cloneID).prop("scrollHeight") )
		
		if( ret.status == 'building' ) {
			
			setTimeout(function(){checkCloningProgress(cloneID)}, 1000);
			
		} else if( ret.status == 'complete' ) {
						
			$('#cloningProgress_'+cloneID).append( $("<br><b>All done :)</b>") ).scrollTop( $('#cloningProgress_'+cloneID).prop("scrollHeight") );
			
			$('[data-crawlid="'+cloneID+'"].checkProgressLink').hide();
			$('[data-crawlid="'+cloneID+'"].statusComplete').show();
			
			$('a.button_cancelCrawl[data-crawlid="'+cloneID+'"]').removeClass('btn-danger').addClass('btn-primary').find('.buttonText').text('Browse the new clone');
			$('a.button_cancelCrawl[data-crawlid="'+cloneID+'"]').attr('href', baseUrl+"sites/"+$('a.button_cancelCrawl[data-crawlid="'+cloneID+'"]').attr('data-siteid')+"/"+$('a.button_cancelCrawl[data-crawlid="'+cloneID+'"]').attr('data-timestamp')+"/" );
			$('a.button_cancelCrawl[data-crawlid="'+cloneID+'"]').attr('target', '_blank');
			$('a.button_cancelCrawl[data-crawlid="'+cloneID+'"]').unbind('click');
			
			//hide the continue crawl icon
			$('[data-crawlid="'+cloneID+'"].continueCrawl').hide();
						
		} else if( ret.status == 'cancelled' ) {
				
			$('#cloningProgress_'+cloneID).scrollTop( $('#cloningProgress_'+cloneID).prop("scrollHeight") );
			
			$('[data-crawlid="'+cloneID+'"].checkProgressLink').hide();
			$('[data-crawlid="'+cloneID+'"].statusCancelled').show();
			
			//show the continue crawl icon
			$('[data-crawlid="'+cloneID+'"].continueCrawl').show();
			
			$('[data-crawlid="'+cloneID+'"].button_cancelCrawl').fadeOut(function(){
				$('[data-crawlid="'+cloneID+'"].button_continueCrawl').show();
			});
			
			
		
		} else if( ret.status == 'timed out' ) {
			
			$('#cloningProgress_'+cloneID).scrollTop( $('#cloningProgress_'+cloneID).prop("scrollHeight") );
			
			$('[data-crawlid="'+cloneID+'"].checkProgressLink').hide();
			$('[data-crawlid="'+cloneID+'"].statusTimedout').show();
			
			//show the continue crawl icon
			$('[data-crawlid="'+cloneID+'"].continueCrawl').show();
			
			$('[data-crawlid="'+cloneID+'"].button_cancelCrawl').fadeOut(function(){
				$('[data-crawlid="'+cloneID+'"].button_continueCrawl').show();
			});
			
		}
		
	});
	
}