<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {
	
	function __construct() {
		
		parent::__construct();

		$this->load->helper(array('url', 'form'));
		$this->load->library(array('crawler','session', 'ion_auth', 'form_validation'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
		
	}

	public function edit() {
		
		$this->form_validation->set_rules('field_accountEmail', 'Email Address', 'required|valid_email');
		$this->form_validation->set_rules('field_accountPasswordRepeat', 'Password Confirmation', 'required|matches[field_accountPassword]');
		$this->form_validation->set_rules('field_accountPassword', 'Password', 'required');
		$this->form_validation->set_rules('field_accountFirstname', 'First name', 'required');
		$this->form_validation->set_rules('field_accountLastname', 'Last name', 'required');
		
		if ($this->form_validation->run() == FALSE) {
			
			$return['code'] = 0;
			$return['message'] = 'There were some issues, please see the details below:<br>'.validation_errors();
			
			die( json_encode($return) );
		
		} else {
		
			$user = $this->ion_auth->user()->row();
			
			$data = array(
				'email' => $_POST['field_accountEmail'],
				'password' => $_POST['field_accountPassword'],
				'first_name' => $_POST['field_accountFirstname'],
				'last_name' => $_POST['field_accountLastname']
			);
			
			if( $this->ion_auth->update($user->id, $data) ) {
				
				$return['code'] = 1;
				$return['message'] = "Your account details were updated successfully.";
			
				die( json_encode($return) );
				
			} else {
				
				$return['code'] = 0;
				$return['message'] = "Unfortunately, we couldn't update your account details. Please reload the page and try again.";
			
				die( json_encode($return) );
				
			}
			
			
		}
		
		
	
	}
	
}
