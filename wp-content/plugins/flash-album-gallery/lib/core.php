<?php
/**
* Main PHP class for the WordPress plugin Flash Album Gallery
* 
*/
class flagGallery {
	
	/**
	* Show a error messages
	*/
	function show_error($message) {
		echo '<div class="wrap"><h2></h2><div class="error" id="error"><p>' . $message . '</p></div></div>' . "\n";
	}
	
	/**
	* Show a system messages
	*/
	function show_message($message) {
		echo '<div class="wrap"><h2></h2><div class="updated fade" id="message"><p>' . $message . '</p></div></div>' . "\n";
	}

	/**
	* get the thumbnail url to the image
	*/
	function get_thumbnail_url($imageID, $picturepath = '', $fileName = ''){
	
		// get the complete url to the thumbnail
		global $wpdb;
		
		// safety first
		$imageID = (int) $imageID;
		
		// get gallery values
		if ( empty($fileName) ) {
			list($fileName, $picturepath ) = $wpdb->get_row("SELECT p.filename, g.path FROM $wpdb->flagpictures AS p INNER JOIN $wpdb->flaggallery AS g ON (p.galleryid = g.gid) WHERE p.pid = '$imageID' ", ARRAY_N);
		}
		
		if ( empty($picturepath) ) {
			$picturepath = $wpdb->get_var("SELECT g.path FROM $wpdb->flagpictures AS p INNER JOIN $wpdb->flaggallery AS g ON (p.galleryid = g.gid) WHERE p.pid = '$imageID' ");
		}
		
		// set gallery url
		$folder_url 	= get_option ('siteurl') . '/' . $picturepath.flagGallery::get_thumbnail_folder($picturepath, FALSE);
		$thumbnailURL	= $folder_url . 'thumbs_' . $fileName;
		
		return $thumbnailURL;
	}
	
	/**
	* get the complete url to the image
	*/
	function get_image_url($imageID, $picturepath = '', $fileName = '') {		
		global $wpdb;

		// safety first
		$imageID = (int) $imageID;
		
		// get gallery values
		if (empty($fileName)) {
			list($fileName, $picturepath ) = $wpdb->get_row("SELECT p.filename, g.path FROM $wpdb->flagpictures AS p INNER JOIN $wpdb->flaggallery AS g ON (p.galleryid = g.gid) WHERE p.pid = '$imageID' ", ARRAY_N);
		}

		if (empty($picturepath)) {
			$picturepath = $wpdb->get_var("SELECT g.path FROM $wpdb->flagpictures AS p INNER JOIN $wpdb->flaggallery AS g ON (p.galleryid = g.gid) WHERE p.pid = '$imageID' ");
		}
		
		// set gallery url
		$imageURL 	= get_option ('siteurl') . '/' . $picturepath . '/' . $fileName;
		
		return $imageURL;	
	}

	/**
	* flagGallery::get_thumbnail_folder()
	* 
	* @param mixed $gallerypath
	* @param bool $include_Abspath
	* @return string $foldername
	*/
	function create_thumbnail_folder($gallerypath, $include_Abspath = TRUE) {
		if (!$include_Abspath) {
			$gallerypath = WINABSPATH . $gallerypath;
		}
		
		if (!file_exists($gallerypath)) {
			return FALSE;
		}
		
		if (is_dir($gallerypath . '/thumbs/')) {
			return '/thumbs/';
		}
		
		if (is_admin()) {
			if (!is_dir($gallerypath . '/thumbs/')) {
				if ( !wp_mkdir_p($gallerypath . '/thumbs/') ) {
					if (SAFE_MODE) {
						flagAdmin::check_safemode($gallerypath . '/thumbs/');	
					} else {
						flagGallery::show_error(__('Unable to create directory ', 'flag') . $gallerypath . '/thumbs !');
					}
					return FALSE;
				}
				return '/thumbs/';
			}
		}
		
		return FALSE;
		
	}

	/**
	* flagGallery::get_thumbnail_folder()
	* 
	* @param mixed $gallerypath
	* @param bool $include_Abspath
	* @deprecated use create_thumbnail_folder() if needed;
	* @return string $foldername
	*/
	function get_thumbnail_folder($gallerypath, $include_Abspath = TRUE) {
		return flagGallery::create_thumbnail_folder($gallerypath, $include_Abspath);
	}
	
	/**
	* flagGallery::get_thumbnail_prefix() - obsolete
	* 
	* @param string $gallerypath
	* @param bool   $include_Abspath
	* @deprecated prefix is now fixed to "thumbs_";
	* @return string  "thumbs_";
	*/
	function get_thumbnail_prefix($gallerypath, $include_Abspath = TRUE) {
		return 'thumbs_';		
	}
	
	/**
	 * flagGallery::graphic_library() - switch between GD and ImageMagick
	 * 
	 * @return path to the selected library
	 */
	function graphic_library() {
		
		return FLAG_ABSPATH . '/lib/gd.thumbnail.inc.php';
		
	}
	
	/**
	 * Support for i18n with polyglot or qtrans
	 * 
	 * @param string $in
	 * @return string $in localized
	 */
	function i18n($in) {
		
		if ( function_exists( 'langswitch_filter_langs_with_message' ) )
			$in = langswitch_filter_langs_with_message($in);
				
		if ( function_exists( 'polyglot_filter' ))
			$in = polyglot_filter($in);
		
		if ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ))
			$in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($in);
		
		$in = apply_filters('localization', $in);
		
		return $in;
	}
	
	/**
	 * Check the memory_limit and calculate a recommended memory size
	 * 
	 * @return string message about recommended image size
	 */
	function check_memory_limit() {

		if ( (function_exists('memory_get_usage')) && (ini_get('memory_limit')) ) {
			
			// get memory limit
			$memory_limit = ini_get('memory_limit');
			if ($memory_limit != '')
				$memory_limit = substr($memory_limit, 0, -1) * 1024 * 1024;
			
			// calculate the free memory 	
			$freeMemory = $memory_limit - memory_get_usage();
			
			// build the test sizes
			$sizes = array();
			$sizes[] = array ( 'width' => 800, 'height' => 600);
			$sizes[] = array ( 'width' => 1024, 'height' => 768);
			$sizes[] = array ( 'width' => 1280, 'height' => 960);  // 1MP	
			$sizes[] = array ( 'width' => 1600, 'height' => 1200); // 2MP
			$sizes[] = array ( 'width' => 2016, 'height' => 1512); // 3MP
			$sizes[] = array ( 'width' => 2272, 'height' => 1704); // 4MP
			$sizes[] = array ( 'width' => 2560, 'height' => 1920); // 5MP
			
			// test the classic sizes
			foreach ($sizes as $size){
				// very, very rough estimation
				if ($freeMemory < round( $size['width'] * $size['height'] * 5.09 )) {
                	$result = sprintf(  __( 'Note : Based on your server memory limit you should not upload larger images then <strong>%d x %d</strong> pixel', 'flag' ), $size['width'], $size['height']); 
					return $result;
				}
			}
		}
		return;
	}
	
	/**
	 * Slightly modfifed version of pathinfo(), clean up filename & rename jpeg to jpg
	 * 
	 * @param string $name The name being checked. 
	 * @return array containing information about file
	 */
	function fileinfo( $name ) {
		
		//Sanitizes a filename replacing whitespace with dashes
		$name = sanitize_file_name($name);
		
		//get the parts of the name
		$filepart = pathinfo ( strtolower($name) );
		
		if ( empty($filepart) )
			return false;
		
		// required until PHP 5.2.0
		if ( empty($filepart['filename']) ) 
			$filepart['filename'] = substr($filepart['basename'],0 ,strlen($filepart['basename']) - (strlen($filepart['extension']) + 1) );
		
		$filepart['filename'] = sanitize_title_with_dashes( $filepart['filename'] );
		
		//extension jpeg will not be recognized by the slideshow, so we rename it
		$filepart['extension'] = ($filepart['extension'] == 'jpeg') ? 'jpg' : $filepart['extension'];
		
		//combine the new file name
		$filepart['basename'] = $filepart['filename'] . '.' . $filepart['extension'];
		
		return $filepart;
	}

	/**
	 * Function used to delete a folder.
	 * @param $path full-path to folder
	 * @return bool result of deletion
	 */
	function flagFolderDelete($path) {
		if (is_dir($path)) {
			if (version_compare(PHP_VERSION, '5.0.0') < 0) {
				$entries = array();
				if ($handle = opendir($path)) {
					while (false !== ($file = readdir($handle))) $entries[] = $file;
					closedir($handle);
				}
			} else {
				$entries = scandir($path);
				if ($entries === false) $entries = array();
			}
			foreach ($entries as $entry) {
				if ($entry != '.' && $entry != '..') {
					flagGallery::flagFolderDelete($path.'/'.$entry);
				}
			}
			return @rmdir($path);
		} elseif (file_exists($path)) {
			return @unlink($path);
		} else {
			return false;
		}
	}

	/*
	 * Save file
	 * @param $sName    - file name
	 * @param $sContent - file content
	 * @param $mode     - open file mode
	 * @return the number of bytes written, or FALSE on error.
	 */

	function saveFile($sName,$sContent,$mode='w+') {
		if (!$dFile=fopen($sName, $mode)) {
			flagGallery::show_error(__("Can't create/open file '","flag").$sName."'.");
			exit;
		}
		flock ($dFile,LOCK_EX);
		ftruncate ($dFile,0);
		if ( $result=fwrite($dFile,$sContent) === FALSE) {
	        flagGallery::show_error(__("Can't write data to file '","flag").$sName."'.");
	        exit;
	    }
		fflush ($dFile);
		flock ($dFile,LOCK_UN);
		fclose ($dFile);
		return $result;
	}
	
	
	function flag_wpmu_enable_function($value) {
		if (IS_WPMU) {
			$flag_options = get_site_option('flag_options');
			return $flag_options[$value];
		}
		// if this is not WPMU, enable it !
		return true;
	}

	function flagGetBetween($content,$start,$end){
	    $r = explode($start, $content);
	    if (isset($r[1])){
	        $r = explode($end, $r[1]);
	        return $r[0];
	    }
	    return '';
	}
	
	function getUserNow($userAgent) {
	    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
	    'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
	    'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|yandex';
	    $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
	    return $isCrawler;
	}
	
	function flagSaveWpMedia() {
	   	if ( !empty($_POST['item_a']) )
	    foreach ( $_POST['item_a'] as $item_id => $item ) {
			$post = $_post = get_post($item_id, ARRAY_A);
			$postmeta = get_post_meta($item_id, 'thumbnail', true);
			$postlink = get_post_meta($item_id, 'link', true);
			$postpreview = get_post_meta($item_id, 'preview', true);
			if ( isset($item['post_content']) )
				$post['post_content'] = $item['post_content'];
			if ( isset($item['post_title']) )
				$post['post_title'] = $item['post_title'];

			$post = apply_filters('attachment_fields_to_save', $post, $item);

	        if( isset($item['post_thumb']) && $item['post_thumb'] != $postmeta ) {
	            /*$thumb = image_resize( $item['post_thumb'], $max_w=200, $max_h=200, $crop = true, $suffix = null, $dest_path = null, $jpeg_quality = 90 );
	            if(is_string($thumb))
	                update_post_meta($item_id, 'thumbnail', $thumb);
	            else*/
	                update_post_meta($item_id, 'thumbnail', $item['post_thumb']);
	        }
	        if( isset($item['link']) && $item['link'] != $postlink ) {
                update_post_meta($item_id, 'link', $item['link']);
	        }
	        if( isset($item['preview']) && $item['preview'] != $postpreview ) {
                update_post_meta($item_id, 'preview', $item['preview']);
	        }
			if ( isset($post['errors']) ) {
				$errors[$item_id] = $post['errors'];
				unset($post['errors']);
			}
			if ( $post != $_post )
				wp_update_post($post);
		}
	}

}

?>