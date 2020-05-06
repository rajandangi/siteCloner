<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Crawlmodel extends CI_Model {
	
    function __construct() {
		
        parent::__construct();
        
        $this->load->database();
		$this->load->helper('file');
        
    }
	
	
	/*
	
		creates a new crawl
	
	*/
	
	public function createCrawl($siteID, $timestamp) {
		
		$data = array(
		   'site_id' => $siteID,
		   'timestamp' => $timestamp
		);

		$this->db->insert('crawls', $data);
		
		return $this->db->insert_id();
		
	}
	
	
	/*
	
		deletes a crawl
	
	*/
	
	public function deleteCrawl($crawlID) {
		
		$query = $this->db->from('crawls')->where('crawl_id', $crawlID)->get();
		
		if( $query->num_rows() > 0 ) {
			
			$res = $query->result();
			
			$siteID = $res[0]->site_id;
			$timestamp = $res[0]->timestamp;
			
	    	//delete files
			
			if( is_dir("./sites/".$siteID."/".$timestamp."/") ) {
			
	    		rrmdir( "./sites/".$siteID."/".$timestamp."/" );
				
			}
			
			//delete crawl entry
	    	//ancors tabl
	    	$this->db->delete('crawls', array('crawl_id' => $crawlID));
			
		} else {
			
			return false;
			
		}
		
	}
	
	
	/*
		
		deletes the files belonging to a crawl
		
	*/
	
	public function clearCrawl($crawlID) {
		
		$crawl = $this->getCrawl($crawlID);
		
		delete_files('./sites/'.$crawl->site_id."/".$crawl->timestamp."/", true);
		
	}
	
	
	
	/*
	
		stores some form data so we know what to do when the crawl completes :)
		
	 */
	
	public function storeFormData($crawlID, $data) {
		
		$data = array(
			'onComplete' => json_encode($data)
		);

		$this->db->where('crawl_id', $crawlID);
		$this->db->update('crawls', $data);
		
	}
	
	
	
	
	/*
	
		fetches existing crawl
	
	*/
	
	public function getCrawl($crawlID) {
		
		$query = $this->db->from('crawls')->where('crawl_id', $crawlID)->get();
		
		if( $query->num_rows() == 0 ) {
			
			return false;
			
		} else {
			
			$res = $query->result();
			
			return $res[0];
			
		}
		
	}
	
	
	
	/*
	
		empties out the progress column for a crawl
	
	*/
	
	public function emptyProgress($crawlID) {
		
		$data = array(
			'progress' => ''
		);

		$this->db->where('crawl_id', $crawlID);
		$this->db->update('crawls', $data);
		
	}
	
	
	
	/*
	
		sets the status for a completed crawl
	
	*/
	
	public function crawlComplete($crawlID, $siteID) {
				
		$crawl = $this->getCrawl( $crawlID );
		
		if( $crawl->status == 'building' ) {//only if the crawl is currently being build
				
			$data = array(
				'status' => 'complete'
			);

			$this->db->where('crawl_id', $crawlID);
			$this->db->update('crawls', $data);
			
			
			//anything to do onComplete?
			
			if( $crawl->onComplete != '' ) {
				
				$onComplete = json_decode( $crawl->onComplete, true );
				
				if( isset( $onComplete['doEmail'] ) && $onComplete['doEmail'] == 'yes' ) {//email notification
					
					$this->load->library('email');
				
					$this->email->from($this->config->item('email_fromaddress'), $this->config->item('email_fromname'));
					$this->email->to( $onComplete['field_email'] );
					$this->email->subject( $onComplete['field_subject'] );
				
					if( $onComplete['textarea_message'] != '' ) {
					
						$this->email->message( $onComplete['textarea_message'] );
					
					} else {
					
						$this->email->message( 'Please find the site clone attached to this email.' );
					
					}
					
					//send clone as attachment?
					if( isset( $onComplete['checkbox_attachClone'] ) && $onComplete['checkbox_attachClone'] == 'yes' ) {
						
						$this->load->library('zip');
			
						$folder_in_zip = "/"; //root directory of the new zip file

						$path = 'sites/'.$crawl->site_id."/".$crawl->timestamp."/";
						$this->zip->get_files_from_folder($path, $folder_in_zip);
			
						$fileName = $crawl->site_id.'_'.$crawl->timestamp.'.zip';
			
						$this->zip->archive('./tmp/'.$fileName);
						
						$this->email->attach('./tmp/'.$fileName);
					
					
					}
				
					$this->email->send();
					
					if( isset( $onComplete['checkbox_attachClone'] ) && $onComplete['checkbox_attachClone'] == 'yes' ) {
						unlink('./tmp/'.$fileName);
					}
					
				}
				
				if( isset( $onComplete['doFTP'] ) && $onComplete['doFTP'] == 'yes' ) {
					
					$this->load->library('ftp');
			
					$config['hostname'] = $onComplete['field_ftpserver'];
					$config['username'] = $onComplete['field_ftpuser'];
					$config['password'] = $onComplete['field_ftpspassword'];
					
					if( isset($onComplete['checkbox_ftpPassive']) && $onComplete['checkbox_ftpPassive'] == 'yes' ) {
					
						$config['passive']  = TRUE;
					
					} else {
					
						$config['passive']  = FALSE;
					
					}
					
					
					if( isset($onComplete['field_ftpport']) && $onComplete['field_ftpport'] != '' ) {
					
						$config['port'] = $onComplete['field_ftpport'];
					
					} else {
					
						$config['port'] = 21;
					
					}
					
					if( $this->ftp->connect($config) ) {
					
						if( isset($onComplete['field_ftppath']) && $onComplete['field_ftppath'] != '' ) {
						
							$path = $onComplete['field_ftppath'];
							
							if (substr($onComplete['field_ftppath'], 0, 1) !== '/') {//make sure path starts with a /
							
								$path = '/'.$path;
							
							}
						
							if( substr($onComplete['field_ftppath'], -1)  !== '/') {//make sure the path ends with a /
							
								$path = $path."/";
							
							}
						
						} else {
						
							$path = "/";
						
						}
						
						$this->ftp->mirror('./sites/'.$crawl->site_id."/".$crawl->timestamp."/", $path);
						
						
					}
					
				}
				
			}
			
		}
		
		
		$data = array(
			'sites_lastcrawl' => time()
		);

		$this->db->where('site_id', $siteID);
		$this->db->update('sites', $data); 
		
	}
	
	
	
	/*
	
		performs actions when a crawl times out
	
	*/
	
	public function timedOut($crawlID) {
		
		$crawl = $this->getCrawl( $crawlID );
		
		if( $crawl ) {
			
			if( $crawl->onComplete != '' ) {
				
				$onComplete = json_decode( $crawl->onComplete, true );
				
				if( isset( $onComplete['doEmail2'] ) && $onComplete['doEmail2'] == 'yes' ) {
					
					$this->load->library('email');
				
					$this->email->from($this->config->item('email_fromaddress'), $this->config->item('email_fromname'));
					$this->email->to( $onComplete['field_email2'] );
					$this->email->subject( $onComplete['field_subject2'] );
				
					if( $onComplete['textarea_message2'] != '' ) {
					
						$this->email->message( $onComplete['textarea_message2'] );
					
					} else {
					
						$this->email->message( 'Please find the site clone attached to this email.' );
					
					}
				
					$this->email->send();
					
				}
				
			}
			
		}
		
	}
	
	
	
	/*
	
		sets all remaining items to status = crawled and cancels the crawl
	
	*/
	
	public function cancelCrawl( $crawlID ) {
		
		//get siteID
		
		$crawl = $this->getCrawl( $crawlID );
		
		$siteID = $crawl->site_id;
		
		//set crawl status to "cancelled"
		
		$data = array(
			'status' => 'cancelled',
			'progress' => "<br><br><b class='text-danger'>Cancelled by user</b>"
		);

		$this->db->where('crawl_id', $crawl->crawl_id);
		$this->db->update('crawls', $data);
		
	}
	
	
	
	/*
		
		changes the crawl status
	
	*/
	
	public function setCrawlStatusTo($crawlID, $status) {
		
		if( $status == 'timed out' ) {
			$timeout = 1;
		} else {
			$timeout = 0;
		}
		
		$data = array(
			'status' => $status,
			'progress' => '',
			'timeout' => $timeout
		);

		$this->db->where('crawl_id', $crawlID);
		$this->db->update('crawls', $data); 
		
	}
	
	
	
	/*
	
		updates the progress for this crawl
	
	*/
	
	public function updateProgress($crawlID, $content) {
		
		//grab existing progress
		
		$query = $this->db->from('crawls')->where('crawl_id', $crawlID)->get();
		
		if( $query->num_rows() > 0 ) {
		
			$res = $query->result();
			
			if( strlen($res[0]->progress) < 50000 ) {
			
				$progress = $res[0]->progress.$content;
			
			} else {
				
				$progress = $content;
				
			}
		
			$data = array(
				'progress' => $progress
			);

			$this->db->where('crawl_id', $crawlID);
			$this->db->update('crawls', $data);
		
		} else {
			
			return false;
			
		}
		
	}
	
	
	
	/* 
		
		fetches all crawls
	
	 */
	
	public function getCrawls() {
		
		$query = $this->db->from('crawls')->join('sites', 'crawls.site_id = sites.site_id')->get();
		
		
		if( $query->num_rows() > 0 ) {
			
			return $query->result();
			
		} else {
			
			return false;
			
		}
		
	}
	
	
	
	/*
	
		resets all items (links, stylesheets, etc) for a site, so it can be re-crawled
	
	*/
	
	public function resetItems($siteID) {
		
		//links
		$this->db->delete('ancors', array('site_id' => $siteID));
				
		//stylesheets
		$this->db->delete('stylesheets', array('site_id' => $siteID));
		
		//images
		$this->db->delete('images', array('site_id' => $siteID));
		
		//scripts
		$this->db->delete('scripts', array('site_id' => $siteID));
		
		//files
		$this->db->delete('files', array('site_id' => $siteID));
		
	}
	
	
	
	
	/*
		
		Inserts ancor item into the database
	
	*/
	
    public function insertAncor($ancor, $base_url, $siteID, $crawlID) {
    	
    	//filter out links to images
    	if( strpos($ancor, '.jpg') !== false || strpos($ancor, '.jpeg') !== false || strpos($ancor, '.png') !== false || strpos($ancor, '.gif') !== false || strpos($ancor, '.bmp') !== false || strpos($ancor, '.pdf') !== false || strpos($ancor, '.swf') !== false ) {
    	
    	    return false;
    	    
    	}
    	
    	//filter out mailto links
    	if( strpos($ancor, "mailto") !== false ) {
    	    
    	    return false;
    	
    	}
		
		
		//filter out javascript links
    	if( strpos($ancor, "javascript") !== false ) {
    	    
    	    return false;
    	
    	}
		
		//filter out skype links
    	if( strpos($ancor, "skype") !== false ) {
    	    
    	    return false;
    	
    	}
		
		
		//trailing '/'
		
		if( substr($ancor, -1) != '/' ) {
		
			$temp = explode("/", $ancor);
		
			$lastOne = array_pop($temp);
				
			if( strpos($lastOne, '.') === false ) {
			
				//append trailing /
				$ancor .= '/';
						
			}
		
		}
    	
    	
    	//insert only doesn't exist yet
		
		//echo $ancor."<br>";
    	
    	$q = $this->db->from('ancors')->where('ancor_href', $ancor)->where('site_id', $siteID)->get();
    	    	
    	if( $q->num_rows() == 0 ) {
    	
    
    		$data = array(
    			'ancor_href' => $ancor,
    	   		'ancor_crawled' => 0,
    	   		'site_id' => $siteID,
				'crawl_id' => $crawlID
    		);
    	
    		$this->db->insert('ancors', $data);
    		
    		return true;
    	
    	} else {
    	
    		return false;
    	
    	}
    
    }
    
    
	/*
		
		Sets the "crawled" status of an ancor item
	
	*/
	
    public function ancorCrawled($ancorID, $siteID, $notfound = false) {
    
    	if( $notfound ) {
    	
    		$nf = 1;
    	
    	} else {
    	
    		$nf = 0;
    	
    	}
    	
    
    	$data = array(
   			'ancor_crawled' => '1',
   			'ancor_404' => $nf
    	);
    	
    	$this->db->where('ancor_id', $ancorID);
    	$this->db->where('site_id', $siteID);
    	$this->db->update('ancors', $data); 
    
    }
	
	
	
	/*
		
		Inserts file item into the database
	
	*/
	
    public function insertFile($file, $base_url, $siteID, $crawlID)
    {
		
    	$temp = explode("?", $file);
    	
    	if( count($temp) > 1 ) {
    		
    		//has it
    		
    		$file = $temp[0];
    	
    	}
		
    	
    	//insert only doesn't exist yet
    		
    	$q = $this->db->from('files')->where('file_url', $file)->where('site_id', $siteID)->get();
    		
    	if( $q->num_rows() == 0 ) {
    		
    	
    		$data = array(
    			'file_url' => $file,
    		   	'file_crawled' => 0,
    		   	'site_id' => $siteID,
				'crawl_id' => $crawlID
    		);
    		
    		$this->db->insert('files', $data);
    			
    		return true;
    		
    	} else {
    		
    		return false;
    		
    	}
    	
    }
	
	
	/*
		
		Sets the "crawled" status of a file item
	
	*/
	
    public function fileCrawled($fileID, $siteID, $notfound = false)
    {
    	
    	if( $notfound ) {
    	
    		$nf = 1;
    	
    	} else {
    	
    		$nf = 0;
    	
    	}
    
    
    	$data = array(
    		'file_crawled' => '1',
    		'file_404' => $nf
    	);
    	
    	$this->db->where('file_id', $fileID);
    	$this->db->where('site_id', $siteID);
    	$this->db->update('files', $data); 
    
    }
	
	
	/*
	
		Stores files on the server
		
	*/
	
    public function store($contents, $path, $fileName, $type = '', $siteID, $timestamp)
    {
		
		$fileName = ($type == 'HTML' && $fileName == '')? "index.html" : $fileName;
		
		if( substr($path, -1) != '/' ) {
			$path .= '/';
		}
		    	
    	write_file('./sites/'.$siteID.'/'.$timestamp.'/'.$path.$fileName, $contents);
    
    }
	
	
    /*
    	receives a full url and returns the file name (somefile.html, somefile.php, etc)
    */
    
    public function getFileName($url)
    {
    
    	if( substr($url, -1) == '/' ) {
  			
  			//there's no file in the url, pointing to folder
  			return "index.html";  	
    	
    	} elseif( strpos($url, '.html') !== false || strpos($url, '.htm') !== false || strpos($url, '.xhtml') !== false ) {
    		
    		//HTML file
    		
    		$temp = explode("/", $url);
    		
    		return end($temp);
    		
    	
    	} elseif( strpos($url, '.php') !== false || strpos($url, 'php4') !== false || strpos($url, 'php5') !== false ) {
    	
    		//PHP file
    		
    		$temp = explode("/", $url);
    		
    		return end($temp);
    		
    	
    	} elseif( strpos($url, '.asp') !== false ) {
    	
    		//ASP file
    		
    		$temp = explode("/", $url);
    		
    		return end($temp);
    		
    	
    	} elseif( strpos($url, '.js') !== false ) {
    	
    		//JS file
    		
    		$temp = explode("/", $url);
    		
    		return end($temp);
    	
    	} elseif( strpos($url, '.css') !== false ) {
    	
    		//CSS file
    		
    		$temp = explode("/", $url);
    		
    		return end($temp);
    	
    	} elseif( strpos($url, '.jpg') !== false || strpos($url, '.jpeg') !== false || strpos($url, '.png') !== false || strpos($url, '.gif') !== false ) {
    	
    		//IMAGE file
    		
    		$temp = explode("/", $url);
    		
    		return end($temp);
    	
    	} elseif( strpos($url, '.eot') !== false || strpos($url, '.woff') !== false || strpos($url, '.ttf') !== false || strpos($url, '.svg') !== false ) {
    	
    		//@font-face files
    		$temp = explode("/", $url);
    		
    		return end($temp);
    	
    	}
    
    	return false;
    
    }
	
	
    public function createDir($ancor, $fileName, $siteUrl, $siteID, $timestamp)
    {	
		$dir = $ancor;
		
		//remove http://
		if (substr($ancor, 0, strlen("http://")) == "http://") {
			$dir = substr($ancor, strlen("http://"));
		}
		
		//remove https://
		if (substr($ancor, 0, strlen("https://")) == "https://") {
			$dir = substr($ancor, strlen("https://"));
		}
		
		//remoce //
		if (substr($ancor, 0, strlen("//")) == "//") {
			$dir = substr($ancor, strlen("//"));
		}
		
    			    	
    	$dir = str_replace($fileName, "", $dir);
		
		$dir = str_replace($siteUrl, "", $dir);
		
			    	    	
    	if (!is_dir('sites/'.$siteID."/".$timestamp."/".$dir)) {
    	
    	    mkdir('./sites/'.$siteID."/".$timestamp."/".$dir, 0777, TRUE);
    	
    	}
    	
    	return $dir;
    	    
    }
	
	
	/*
		
		Insert an image item into the database
	
	*/
	
    public function insertImage($image, $base_url, $siteID, $crawlID)
    {	
		
    	$temp = explode("?", $image);
    	
    	if( count($temp) > 1 ) {
    		
    		//has it
    		
    		$image = $temp[0];
    	
    	}
		
    	
    	//make sure the href contains the URL
    	if( strpos($image, $base_url) === false ) {
    	    
    	    return false;
    	
    	}
    	
    
    	//insert only doesn't exist yet
    		
    	$q = $this->db->from('images')->where('image_src', $image)->where('site_id', $siteID)->get();
    		
    	if( $q->num_rows() == 0 && $image != '' && is_string($image) ) {
    		
    	
    		$data = array(
    			'image_src' => $image,
    			'site_id' => $siteID,
				'crawl_id' => $crawlID
    		);
    		
    		$this->db->insert('images', $data);
    		    		    		    			
    		return true;
    		
    	} else {
    		
    		return false;
    		
    	}
    
    }
	
	
	/*
	
		sets the "crawled" status of an image item
		
	*/
	
    public function imageCrawled($imageID, $siteID, $notfound = false)
    {
    	
    	if( $notfound ) {
    	
    		$nf = 1;
    	
    	} else {
    	
    		$nf = 0;
    	
    	}
    
    
    	$data = array(
    		'image_crawled' => '1',
    		'image_404' => $nf
    	);
    	
    	$this->db->where('image_id', $imageID);
    	$this->db->where('site_id', $siteID);
    	$this->db->update('images', $data); 
    
    }
	
	
	/*
		
		inserts a script item into the database
	
	*/
	
    public function insertScript($script, $base_url, $siteID, $crawlID)
    {	
    	
    	//remove ?v=whatever stuff
    	
    	$temp = explode("?", $script);
    	
    	if( count($temp) > 1 ) {
    		
    		//has it
    		
    		$script = $temp[0];
    	
    	}
    	
    	
    	//make sure the href contains the URL
    	if( strpos($script, $base_url) === false ) {
    	    
    	    return false;
    	
    	}
		
		
		//make sure script URL ends with .js
		if( substr_compare($script, '.js', strlen($script)-strlen('.js'), strlen('.js')) !== 0 ) {
			
			return false;
			
		}
    	
    
    	//insert only doesn't exist yet
    		
    	$q = $this->db->from('scripts')->where('script_url', $script)->where('site_id', $siteID)->get();
    		
    	if( $q->num_rows() == 0 && $script != '' && is_string($script) ) {
    		
    	
    		$data = array(
    			'script_url' => $script,
    			'site_id' => $siteID,
				'crawl_id' => $crawlID
    		);
    		
    		$this->db->insert('scripts', $data);
    		    		    			
    		return true;
    		
    	} else {
    		
    		return false;
    		
    	}
    
    }
	
	
	/*
	
		
	
	*/
	
    public function scriptCrawled($scriptID, $siteID, $notfound = false)
    {
    	
    	if( $notfound ) {
    	
    		$nf = 1;
    	
    	} else {
    	
    		$nf = 0;
    	
    	}
    	
    
    	$data = array(
    		'script_crawled' => '1',
    		'script_404' => $nf
    	);
    	
    	$this->db->where('script_id', $scriptID);
    	$this->db->where('site_id', $siteID);
    	$this->db->update('scripts', $data); 
    
    }
	
	
	/*
	
		Creates a site item
	
	*/
	
    public function createSite($url)
    {
    
    	
    	//domain name retrievel
    	
    	$temp = explode("/", $url);
    	
    	if( count($temp) > 1 ) {
    	
    		$domain = $temp[0];
			
			//array_shift($temp);
			
			$folder = implode("/", $temp);
			
			//make sure the URL doesn't contain any file names
			
			if (strpos(end($temp), '.') !== false) {
				
				array_pop($temp);
				
				$url = implode('/', $temp)."/";
				
			}
    	
    	} else {
    	
    		$domain = $url;
			$folder = $url;
    	
    	}
    	
    
    	$data = array(
    	   'site_url' => $url,
    	   'site_domain' => $domain,
		   'sites_created' => time()
    	);
    	
    	$this->db->insert('sites', $data);
    	
    	return $this->db->insert_id();
    
    }
	
	
	
	/*
	
		updates a site
	
	*/
	
	public function updateSite($siteID, $siteData) {
		
		$data = array(
			'sites_excludekeywords' => $siteData['exludekeywords'],
			'sites_timeout' => $siteData['field_timeout']
		);

		$this->db->where('site_id', $siteID);
		$this->db->update('sites', $data);
		
	}
	
	
	
	/*
	
		Get site details
	
	*/
	
    public function getSiteDetails($siteID)
    {
    					
    	$query = $this->db->from('sites')->where('site_id', $siteID)->get();
    	
    	if( $query->num_rows() > 0 ) {
    	
    		$res = $query->result();
    	
    		return $res[0];
    	
    	} else {
    	
    		return false;
    	
    	}
    
    }
	
	
	
	/*
		
		get all sites
	
	*/
	
	
	public function getSites() {
		
		$query = $this->db->from('sites')->get();
		
		if( $query->num_rows() > 0 ) {
		
			return $query->result();
		
		} else {
			
			return false;
			
		}
		
	}
	
	
	
    public function getDomain($siteID)
    {
    
    	$query = $this->db->from('sites')->where('site_id', $siteID)->get();
    	
    	if( $query->num_rows() > 0 ) {
    	
    		$res = $query->result();
    	
    		return $res[0]->site_url;
    	
    	} else {
    	
    		return false;
    	
    	}
    
    }
	
	
	
    public function getBaseUrl($siteID)
    {
    	
    	$query = $this->db->from('sites')->where('site_id', $siteID)->get();
    	
    	if( $query->num_rows() > 0 ) {
    	
    		$res = $query->result();
    	
    		return $res[0]->site_domain;
    	
    	} else {
    	
    		return false;
    	
    	}
    
    }
	
	
	
    public function deleteSite($siteID)
    {
    
    	//ancors table
    	$this->db->delete('ancors', array('site_id' => $siteID));
    	
    	
    	//files table
    	$this->db->delete('files', array('site_id' => $siteID));
    	
    	
    	//images table
    	$this->db->delete('images', array('site_id' => $siteID));
    	
    	
    	//scripts table
    	$this->db->delete('scripts', array('site_id' => $siteID));
    	
    	
    	//stylesheets table
    	$this->db->delete('stylesheets', array('site_id' => $siteID));
    	
		
		//crawls table
		$this->db->delete('crawls', array('site_id' => $siteID));
		
		
		//sites table
		$this->db->delete('sites', array('site_id' => $siteID));
		
    	
    	//delete files
		if( is_dir("./sites/".$siteID."/") ) {
		
    		rrmdir("./sites/".$siteID."/" );
		
		}
    
    }
	
	
	/*
	
		Creates a new stylesheet model
	
	*/
	
    public function insertStylesheet($stylesheet, $base_url, $siteID, $crawlID)
    {	
    
    	//remove ?v=whatever stuff
    	
    	$temp = explode("?", $stylesheet);
    	
    	if( count($temp) > 1 ) {
    		
    		//has it
    		
    		$stylesheet = $temp[0];
    	
    	}
    	
    	
    	//make sure the href contains the URL
    	if( strpos($stylesheet, $base_url) === false ) {
    	    
    	    //return false;
    	
    	}
    	
    
    	//insert only doesn't exist yet
    		
    	$q = $this->db->from('stylesheets')->where('stylesheet_url', $stylesheet)->where('site_id', $siteID)->get();
    		
    	if( $q->num_rows() == 0 && $stylesheet != '' && is_string($stylesheet) ) {
    		    	
    		$data = array(
    			'stylesheet_url' => $stylesheet,
    			'site_id' => $siteID,
				'crawl_id' => $crawlID
    		);
    		
    		$this->db->insert('stylesheets', $data);
    		    		    		    			
    		return true;
    		
    	} else {
    		
    		return false;
    		
    	}
    
    }
	
	
	/*
	
		Sets the "crawled" status of stylesheet item
	
	*/
	
    public function stylesheetCrawled($stylesheetID, $siteID, $notfound = false)
    {
    	
    	if( $notfound ) {
    	
    		$nf = 1;
    	
    	} else {
    	
    		$nf = 0;
    	
    	}
    
    	$data = array(
    		'stylesheet_crawled' => '1',
    		'stylesheet_404' => $nf
    	);
    	
    	$this->db->where('stylesheet_id', $stylesheetID);
    	$this->db->where('site_id', $siteID);
    	$this->db->update('stylesheets', $data); 
    
    }
	
	
	/*
	
		prepares URLs for insertion into database, takes any URL and returns a proper absolute URL
	
	*/
	
	public function prepUrl($link, $url, $domain, $base_url) {
		
		//basic filtering of URLs we don't need
		
		
		//remove white space
		$link = rtrim($link);
		    	
		//filter out links starting with '#'
    	if( $link[0] == '#' ) {
    	
    		return false;
    	
    	}
						
		//filter out external links
		/*if( (strpos($link,'http://'.$domain) === false && strpos($link,'https://'.$domain) === false) ) {
			
			return false;
			
		}*/
						
		if( 0 === strpos($link, 'http://') && 0 !== strpos($link, 'http://'.$base_url) ) {
			return false;
		}
		
		if( 0 === strpos($link, 'https://') && 0 !== strpos($link, 'https://'.$base_url) ) {
			return false;
		}
		
		if( 0 === strpos($link, '//') && 0 !== strpos($link, '//'.$base_url) ) {
			return false;
		}
				
				
		//absolute or relative URL
		
		if( 0 === strpos($link, "//") ) {// double slashes at the beginning
			
			$tLink = "http:".$link;
			
			return $tLink;
			
		} elseif( 0 === strpos($link, "http") || 0 === strpos($link, "https") || 0 === strpos($link, "/") ) {//absolute
			
			if( 0 === strpos($link, "/") ) {//absolute without domain, append domain
							
				$tLink = "http://".$domain.$link;
				
				if (substr($tLink, -2) == '/') {
				
					$tLink = rtrim($tLink, '/');
				
				}
				
				return $tLink;
				
			} else {//nothing to do
				
				$tLink = $link;
					
				if (substr($tLink, -2) == '/') {
				
					$tLink = rtrim($tLink, '/');
				
				}
				
				return $tLink;
				
			}
			
		} else {//relative
			
			//echo rel2abs($link, $url)."<br>";
			
			$tLink = rel2abs($link, $url);
			
			if (substr($tLink, -2) == '/') {
			
				$tLink = rtrim($tLink, '/');
			
			}
			
			return $tLink;
			
		}
		
	}
	
}

/* End of file Crawler.php */