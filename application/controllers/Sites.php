<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sites extends CI_Controller {
	
	function __construct() {
		
		parent::__construct();

		$this->load->helper('url');
		$this->load->library(array('crawler','session', 'ion_auth'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
		
	}

	public function index() {
		
		$this->data['page'] = "Sites";
		
		//get all sites
		$this->data['sites'] = $this->crawlmodel->getSites();
		
		$this->load->view('sites', $this->data);
	
	}
	
	
	public function create() {
		
		if( !isset( $_POST['field_siteUrl'] ) || $_POST['field_siteUrl'] == '' ) {//missing URL
			
			$this->session->set_flashdata('error', 'Please make sure you specify a URL for your new site');
			
			redirect('/sites/', 'location');
			
		} else {
			
			$url = $_POST['field_siteUrl'];
			
			
			//remove http://
			if (substr($url, 0, strlen("http://")) == "http://") {
				$url = substr($url, strlen("http://"));
			}
		
			//remove https://
			if (substr($url, 0, strlen("https://")) == "https://") {
				$url = substr($url, strlen("https://"));
			}
		
			//remoce //
			if (substr($url, 0, strlen("//")) == "//") {
				$url = substr($url, strlen("//"));
			}
			
			//double check the URL again
		    $contents = @file_get_contents("http://".$url, false, null, -1);
	    
		    if ( !$contents || empty($contents)) {
			
				$this->session->set_flashdata('error', 'The URL you specified can not be read. Please try again.');
			
				redirect('/sites/', 'location');
	    
			} else {
			
		        $siteID = $this->crawlmodel->createSite($url);
				
				if( $siteID ) {
					
					$this->session->set_flashdata('success', 'Your new site was added successfully. Yay!');
					
					redirect('/sites/', 'location');
					
				}
			
			}
			
		}
		
	}
	
	
	public function checkUrl() {
		
		$url = (isset($_POST['field_siteUrl']))? "http://".$_POST['field_siteUrl'] : 'dummy';
		
		
		$return = array();
		
		$opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
		$context = stream_context_create($opts);
						
	    $contents = file_get_contents($url, false, $context, -1);
	    
	    if ( !$contents || empty($contents)) {
			
	        $return['code'] = 0;
			$return['message'] = "Bad URL, please try again.";
	    
		} else {
			
	        $return['code'] = 1;
			$return['message'] = "URL is good, please continue below";
			
		}
		
		echo json_encode( $return );
		
	}
	
	
	public function updateSettings() {
						
		if( !isset($_POST['siteID']) || $_POST['siteID'] == '' || $_POST['siteID'] == 'undefined' ) {
			
			$this->session->set_flashdata('error', 'We could not save your site settings. Please reload the page and try again.');
			
			redirect('/sites/', 'location');
			
		} else {
			
			//update the site
			$this->crawlmodel->updateSite($_POST['siteID'], $_POST);
			
			$this->session->set_flashdata('success', 'Yay! We have saved your side settings.');
			
			redirect('/sites/', 'location');
			
		}
		
	}
	
	
	public function loadSiteData($siteID = '') {
				
		$return = array();
				
		if( $siteID == '' || $siteID == 'undefined' ) {
			
	        $return['code'] = 0;
			$return['message'] = "Site ID is missing. Please reload and try again.";
			
		} else {
						
			$site = $this->crawlmodel->getSiteDetails($siteID);
			
			if( $site ) {
				
				$return['code'] = 1;
				$return['message'] = $this->load->view('partials/sitesettings', array('site'=>$site), true);
				
			} else {
				
		        $return['code'] = 0;
				$return['message'] = "We couldn't retrieve the site's details. Please reload and try again.";
				
			}
			
		}
		
		echo json_encode( $return );
		
	}
	
	
	public function removeSites() {
		
		if( !isset($_POST['toDel']) || !is_array($_POST['toDel']) ) {
			
			$this->session->set_flashdata('error', 'Please select one or more sites to remove');
			
			redirect('/sites/', 'location');
			
		} else {
			
			foreach( $_POST['toDel'] as $siteID ) {
				
				$this->crawlmodel->deleteSite($siteID);
				
			}
			
			$this->session->set_flashdata('success', 'The selected sites we deleted successfully.');
			
			redirect('/sites/', 'location');
			
		}
		
	}
	
}
