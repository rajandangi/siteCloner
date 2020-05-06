<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clones extends CI_Controller {
	
	function __construct() {
		
		parent::__construct();

		$this->load->helper(array('url', 'form'));
		$this->load->library('crawler');
						
	}

	public function index() {
		
		$this->load->library(array('crawler','session', 'ion_auth'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
				
		$this->data['page'] = "Clones";
		
		//get all clones
		$this->data['clones'] = $this->crawlmodel->getCrawls();
		
		//get all sites
		$this->data['sites'] = $this->crawlmodel->getSites();
		
		$this->load->view('clones', $this->data);
	
	}
	
	
	public function build( $siteID, $crawlID = false ) {
		
		$this->load->library(array('crawler','session', 'ion_auth'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
		
		
		$this->page['clones'] = "Clones";
		
		
		//create a new crawl/clone
		
		if( !$crawlID ) {//new crawl
		
			$crawlID = $this->crawlmodel->createCrawl($siteID, time());
		
			if( $crawlID ) {
			
				$this->session->set_flashdata('crawlID', $crawlID);
				$this->session->set_flashdata('siteID', $siteID);
				
				
				//do we have any onComplete data (form data)
				if( count($_POST) > 0 ) {
					$this->crawlmodel->storeFormData($crawlID, $_POST);
				}
			
			} else {//some error occured
			
				$this->session->set_flashdata('error', 'The cloning process could not be initiated');
			
			}
		
		} else {//existing crawl
			
			$this->crawlmodel->setCrawlStatusTo($crawlID, 'building');
			
			$this->session->set_flashdata('crawlID', $crawlID);
			$this->session->set_flashdata('siteID', $siteID);
			
		}
		
		redirect('/clones/', 'location');
		
	}
	
	
	public function rbuild( $siteID, $crawlID ) {
		
		$this->load->library(array('crawler','session', 'ion_auth'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
		
		$this->page['clones'] = "Clones";
		
		
		$this->crawlmodel->setCrawlStatusTo($crawlID, 'building');
		
		$this->session->set_flashdata('continue', $crawlID);
		
		redirect('/clones/', 'location');
		
	}
 	
	
	public function buildClone($crawlID, $continue = false) {
		
		$this->load->library(array('crawler','session', 'ion_auth'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
		
		//$this->crawler->showProgress = true;
		
		$crawl = $this->crawlmodel->getCrawl($crawlID);
		
		if( $crawl ) {
			
			$site = $this->crawlmodel->getSiteDetails($crawl->site_id);
			
			if( $site ) {
				
				if( $site->sites_excludekeywords != '' ) {
										
					$excludeKeywords = explode(",", $site->sites_excludekeywords);
										
					if( count($excludeKeywords) == 1 ) {
						
						$excludeKeywords = array();
						
						$excludeKeywords[] = $site->sites_excludekeywords;
						$excludeKeywords[] = 'dummy';
						
					}
																														
					$this->crawler->startCrawl($crawl->site_id, $crawlID, $excludeKeywords, $continue);
					
					
				} else {
					
					$this->crawler->startCrawl($crawl->site_id, $crawlID, false, $continue);
					
				}
				
				
			} else {
			
				Die('Error: Site does not exist');
			
			}
			
		} else {
			
			Die('Error: could not start crawl with ID '.$crawlID);
			
		}
		
	}
	
	
	public function checkCrawlProgress($crawlID) {
		
		$crawl = $this->crawlmodel->getCrawl($crawlID);
		$this->crawlmodel->emptyProgress($crawlID);
		
		$return = array();
		
		$return['status'] = $crawl->status;
		$return['progress'] = $crawl->progress;
		
		echo json_encode($return);
		
	}
	
	
	public function removeClones() {
		
		$this->load->library(array('crawler','session', 'ion_auth'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
		
		if( !isset($_POST['toDel']) || !is_array($_POST['toDel']) ) {
			
			$this->session->set_flashdata('error', 'Please select one or more clones to remove');
			
			redirect('/clones/', 'location');
			
		} else {
			
			foreach( $_POST['toDel'] as $cloneID ) {
				
				$this->crawlmodel->deleteCrawl($cloneID);
				
			}
			
			$this->session->set_flashdata('success', 'The selected clones we deleted successfully.');
			
			redirect('/clones/', 'location');
			
		}
		
	}
	
	
	public function getZip($siteID, $timestamp) {
		
		$this->load->library(array('crawler','session', 'ion_auth'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
		
		$this->load->library('zip');
		
		$folder_in_zip = "/"; //root directory of the new zip file

		$path = 'sites/'.$siteID."/".$timestamp."/";
		$this->zip->get_files_from_folder($path, $folder_in_zip);
		
		$this->zip->download($siteID.'_'.$timestamp.'.zip');
		
	}
	
	
	public function sendByEmail() {
		
		$this->load->library(array('crawler','session', 'ion_auth'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
		
		$this->load->library('form_validation');
		
		$return = array();
		
		$this->form_validation->set_rules('siteID', 'Site ID', 'required|integer');
		$this->form_validation->set_rules('timestamp', 'Timestamp', 'required|integer');
		$this->form_validation->set_rules('field_email', 'Email', 'required|valid_email');
		
		if ($this->form_validation->run() == FALSE) {
			
			$return['code'] = 0;
			$return['message'] = 'There were some issues, please see the details below:<br>'.validation_errors();
		
		} else {
			
			//create the ZIP file, store in /tmp
			
			$this->load->library('zip');
			
			$folder_in_zip = "/"; //root directory of the new zip file

			$path = 'sites/'.$_POST['siteID']."/".$_POST['timestamp']."/";
			$this->zip->get_files_from_folder($path, $folder_in_zip);
			
			$fileName = $_POST['siteID'].'_'.$_POST['timestamp'].'.zip';
			
			$this->zip->archive('./tmp/'.$fileName);
			
			if( !file_exists( './tmp/'.$fileName ) ) {
				
				$return['code'] = 0;
				$return['message'] = "The ZIP archive can not be found; please verify that the server can write to folder: /tmp";
				
				die( json_encode($return) );
				
			} else {
				
				//all good so far, send the email
				
				$this->load->library('email');
				
				$this->email->from($this->config->item('email_fromaddress'), $this->config->item('email_fromname'));
				$this->email->to( $_POST['field_email'] );
				$this->email->subject( $_POST['field_subject'] );
				
				if( $_POST['textarea_message'] != '' ) {
					
					$this->email->message( $_POST['textarea_message'] );
					
				} else {
					
					$this->email->message( 'Please find the site clone attached to this email.' );
					
				}
				
				$this->email->attach('./tmp/'.$fileName);
				
				if( $this->email->send() ) {
			
					$return['code'] = 1;
					$return['message'] = "The email was sent successfully.";
				
				} else {
					
					$return['code'] = 0;
					$return['message'] = "The email could not be sent. Please note that sending emails might require configuring the script. The email settings can be found in /application/config/email.php, and more info regarding the CI email library can be found here: <a href='http://http://www.codeigniter.com/userguide3/libraries/email.html' target='_blank'>http://www.codeigniter.com/userguide3/libraries/email.html</a>";
					
				}
				
				unlink('./tmp/'.$fileName);
			
			}
			
		}
		
		echo json_encode( $return );
		
	}
	
	
	
	public function cancelCloning( $crawlID = '' ) {
		
		$return = array();
		
		
		if( $crawlID == '' || $crawlID == 'undefined' ) {
				
			$return['code'] = 0;
			$return['message'] = "Unfortunately, we can't cancel the cloning process due to a corrupted or missing clone ID.";
			
			die( json_encode($return) );
			
		}
		
		// all good, cancel cloning process
		
		$this->crawlmodel->cancelCrawl( $crawlID );
		
		$return['code'] = 1;
		$return['message'] = "Cloning cancelled successfully!";
		
		echo json_encode( $return );
		
	}
	
	
	
	public function upload() {
		
		$this->load->library(array('crawler','session', 'ion_auth'));
		
		if( !$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() ) {
			
			redirect('auth/login', 'location');
			
		}
		
		
		$this->load->library('form_validation');
		
		$return = array();
		
		$this->form_validation->set_rules('crawlID', 'Crawl ID', 'required|integer');
		$this->form_validation->set_rules('ftp_server', 'FTP Server', 'required');
		$this->form_validation->set_rules('ftp_user', 'FTP User', 'required');
		$this->form_validation->set_rules('ftp_password', 'FTP Password', 'required');
		
		if ($this->form_validation->run() == FALSE) {
			
			$return['code'] = 0;
			$return['message'] = 'There were some issues, please see the details below:<br>'.validation_errors();
			
			die( json_encode( $return ) );
		
		} else {
			
			//upload the clone
			
			$crawl = $this->crawlmodel->getCrawl( $_POST['crawlID'] );
			
			if( $crawl ) {
			
				$this->load->library('ftp');
			
				$config['hostname'] = $_POST['ftp_server'];
				$config['username'] = $_POST['ftp_user'];
				$config['password'] = $_POST['ftp_password'];
				
				if( isset($_POST['ftp_passive']) && $_POST['ftp_passive'] == 'yes' ) {
					
					$config['passive']  = TRUE;
					
				} else {
					
					$config['passive']  = FALSE;
					
				}
				
				if( isset($_POST['ftp_port']) && $_POST['ftp_port'] != '' ) {
					
					$config['port'] = $_POST['ftp_port'];
					
				} else {
					
					$config['port'] = 21;
					
				}
				
				$config['debug'] = FALSE;

				if( !$this->ftp->connect($config) ) {
					
					$return['code'] = 0;
					$return['message'] = "We could not connect to your FTP server with the provided details. Please double check and make sure the details are correct.";
					
					die( json_encode( $return ) );
					
				} else {
					
					if( isset($_POST['ftp_path']) && $_POST['ftp_path'] != '' ) {
						
						$path = $_POST['ftp_path'];
							
						if (substr($_POST['ftp_path'], 0, 1) !== '/') {//make sure path starts with a /
							
							$path = '/'.$path;
							
						}
						
						if( substr($_POST['ftp_path'], -1)  !== '/') {//make sure the path ends with a /
							
							$path = $path."/";
							
						}
						
					} else {
						
						$path = "/";
						
					}
					
					//connection is good, upload this bad boy
					if( $this->ftp->mirror('./sites/'.$crawl->site_id."/".$crawl->timestamp."/", $path) ) {
						
						$return['code'] = 1;
						$return['message'] = "All set; your clone was uploaded successfully.";
					
						die( json_encode( $return ) );
						
					} else {
						
						$return['code'] = 0;
						$return['message'] = "We were able to connect to your server, but we could not transfer the files. This is likely to be either a permissions issue (please double check and sure your FTP user has the proper permissions to upload data) or the path you're trying to upload to might not exist.";
					
						die( json_encode( $return ) );
						
					}
					
				}
			
			} else {
				
				//couldn't retrieve crawl ID
				$return['code'] = 0;
				$return['message'] = "We could not retrieve the clone. Please reload the page and try again.";
				
				die( json_encode( $return ) );
				
			}
			
			
		}
		
		echo json_encode( $return );
		
	}
	
}
