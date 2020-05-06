<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Crawler {
	
	
	
	
	public $showProgress = true;//echo progress to the screen?
	
	private $crawlID;//current crawl ID
	
	private $timestamp;//timestamp of moment a crawl was initiated
	
	private $siteID; //siteID we're working on
	
	private $storeIn = '';//folder to put the site in, the domain name is used if empty
	
	private $restingPlace;
	
	private $startTime; //timestamp of the starting point of the recent crawl
	
	Private $allowedTime = 300; //the number of seconds a crawl is allowed to run
	
	Private $domain = '';
	
	public function __construct() {
		
		$CI =& get_instance();
		
		$CI->load->model('crawlmodel');
		$CI->load->helper('crawl');
		$CI->load->helper('htmldom');
		
		$CI->config->load('crawler');
				
	}

	public function startCrawl($siteID, $crawlID = false, $exludeKeywords = false, $continue)
	{
		
		$CI =& get_instance();
		
		$this->siteID = $siteID;
		
		
		//set the site's timeout
		
		$site = $CI->crawlmodel->getSiteDetails($siteID);
		
		$this->allowedTime = $site->sites_timeout;
		
		
		$timestamp = time();
				
		if( !$crawlID ) {//no crawlID, create a new one
			
			$CI->crawlmodel->resetItems($siteID);//delete all grabbed items
			
			$this->crawlID = $CI->crawlmodel->createCrawl($siteID, $timestamp);
			
			$this->timestamp = $timestamp;
			
		} else {//redoing existing crawl
			
			$cID = $CI->crawlmodel->getCrawl($crawlID);
			
			if( $cID ) {
								
				if( !$continue ) {//make sure this is not a timed out crawl
				
					//reset scrawl, delete files
					$CI->crawlmodel->clearCrawl( $cID->crawl_id );
					$CI->crawlmodel->resetItems($siteID);//delete all grabbed items
				
				}
				
				$this->timestamp = $cID->timestamp;
				$this->crawlID = $cID->crawl_id;
				
			} else {
				
				die("Crawl ID is incorrect; no matching crawl was found.");
				
			}
			
		}
		
		
		$this->startTime = time();
		
		//create a new crawl, or redo existing one?
		
												
		//prevent timeouts
		
		ignore_user_abort(true);
		set_time_limit(0);
			
		$siteDetails = $CI->crawlmodel->getSiteDetails($siteID);
		
		$fullUrl = $siteDetails->site_url;
		
		$this->domain = $siteDetails->site_domain;
						
		$this->restingPlace = "stattic/sites/".$fullUrl;
		
		$firstUrl = "http://".$siteDetails->site_url;
			
		if (substr($firstUrl, -1) != '/') {
			$firstUrl = "http://".$siteDetails->site_url."/";
		}
		
		//insert the base_url as our first page to crawl
		$CI->crawlmodel->insertAncor($firstUrl, $fullUrl, $siteID, $crawlID);
		
		$this->crawll($siteDetails->site_url, $exludeKeywords);
	
	}
	
	public function crawll($base_url, $exludeKeywords)
	{
				
		$CI =& get_instance();
						
		error_reporting(0);
		
		//grab uncrawled URLs
		$query = $CI->db->from('ancors')->where('ancor_crawled', '0')->join('crawls', 'ancors.crawl_id = crawls.crawl_id')->where('ancors.site_id', $this->siteID)->where('status', 'building')->get();
		
		$counter = 0;
						
		while( $query->num_rows() > 0 ) {//do we have any?
		//while( $counter == 0 ) {//do we have any?
		
			//grab first uncrawled URL
			$res = $query->result();
			
			$url = $res[0]->ancor_href;
			
			if($this->showProgress) {
				
				$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing link</b>: ".$url." ...<br>");
				
			}
			
						
			// Create DOM from URL or file
			
			$html = file_get_html($url);
						
			if( $html ) {
			
				//retrieve the file name
				$fileName = $CI->crawlmodel->getFileName($url);
			
			
				//create the folder structure for this URL
				$path = $CI->crawlmodel->createDir($url, $fileName, $base_url, $this->siteID, $this->timestamp);
				
				//alter the <base> tag
				
				$baseTag = $html->find('base');
				$baseTag[0]->href = "";//empty for now
				
				
				
				//find all inline style which contains images
				$inlineImageItems = $html->find('*[style*=url]');
				
				foreach( $inlineImageItems as $inlineImageItem ) {
					
					$styleAttribute = $inlineImageItem->style;
					
					preg_match_all('/url\(\s*[\'"]?(\S*\.(?:jpe?g|gif|png))[\'"]?\s*\)[^;}]*?/i', $styleAttribute, $matches);
					$images = $matches[1];
			
					//echo "<b>".$stylesheetUrl."</b></br>";
							
					foreach( $images as $img ) {
																								
						if( strpos($img, $base_url) !== false ) {
						
							//img has absolute path, nothing else to do but add for crawling
						
							$urlToInsert = $CI->crawlmodel->prepUrl($img, $url, $this->domain, $base_url);
						
							if( $urlToInsert ) {
						
								$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
							
								$CI->crawlmodel->updateProgress($this->crawlID, "<b>X ".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
								
								$theRelPath = getRelativePath($url, $img);
								
								//search and replace the absolute path with the relative one
								$inlineImageItem->style = str_replace($img, $theRelPath, $inlineImageItem->style);
						
							}
				
						} else {
							
							if( 0 === strpos($img, "/") ) {
								
								$urlToInsert = $CI->crawlmodel->prepUrl("http://".$this->domain.$img, $url, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>X ".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
									
									$theRelPath = getRelativePath($url, 'http://'.$this->domain.$img);
									
									//search and replace the absolute path with the relative one
									$inlineImageItem->style = str_replace($img, $theRelPath, $inlineImageItem->style);
									
								}
								
								
							} else {
				
								//image has relative path, ouch! change url and add for crawling
						
								$levels = substr_count($img, "../");
										
								$imgExploded = explode('/', $url);
					
								//popup off the file name
					
								array_pop( $imgExploded );
					
								for( $x=1; $x <= $levels; $x++ ) {
										
									array_pop( $imgExploded );
					
								}
					
					
								$newUrl = implode("/", $imgExploded)."/".str_replace("../", "", $img);
					
								//echo $newUrl;
						
								$urlToInsert = $CI->crawlmodel->prepUrl($newUrl, $url, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>X ".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
								}
							
							}
				
						}
			
					}
					
				}
				
				
				//grab all style tags and look for @import
				
				$styles = $html->find('style');
				
				foreach( $styles as $style ) {
					
					$theInnertext = $style->innertext;
					
					//do all @mport linked CSS
					preg_match_all("/[\.'A-Za-z0-9\/\-:_]*\.(css){1}/", $theInnertext, $output_array);
				
					foreach( $output_array[0] as $cssUrl ) {
						
						if( strpos($cssUrl, $base_url) !== false ) {//css url has absolute path, nothing else to do but add for crawling
						
							$urlToInsert = $CI->crawlmodel->prepUrl($cssUrl, $url, $this->domain, $base_url);
						
							if( $urlToInsert ) {
							
								$CI->crawlmodel->insertStylesheet($urlToInsert, $base_url, $this->siteID, $this->crawlID);
							
								$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing CSS LINK</b>: ".$urlToInsert."<br>");
								
								$theRelPath = getRelativePath($url, $cssUrl);
								
								//search and replace the absolute path with the relative one
								$style->innertext = str_replace($cssUrl, $theRelPath, $style->innertext);
							
							}
						
						} else {
							
							if( 0 === strpos($cssUrl, "/") ) {
								
								$urlToInsert = $CI->crawlmodel->prepUrl("http://".$this->domain.$cssUrl, $url, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertStylesheet($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing CSS LINK</b>: ".$urlToInsert."<br>");
									
									$theRelPath = getRelativePath($url, 'http://'.$this->domain.$cssUrl);
									
									//search and replace the absolute path with the relative one
									$style->innertext = str_replace($cssUrl, $theRelPath, $style->innertext);
									
								}
								
								
							} else {
				
								//image has relative path, ouch! change url and add for crawling
						
								$levels = substr_count($cssUrl, "../");
										
								$urlExploded = explode('/', $url);
					
								//popup off the file name
					
								array_pop( $urlExploded );
					
								for( $x=1; $x <= $levels; $x++ ) {
										
									array_pop( $urlExploded );
					
								}
					
					
								$newUrl = implode("/", $urlExploded)."/".str_replace("../", "", $cssUrl);
					
								//echo $newUrl;
						
								$urlToInsert = $CI->crawlmodel->prepUrl($newUrl, $url, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertStylesheet($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
								}
							
							}
							
						}
						
						
						
					}
					
					
					//do all images
					preg_match_all('/url\(\s*[\'"]?(\S*\.(?:jpe?g|gif|png))[\'"]?\s*\)[^;}]*?/i', $theInnertext, $matches);
					$images = $matches[1];
			
					//echo "<b>".$stylesheetUrl."</b></br>";
							
					foreach( $images as $img ) {
																								
						if( strpos($img, $base_url) !== false ) {
						
							//img has absolute path, nothing else to do but add for crawling
						
							$urlToInsert = $CI->crawlmodel->prepUrl($img, $url, $this->domain, $base_url);
						
							if( $urlToInsert ) {
						
								$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
							
								$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
								
								$theRelPath = getRelativePath($url, $img);
								
								//search and replace the absolute path with the relative one
								$style->innertext = str_replace($img, $theRelPath, $style->innertext);
						
							}
				
						} else {
							
							if( 0 === strpos($img, "/") ) {
								
								$urlToInsert = $CI->crawlmodel->prepUrl("http://".$this->domain.$img, $url, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
									
									$theRelPath = getRelativePath($url, 'http://'.$this->domain.$img);
									
									//search and replace the absolute path with the relative one
									$style->innertext = str_replace($img, $theRelPath, $style->innertext);
									
								}
								
								
							} else {
				
								//image has relative path, ouch! change url and add for crawling
						
								$levels = substr_count($img, "../");
										
								$imgExploded = explode('/', $url);
					
								//popup off the file name
					
								array_pop( $imgExploded );
					
								for( $x=1; $x <= $levels; $x++ ) {
										
									array_pop( $imgExploded );
					
								}
					
					
								$newUrl = implode("/", $imgExploded)."/".str_replace("../", "", $img);
					
								//echo $newUrl;
						
								$urlToInsert = $CI->crawlmodel->prepUrl($newUrl, $url, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
								}
							
							}
				
						}
			
					}
					
				}
				
														
				//get all links
			
				$links = $html->find('a');
			
				foreach( $links as $link ) {
					
					$doLink = true;
					
					//exclude keyword check
					if( is_array($exludeKeywords) ) {							
													
						foreach( $exludeKeywords as $keyword ) {
							
							if (strpos($link->href, $keyword) !== false) {
							    
								$doLink = false;
								break;
								
							} 
							
						}
												
					}
								
					if( $link->href != '#' && $doLink ) {
																								
						$urlToInsert = $CI->crawlmodel->prepUrl($link->href, $url, $this->domain, $base_url);
																		
						if( $urlToInsert ) {
														
							$CI->crawlmodel->insertAncor($urlToInsert, $base_url, $this->siteID, $this->crawlID);
							
							$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing link</b>: ".$urlToInsert."<br>");
														
							//alter the href attribute, point to the new path
						
							//check if we have the domain name at the beginning of the href
							
							if( 0 === strpos($link->href, "//") ) {
							
								$link->href = getRelativePath($url, 'http:'.$link->href);
							
							} elseif ( 0 === strpos($link->href, "http://".$base_url) || 0 === strpos($link->href, "https://".$base_url) || 0 === strpos($link->href, "/") ) {
						   
								//absolute URL

							   	if( 0 === strpos($link->href, "/") ) {
																	   
									$link->href = getRelativePath($url, 'http://'.$this->domain.$link->href);
																	
							   	} else {
							   							   						   
							   	 	$link->href = getRelativePath($url, $link->href);
								   
						   		}
						   
							}
							
						}
																								
						//make sure we don't cross the maximum number of allowed pages
						
						if( $CI->session->userdata('pageCounter') >= 600 ) {
						
							//delete data for this site
							$CI->crawlmodel->deleteSite($this->siteID, $base_url);
						
							$return = array();
							$return['content'] = $CI->load->view('partials/toomany', $CI->data, true);
						
							die( json_encode($return) );
						 
						} elseif( $linkInsertResult ) {
						
							$n = $CI->session->userdata('pageCounter')+1;
						
							$CI->session->set_userdata('pageCounter', $n);
						
						}
						
						
						//some links to the home page might be without the trailing /
						/*
						if( $link->href == rtrim($base_url, '/') ) {
						
							$link->href = rtrim($this->restingPlace, '/');
						
						}
						*/
					
					
					}
			
				}
			
				
				//get all external js files
				
				$scripts = $html->find('script');
			
				foreach( $scripts as $script ) {
					
					$urlToInsert = $CI->crawlmodel->prepUrl($script->src, $url, $this->domain, $base_url);
					
					if( $urlToInsert ) {
						
						$CI->crawlmodel->insertScript($urlToInsert, $base_url, $this->siteID, $this->crawlID);
						
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing script</b>: ".$urlToInsert."<br>");
						
						if( $script->src != '' ) {
				
							//alter the src attribute, point to the new path
							
							if( 0 === strpos($script->src, "//") ) {
								
								$script->src = getRelativePath($url, 'http:'.$script->src);
								
							} elseif ( 0 === strpos($script->src, "http://".$base_url) || 0 === strpos($script->src, "https://".$base_url) || 0 === strpos($script->src, "/") ) {
						   
								//absolute URL

							   	if( 0 === strpos($script->src, "/") ) {
								   
									$script->src = getRelativePath($url, 'http://'.$this->domain.$script->src);
								
							   	} else {
							   							   						   
							   	 	$script->src = getRelativePath($url, $script->src);
								   
						   		}
						   
							}
				
						}
						
					}
									
				}
			
			
				//get all stylesheets
			
				$stylesheets = $html->find('link[rel=stylesheet]');
			
				foreach( $stylesheets as $stylesheet ) {
					
					$urlToInsert = $CI->crawlmodel->prepUrl($stylesheet->href, $url, $this->domain, $base_url);
					
					if( $urlToInsert ) {
						
						$CI->crawlmodel->insertStylesheet($urlToInsert, $base_url, $this->siteID, $this->crawlID);
						
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing stylesheet</b>: ".$urlToInsert."<br>");
						
						//alter the src attribute, point to the new path
						
						if( 0 === strpos($stylesheet->href, "//") ) {
							
							$stylesheet->href = getRelativePath($url, 'http:'.$stylesheet->href);
							
						} elseif ( 0 === strpos($stylesheet->href, "http://".$base_url) || 0 === strpos($stylesheet->href, "https://".$base_url) || 0 === strpos($stylesheet->href, "/") ) {
							
							//absolute URL

						   	if( 0 === strpos($stylesheet->href, "/") ) {
															   	
								$stylesheet->href = getRelativePath($url, 'http://'.$this->domain.$stylesheet->href);
																																													
						   	} else {
						   							   						   
						   	 	$stylesheet->href = getRelativePath($url, $stylesheet->href);
															   
					   		}
																																
						}
						
					}
							
				}
			
			
				//get all images from markup
			
				$images = $html->find('img');
			
				foreach( $images as $image ) {
					
					$urlToInsert = $CI->crawlmodel->prepUrl($image->src, $url, $this->domain, $base_url);
					
					if( $urlToInsert ) {
						
						$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
						
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
						
						
						if( 0 === strpos($image->src, "//") ) {
							
							$image->src = getRelativePath($url, 'http:'.$image->src);
							
						} elseif ( 0 === strpos($image->src, "http://".$base_url) || 0 === strpos($image->src, "https://".$base_url) || 0 === strpos($image->src, "/") ) {
							
							//absolute URL

						   	if( 0 === strpos($image->src, "/") ) {
							   
								$image->src = getRelativePath($url, 'http://'.$this->domain.$image->src);
							
						   	} else {
						   							   						   
						   	 	$image->src = getRelativePath($url, $image->src);
							   
					   		}
																																
						}
												
					}
				
				}
						
			
				//store the file
				$theHTML = $html->save();
				
				$CI->crawlmodel->store($theHTML, $path, $fileName, "HTML", $this->siteID, $this->timestamp);
			
				//set URL as crawled
				$CI->crawlmodel->ancorCrawled($res[0]->ancor_id, $this->siteID);
			
				//dump to screen
				//flush();
				
				if($this->showProgress) {
				
					$CI->crawlmodel->updateProgress($this->crawlID, "<b>Processing link</b>: <span class='text-primary'>done</span><br>");
				
				}
			
			} else {
			
				//404!
				//set URL as crawled
				$CI->crawlmodel->ancorCrawled($res[0]->ancor_id, $this->siteID, true);
				
				if($this->showProgress) {
				
					$CI->crawlmodel->updateProgress($this->crawlID, "<b>Processing link</b>: <span class='text-danger'>404</span><br>");
				
				}
			
			}
			
			
			
			//recursion baby!, check timer first
			if( (time() - $this->startTime) < $this->allowedTime ) {
			
				$query = $CI->db->from('ancors')->where('ancor_crawled', '0')->join('crawls', 'ancors.crawl_id = crawls.crawl_id')->where('ancors.site_id', $this->siteID)->where('status', 'building')->get();
			
				$counter++;
			
			} else {
				
				//time's up!
				$CI->crawlmodel->setCrawlStatusTo($this->crawlID, 'timed out');
				$CI->crawlmodel->updateProgress($this->crawlID, '<br><p><b class="text-danger">Timed out... (after '.$this->allowedTime.' seconds)</b> You can re-build this clone at any later moment, it will pick up where it left off...</p>');
				$CI->crawlmodel->timedOut($this->crawlID);//possible action is required
				
				die();
				
			}
			
			$html = null;
			unset($html);
		
		}
		
		$html = null;
		unset($html);
		
		
		//grab uncrawled scripts, if time permits
		
		if( (time() - $this->startTime) < $this->allowedTime ) {
		
			$query = $CI->db->from('scripts')->where('script_crawled', '0')->join('crawls', 'scripts.crawl_id = crawls.crawl_id')->where('scripts.site_id', $this->siteID)->where('status', 'building')->get();
		
			$counter = 0;
		
			while( $query->num_rows() > 0 ) {
		
				//grab first uncrawled URL
				$res = $query->result();
			
				$scriptUrl = $res[0]->script_url;
			
				if($this->showProgress) {
				
					$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing script:</b> ".$scriptUrl." ...<br>");
				
				}
			
			
				$jsFile = file_get_contents( $scriptUrl );
			
				if( $jsFile ) {
				
					//retrieve the file name
					$fileName = $CI->crawlmodel->getFileName($scriptUrl);
			
					//create the folder structure for this URL
					$path = $CI->crawlmodel->createDir($scriptUrl, $fileName, $base_url, $this->siteID, $this->timestamp);
				
									
					//store the file
					$CI->crawlmodel->store($jsFile, $path."/", $fileName, '', $this->siteID, $this->timestamp);
			
			
					//set script as crawled
					$CI->crawlmodel->scriptCrawled($res[0]->script_id, $this->siteID);
				
					if($this->showProgress) {
				
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing script</b>: <span class='text-primary'>done</span><br>");
				
					}
			
				} else {
			
					//404
					$CI->crawlmodel->scriptCrawled($res[0]->script_id, $this->siteID, true);
				
					if($this->showProgress) {
				
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing script/b>: <span class='text-danger'>404</span><br>");
				
					}
			
				}
			
			
			
				//recursion baby! if time is ok
				
				if( (time() - $this->startTime) < $this->allowedTime ) {
					
					$query = $CI->db->from('scripts')->where('script_crawled', '0')->join('crawls', 'scripts.crawl_id = crawls.crawl_id')->where('scripts.site_id', $this->siteID)->where('status', 'building')->get();
			
					$counter++;
				
				} else {
					
					//time's up!
					$CI->crawlmodel->setCrawlStatusTo($this->crawlID, 'timed out');
					$CI->crawlmodel->updateProgress($this->crawlID, '<br><p><b class="text-danger">Timed out... (after '.$this->allowedTime.' seconds)</b> You can re-build this clone at any later moment, it will pick up where it left off...</p>');
					
					$CI->crawlmodel->timedOut($this->crawlID);//possible action is required
					
					die();
					
				}
				
				$jsFile = null;
				unset($jsFile);
		
			}
		
		} else {
			
			//time's up!
			$CI->crawlmodel->setCrawlStatusTo($this->crawlID, 'timed out');
			$CI->crawlmodel->updateProgress($this->crawlID, '<br><p><b class="text-danger">Timed out... (after '.$this->allowedTime.' seconds)</b> You can re-build this clone at any later moment, it will pick up where it left off...</p>');
			
			$CI->crawlmodel->timedOut($this->crawlID);//possible action is required
			
			die();
			
		}
		
		$jsFile = null;
		unset($jsFile);
		
		
		//grab uncrawled stylesheets
		
		if( (time() - $this->startTime) < $this->allowedTime ) {
		
			$q = $CI->db->from('stylesheets')->where('stylesheet_crawled', '0')->join('crawls', 'stylesheets.crawl_id = crawls.crawl_id')->where('stylesheets.site_id', $this->siteID)->where('status', 'building')->get();
				
			$counter = 0;
		
			while( $q->num_rows() > 0 ) {
		
		
				//grab first uncrawled URL
				$r = $q->result();
												
				$stylesheetUrl = $r[0]->stylesheet_url;
			
				if($this->showProgress) {
				
					$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing stylesheet</b>: ".$stylesheetUrl." ...<br>");
				
				}
			
				$cssFile = file_get_contents( $stylesheetUrl );
			
			
				if( $cssFile ) {
			
					//create the folder structure for this URL
				
					//retrieve the file name
					$fileName = $CI->crawlmodel->getFileName( $stylesheetUrl );
			
					$path = $CI->crawlmodel->createDir( $stylesheetUrl, $fileName, $base_url, $this->siteID, $this->timestamp );
			
						
					
					//do all @mport linked CSS
					preg_match_all("/[\.'A-Za-z0-9\/\-:_]*\.(css){1}/", $cssFile, $output_array);
				
					foreach( $output_array[0] as $cssUrl ) {
						
						if( strpos($cssUrl, $base_url) !== false ) {//css url has absolute path, nothing else to do but add for crawling
						
							$urlToInsert = $CI->crawlmodel->prepUrl($cssUrl, $stylesheetUrl, $this->domain, $base_url);
						
							if( $urlToInsert ) {
							
								$CI->crawlmodel->insertStylesheet($urlToInsert, $base_url, $this->siteID, $this->crawlID);
							
								$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing CSS LINK</b>: ".$urlToInsert."<br>");
								
								$theRelPath = getRelativePath($stylesheetUrl, $cssUrl);
								
								//search and replace the absolute path with the relative one
								$style->innertext = str_replace($cssUrl, $theRelPath, $style->innertext);
							
							}
						
						} else {
							
							if( 0 === strpos($cssUrl, "/") ) {
								
								$urlToInsert = $CI->crawlmodel->prepUrl("http://".$this->domain.$cssUrl, $stylesheetUrl, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertStylesheet($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing CSS LINK</b>: ".$urlToInsert."<br>");
									
									$theRelPath = getRelativePath($url, 'http://'.$this->domain.$cssUrl);
									
									//search and replace the absolute path with the relative one
									$style->innertext = str_replace($cssUrl, $theRelPath, $style->innertext);
									
								}
								
								
							} else {
				
								//image has relative path, ouch! change url and add for crawling
						
								$levels = substr_count($cssUrl, "../");
										
								$urlExploded = explode('/', $stylesheetUrl);
					
								//popup off the file name
					
								array_pop( $urlExploded );
					
								for( $x=1; $x <= $levels; $x++ ) {
										
									array_pop( $urlExploded );
					
								}
					
					
								$newUrl = implode("/", $urlExploded)."/".str_replace("../", "", $cssUrl);
					
								//echo $newUrl;
						
								$urlToInsert = $CI->crawlmodel->prepUrl($newUrl, $stylesheetUrl, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertStylesheet($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing CSS LINK</b>: ".$urlToInsert."<br>");
								}
							
							}
							
						}
						
						
						
					}
			
			
					//we'll also need all images from these stylesheets
			
					preg_match_all('/url\(\s*[\'"]?(\S*\.(?:jpe?g|gif|png))[\'"]?\s*\)[^;}]*?/i', $cssFile, $matches);
					$images = $matches[1];
			
					//echo "<b>".$stylesheetUrl."</b></br>";
							
					foreach( $images as $img ) {
																								
						if( strpos($img, $base_url) !== false ) {
						
							//img has absolute path, nothing else to do but add for crawling
						
							$urlToInsert = $CI->crawlmodel->prepUrl($img, $stylesheetUrl, $this->domain, $base_url);
						
							if( $urlToInsert ) {
						
								$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
							
								$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
								
								$theRelPath = getRelativePath($stylesheetUrl, $img);
								
								//search and replace the absolute path with the relative one
								$cssFile = str_replace($img, $theRelPath, $cssFile);
						
							}
				
						} else {
							
							if( 0 === strpos($img, "/") ) {
								
								$urlToInsert = $CI->crawlmodel->prepUrl("http://".$this->domain.$img, $stylesheetUrl, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
									
									$theRelPath = getRelativePath($stylesheetUrl, 'http://'.$this->domain.$img);
									
									//search and replace the absolute path with the relative one
									$cssFile = str_replace($img, $theRelPath, $cssFile);
									
								}
								
								
							} else {
				
								//image has relative path, ouch! change url and add for crawling
						
								$levels = substr_count($img, "../");
										
								$imgExploded = explode('/', $stylesheetUrl);
					
								//popup off the file name
					
								array_pop( $imgExploded );
					
								for( $x=1; $x <= $levels; $x++ ) {
										
									array_pop( $imgExploded );
					
								}
					
					
								$newUrl = implode("/", $imgExploded)."/".str_replace("../", "", $img);
					
								//echo $newUrl;
						
								$urlToInsert = $CI->crawlmodel->prepUrl($newUrl, $stylesheetUrl, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertImage($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing image</b>: ".$urlToInsert."<br>");
								}
							
							}
				
						}
			
					}
				
				
					//we'll also need @font-face fonts from the stylesheets
				
					preg_match_all("/[\.'A-Za-z0-9\/\-:_]*\.(eot|woff|ttf|svg){1}/", $cssFile, $output_array);
				
					foreach( $output_array[0] as $file ) {
				
						//remove crap from the front
					
						$file = ltrim($file, "('");
						$file = ltrim($file, '("');
						$file = ltrim($file, "(");
					
						if( strpos($file, $base_url) !== false ) {
						
							//img has absolute path, nothing else to do but add for crawling
							
							$urlToInsert = $CI->crawlmodel->prepUrl($file, $stylesheetUrl, $this->domain, $base_url);
							
							if( $urlToInsert ) {
													
								$CI->crawlmodel->insertFile($urlToInsert, $base_url, $this->siteID, $this->crawlID);
								$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing file</b>: ".$urlToInsert." > ".$stylesheetUrl."<br>");
								
								
								$theRelPath = getRelativePath($stylesheetUrl, $file);
								
								//search and replace the absolute path with the relative one
								$cssFile = str_replace($file, $theRelPath, $cssFile);
							
							}
					
						} else {
							
							if( 0 === strpos($file, "/") ) {
								
								$urlToInsert = $CI->crawlmodel->prepUrl("http://".$this->domain.$file, $stylesheetUrl, $this->domain, $base_url);
								
								if( $urlToInsert ) {
									
									$CI->crawlmodel->insertFile($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing file</b>: ".$urlToInsert."<br>");
									
									$theRelPath = getRelativePath($stylesheetUrl, 'http://'.$this->domain.$file);
									
									//search and replace the absolute path with the relative one
									$cssFile = str_replace($file, $theRelPath, $cssFile);
									
								}
								
								
							} else {
				
								//image has relative path, ouch! change url and add for crawling
						
								$levels = substr_count($file, "../");
											
								$fileExploded = explode('/', $stylesheetUrl);
						
								//popup off the file name
						
								array_pop( $fileExploded );
						
								for( $x=1; $x <= $levels; $x++ ) {
											
									array_pop( $fileExploded );
						
								}
						
						
								$newUrl = implode("/", $fileExploded)."/".str_replace("../", "", $file);
						
								//echo $newUrl;
						
								$urlToInsert = $CI->crawlmodel->prepUrl($newUrl, $stylesheetUrl, $this->domain, $base_url);
						
								if( $urlToInsert ) {
									$CI->crawlmodel->insertFile($urlToInsert, $base_url, $this->siteID, $this->crawlID);
									$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Grabbing file</b>: ".$urlToInsert."<br>");
								}
						
							}
					
						}
				
					
				
					}
					
					
					//store the file
					$CI->crawlmodel->store($cssFile, $path."/", $fileName, '', $this->siteID, $this->timestamp);
			
			
					//set script as crawled
					$CI->crawlmodel->stylesheetCrawled($r[0]->stylesheet_id, $this->siteID);
				
					if($this->showProgress) {
				
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing stylesheet</b>: <span class='text-primary'>done</span><br>");
				
					}
			
				} else {
			
					//404
					$CI->crawlmodel->stylesheetCrawled($r[0]->stylesheet_id, $this->siteID, true);
				
					if($this->showProgress) {
				
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing stylesheet</b>: <span class='text-danger'>404</span><br>");
				
					}
			
				}
				
			
			
				//recursion baby!, if time is ok
				
				if( (time() - $this->startTime) < $this->allowedTime ) {
					
					$q = $CI->db->from('stylesheets')->where('stylesheet_crawled', '0')->join('crawls', 'stylesheets.crawl_id = crawls.crawl_id')->where('stylesheets.site_id', $this->siteID)->where('status', 'building')->get();
			
					$counter++;
				
				} else {
					
					//time's up!
					$CI->crawlmodel->setCrawlStatusTo($this->crawlID, 'timed out');
					$CI->crawlmodel->updateProgress($this->crawlID, '<br><p><b class="text-danger">Timed out... (after '.$this->allowedTime.' seconds)</b> You can re-build this clone at any later moment, it will pick up where it left off...</p>');
					
					$CI->crawlmodel->timedOut($this->crawlID);//possible action is required
					
					die();
					
				}
				
				$cssFile = null;
				unset($cssFile);
		
			}
		
		} else {
			
			//time's up!
			$CI->crawlmodel->setCrawlStatusTo($this->crawlID, 'timed out');
			$CI->crawlmodel->updateProgress($this->crawlID, '<br><p><b class="text-danger">Timed out... (after '.$this->allowedTime.' seconds)</b> You can re-build this clone at any later moment, it will pick up where it left off...</p>');
			
			$CI->crawlmodel->timedOut($this->crawlID);//possible action is required
			
			die();
			
		}
		
		$cssFile = null;
		unset($cssFile);
		
		
		//grab uncrawled images, if time is ok
		
		if( (time() - $this->startTime) < $this->allowedTime ) {
		
			$q = $CI->db->from('images')->where('image_crawled', '0')->join('crawls', 'images.crawl_id = crawls.crawl_id')->where('images.site_id', $this->siteID)->where('status', 'building')->get();
		
			$counter = 0;
		
			while( $q->num_rows() > 0 ) {
		
				//grab first uncrawled URL
				$r = $q->result();
												
				$imageSrc = $r[0]->image_src;
			
				if($this->showProgress) {
				
					$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing image</b>: ".$imageSrc." ...<br>");
				
				}
			
				$imageFile = file_get_contents( $imageSrc );
			
			
				if( $imageFile ) {
			
					//retrieve the file name
					$fileName = $CI->crawlmodel->getFileName( $imageSrc );
			
					//create the folder structure for this URL
					$path = $CI->crawlmodel->createDir( $imageSrc, $fileName, $base_url, $this->siteID, $this->timestamp );
							
					//store the file
					$CI->crawlmodel->store($imageFile, $path."/", $fileName, '', $this->siteID, $this->timestamp);
			
			
					//set script as crawled
					$CI->crawlmodel->imageCrawled($r[0]->image_id, $this->siteID);
				
					if($this->showProgress) {
				
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing image</b>: <span class='text-primary'>done</span><br>");
				
					}
			
				} else {
			
					//404
					$CI->crawlmodel->imageCrawled($r[0]->image_id, $this->siteID, true);
				
					if($this->showProgress) {
				
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing image</b>: <span class='text-danger'>404</span><br>");
				
					}
			
				}
			
			
			
				//recursion baby!, if time is ok
				if( (time() - $this->startTime) < $this->allowedTime ) {
					
					$q = $CI->db->from('images')->where('image_crawled', '0')->join('crawls', 'images.crawl_id = crawls.crawl_id')->where('images.site_id', $this->siteID)->where('status', 'building')->get();
			
					$counter++;
				
				} else {
					
					//time's up!
					$CI->crawlmodel->setCrawlStatusTo($this->crawlID, 'timed out');
					$CI->crawlmodel->updateProgress($this->crawlID, '<br><p><b class="text-danger">Timed out... (after '.$this->allowedTime.' seconds)</b> You can re-build this clone at any later moment, it will pick up where it left off...</p>');
					
					$CI->crawlmodel->timedOut($this->crawlID);//possible action is required
					
					die();
					
				}
				
				$imageFile = null;
				unset($imageFile);
		
			}
		
		} else {
			
			//time's up!
			$CI->crawlmodel->setCrawlStatusTo($this->crawlID, 'timed out');
			$CI->crawlmodel->updateProgress($this->crawlID, '<br><p><b class="text-danger">Timed out... (after '.$this->allowedTime.' seconds)</b> You can re-build this clone at any later moment, it will pick up where it left off...</p>');
			
			$CI->crawlmodel->timedOut($this->crawlID);//possible action is required
			
			die();
			
		}
		
		$imageFile = null;
		unset($imageFile);
		
		
		//grab uncrawled files, if time is ok
		
		if( (time() - $this->startTime) < $this->allowedTime ) {
		
			$q = $CI->db->from('files')->where('file_crawled', '0')->join('crawls', 'files.crawl_id = crawls.crawl_id')->where('files.site_id', $this->siteID)->where('status', 'building')->get();
		
			$counter = 0;
		
			while( $q->num_rows() > 0 ) {
		
				//grab first uncrawled URL
				$r = $q->result();
												
				$fileSUrl = $r[0]->file_url;
			
				if($this->showProgress) {
				
					$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing file</b>: ".$fileSUrl." ...<br>");
				
				}
			
				$file = file_get_contents( $fileSUrl );
			
			
				if( $file ) {
			
					//retrieve the file name
					$fileName = $CI->crawlmodel->getFileName( $fileSUrl );
			
					//create the folder structure for this URL
					$path = $CI->crawlmodel->createDir( $fileSUrl, $fileName, $base_url, $this->siteID, $this->timestamp );
			
						
					//store the file
					$CI->crawlmodel->store($file, $path."/", $fileName, '', $this->siteID, $this->timestamp);
			
			
					//set script as crawled
					$CI->crawlmodel->fileCrawled($r[0]->file_id, $this->siteID);
				
					if($this->showProgress) {
				
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing file</b>: <span class='text-primary'>done</span><br>");
				
					}
			
				} else {
			
					//404
					$CI->crawlmodel->fileCrawled($r[0]->file_id, $this->siteID, true);
				
					if($this->showProgress) {
				
						$CI->crawlmodel->updateProgress($this->crawlID, "<b>".memory_get_peak_usage()." Processing file</b>: <span class='text-danger'>404</span><br>");
				
					}
			
				}
			
			
			
				//recursion baby!, if time is ok
				
				if( (time() - $this->startTime) < $this->allowedTime ) {
				
					$q = $CI->db->from('files')->where('file_crawled', '0')->join('crawls', 'files.crawl_id = crawls.crawl_id')->where('files.site_id', $this->siteID)->where('status', 'building')->get();
			
					$counter++;
				
				} else {
					
					//time's up!
					$CI->crawlmodel->setCrawlStatusTo($this->crawlID, 'timed out');
					$CI->crawlmodel->updateProgress($this->crawlID, '<br><p><b class="text-danger">Timed out... (after '.$this->allowedTime.' seconds)</b> You can re-build this clone at any later moment, it will pick up where it left off...</p>');
					
					$CI->crawlmodel->timedOut($this->crawlID);//possible action is required
					
					die();
					
				}
				
				$file = null;
				unset($file);
		
			}
		
		} else {
			
			//time's up!
			$CI->crawlmodel->setCrawlStatusTo($this->crawlID, 'timed out');
			$CI->crawlmodel->updateProgress($this->crawlID, '<br><p><b class="text-danger">Timed out... (after '.$this->allowedTime.' seconds)</b> You can re-build this clone at any later moment, it will pick up where it left off...</p>');
			
			$CI->crawlmodel->timedOut($this->crawlID);//possible action is required
			
			die();
			
		}
		
		$file = null;
		unset($file);
		
		
		//mark crawl as complete
		$CI->crawlmodel->crawlComplete( $this->crawlID, $this->siteID );
		
		
		//$CI->data['url'] = "http://getstatiq.com/sites/".$base_url;
		
		//$return['content'] = $CI->load->view('partials/crawldone', $this->data, true);
		
		//echo json_encode( $return );
		
	}
	
	
}

/* End of file Crawler.php */