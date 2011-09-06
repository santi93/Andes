<?php  

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

class flagManageGallery {

	var $mode = 'main';
	var $gid = false;
	var $pid = false;
	var $base_page = 'admin.php?page=flag-manage-gallery';
	var $search_result = false;
	
	// initiate the manage page
	function flagManageGallery() {

		// GET variables
		if(isset($_GET['gid']))
			$this->gid  = (int) $_GET['gid'];
		if(isset($_GET['pid']))
			$this->pid  = (int) $_GET['pid'];	
		if(isset($_GET['mode']))
			$this->mode = trim ($_GET['mode']);
		// Should be only called via manage galleries overview
		if ( $_POST['page'] == 'manage-galleries' )
			$this->post_processor_galleries();
		// Should be only called via a edit single gallery page	
		if ( $_POST['page'] == 'manage-images' )
			$this->post_processor_images();
		//Look for other POST process
		if ( !empty($_POST) || !empty($_GET) )
			$this->processor();
	
	}

	function controller() {

		switch($this->mode) {
			case 'sort':
				include_once (dirname (__FILE__) . '/manage-sort.php');
				flag_sortorder($this->gid);
			break;
			case 'edit':
				include_once (dirname (__FILE__) . '/manage-images.php');
				flag_picturelist();	
			break;
		  	case 'main':
			default:
				if(current_user_can('FlAG Upload images')){
					include_once (dirname (__FILE__) . '/addgallery.php');
					flag_admin_add_gallery();
				}
				include_once (dirname (__FILE__) . '/manage-galleries.php');
				flag_manage_gallery_main();
			break;
		}
	}

	function processor() {
		global $wpdb, $flag, $flagdb;
		
		// Delete a gallery
		if ($this->mode == 'delete') {

			check_admin_referer('flag_editgallery');
		
			// get the path to the gallery
			$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = '$this->gid' ");
			if ($gallerypath){
		
				// delete pictures
				$imagelist = $wpdb->get_col("SELECT filename FROM $wpdb->flagpictures WHERE galleryid = '$this->gid' ");
				if ($flag->options['deleteImg']) {
					if (is_array($imagelist)) {
						foreach ($imagelist as $filename) {
							@unlink(WINABSPATH . $gallerypath . '/thumbs/thumbs_' . $filename);
							@unlink(WINABSPATH . $gallerypath .'/'. $filename);
						}
					}
					// delete folder
						@rmdir( WINABSPATH . $gallerypath . '/thumbs' );
						@rmdir( WINABSPATH . $gallerypath );
				}
			}
	
			$delete_pic = $wpdb->query("DELETE FROM $wpdb->flagpictures WHERE galleryid = $this->gid");
			$delete_galllery = $wpdb->query("DELETE FROM $wpdb->flaggallery WHERE gid = $this->gid");
			
			if($delete_galllery) {
				
				$albums = $wpdb->get_results("SELECT id, categories FROM $wpdb->flagalbum WHERE categories LIKE '%{$this->gid}%' ");
				if($albums) {
					foreach ($albums as $album) {
						$strsearch = array(','.$this->gid, $this->gid.',', strval($this->gid) );
						$galstring = str_replace($strsearch,'',$album->categories);
						$wpdb->query( "UPDATE $wpdb->flagalbum SET categories = '{$galstring}' WHERE id = $album->id" );
					}
				}
			
				flagGallery::show_message( __ngettext( 'Gallery', 'Galleries', 1, 'flag' ) . ' \''.$this->gid.'\' '.__('deleted successfully','flag'));
				
			}
				
		 	$this->mode = 'main'; // show mainpage
		}
	
		// New Album
		if (isset($_POST['album_name'])) {

			check_admin_referer('flag_album');
			$newalbum = $wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->flagalbum (name) VALUES (%s)", $_POST['album_name']) );
			// and give me the new id
			$newalbum_id = (int) $wpdb->insert_id;
		
			if($newalbum)
				flagGallery::show_message( __( 'Album', 'flag' ) . ' \''.$_POST["album_name"].'\' '.__('successfully created','flag'));
				
		 	$this->mode = 'main'; // show mainpage
		}

		// Delete a picture
		if ($this->mode == 'delpic') {

			check_admin_referer('flag_delpicture');
			$image = $flagdb->find_image( $this->pid );
			if ($image) {
				if ($flag->options['deleteImg']) {
					@unlink($image->imagePath);
					@unlink($image->thumbPath);	
				} 
				$delete_pic = $wpdb->query("DELETE FROM $wpdb->flagpictures WHERE pid = $image->pid");
			}
			if($delete_pic)
				flagGallery::show_message( __('Picture','flag').' \''.$this->pid.'\' '.__('deleted successfully','flag') );
				
		 	$this->mode = 'edit'; // show pictures
	
		}
		
		// will be called after a ajax operation
		if (isset ($_POST['ajax_callback']))  {
				if ($_POST['ajax_callback'] == 1)
					flagGallery::show_message(__('Operation successful. Please clear your browser cache.','flag'));
		}
	
		if ( isset ($_POST['backToGallery']) )
			$this->mode = 'edit';
		
		// show sort order
		if ( isset ($_POST['sortGallery']) )
			$this->mode = 'sort';
		
		if ( isset ($_GET['s']) )	
			$this->search_images();
		
	}
		
	function post_processor_galleries() {
		global $wpdb, $flag, $flagdb;
		
		// bulk update in a single gallery
		if (isset ($_POST['bulkaction']) && isset ($_POST['doaction']))  {

			check_admin_referer('flag_bulkgallery');
			
			switch ($_POST['bulkaction']) {
				case 'no_action';
				// No action
					break;
				case 'import_meta':
				// Import Metadata
					// A prefix 'gallery_' will first fetch all ids from the selected galleries
					flagAdmin::do_ajax_operation( 'gallery_import_metadata' , $_POST['doaction'], __('Import metadata','flag') );
					break;
				case 'copy_meta':
				// Copy Metadata
					// A prefix 'gallery_' will first fetch all ids from the selected galleries
					flagAdmin::do_ajax_operation( 'gallery_copy_metadata' , $_POST['doaction'], __('Copy metadata to image Description','flag') );
					break;
			}
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_ResizeImages']))  {
			
			check_admin_referer('flag_thickbox_form');
			
			//save the new values for the next operation
			$flag->options['imgWidth']  = (int) $_POST['imgWidth'];
			$flag->options['imgHeight'] = (int) $_POST['imgHeight'];
			// What is in the case the user has no if cap 'FlAG Change options' ? Check feedback
			update_option('flag_options', $flag->options);
			
			$gallery_ids  = explode(',', $_POST['TB_imagelist']);
			// A prefix 'gallery_' will first fetch all ids from the selected galleries
			flagAdmin::do_ajax_operation( 'gallery_resize_image' , $gallery_ids, __('Resize images','flag') );
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_NewThumbnail']))  {
			
			check_admin_referer('flag_thickbox_form');
			
			//save the new values for the next operation
			$flag->options['thumbWidth']  = (int)  $_POST['thumbWidth'];
			$flag->options['thumbHeight'] = (int)  $_POST['thumbHeight'];
			$flag->options['thumbFix']    = (bool) $_POST['thumbFix']; 
			// What is in the case the user has no if cap 'FlAG Change options' ? Check feedback
			update_option('flag_options', $flag->options);
			
			$gallery_ids  = explode(',', $_POST['TB_imagelist']);
			// A prefix 'gallery_' will first fetch all ids from the selected galleries
			flagAdmin::do_ajax_operation( 'gallery_create_thumbnail' , $gallery_ids, __('Create new thumbnails','flag') );
		}

	}

	function post_processor_images() {
		global $wpdb, $flag, $flagdb;
		
		// bulk update in a single gallery
		if (isset ($_POST['bulkaction']) && isset ($_POST['doaction']))  {
			
			check_admin_referer('flag_updategallery');
			
			switch ($_POST['bulkaction']) {
				case 'no_action';
					break;
				case 'delete_images':
					if ( is_array($_POST['doaction']) ) {
						foreach ( $_POST['doaction'] as $imageID ) {
							$image = $flagdb->find_image( $imageID );
							if ($image) {
								if ($flag->options['deleteImg']) {
									@unlink($image->imagePath);
									@unlink($image->thumbPath);	
								} 
								$delete_pic = flagdb::delete_image( $image->pid );
							}
						}
						if($delete_pic)
							flagGallery::show_message(__('Pictures deleted successfully ','flag'));
					}
					break;
				case 'import_meta':
					flagAdmin::do_ajax_operation( 'import_metadata' , $_POST['doaction'], __('Import metadata','flag') );
					break;
				case 'copy_meta':
					flagAdmin::do_ajax_operation( 'copy_metadata' , $_POST['doaction'], __('Copy metadata to image Description','flag') );
					break;
			}
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_ResizeImages']))  {
			
			check_admin_referer('flag_thickbox_form');
			
			//save the new values for the next operation
			$flag->options['imgWidth']  = (int) $_POST['imgWidth'];
			$flag->options['imgHeight'] = (int) $_POST['imgHeight'];
			
			update_option('flag_options', $flag->options);
			
			$pic_ids  = explode(',', $_POST['TB_imagelist']);
			flagAdmin::do_ajax_operation( 'resize_image' , $pic_ids, __('Resize images','flag') );
		}

		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_NewThumbnail']))  {
			
			check_admin_referer('flag_thickbox_form');
			
			//save the new values for the next operation
			$flag->options['thumbWidth']  = (int)  $_POST['thumbWidth'];
			$flag->options['thumbHeight'] = (int)  $_POST['thumbHeight'];
			$flag->options['thumbFix']    = (bool) $_POST['thumbFix']; 
			update_option('flag_options', $flag->options);
			
			$pic_ids  = explode(',', $_POST['TB_imagelist']);
			flagAdmin::do_ajax_operation( 'create_thumbnail' , $pic_ids, __('Create new thumbnails','flag') );
		}
		
		if (isset ($_POST['TB_bulkaction']) && isset ($_POST['TB_SelectGallery']))  {
			
			check_admin_referer('flag_thickbox_form');
			
			$pic_ids  = explode(',', $_POST['TB_imagelist']);
			$dest_gid = (int) $_POST['dest_gid'];
			
			switch ($_POST['TB_bulkaction']) {
				case 'copy_to':
				// Copy images
					flagAdmin::copy_images( $pic_ids, $dest_gid );
					break;
				case 'move_to':
				// Move images
					flagAdmin::move_images( $pic_ids, $dest_gid );
					break;
			}
		}
		
		if (isset ($_POST['updatepictures']))  {
		// Update pictures	
		
			check_admin_referer('flag_updategallery');
		
			$gallery_title   = esc_attr($_POST['title']);
			$gallery_path    = esc_attr($_POST['path']);
			$gallery_desc    = esc_attr($_POST['gallerydesc']);
			$gallery_preview = (int) $_POST['previewpic'];
			
			$wpdb->query("UPDATE $wpdb->flaggallery SET title= '$gallery_title', path= '$gallery_path', galdesc = '$gallery_desc', previewpic = '$gallery_preview' WHERE gid = '$this->gid'");
	
			if (isset ($_POST['author']))  {		
				$gallery_author  = (int) $_POST['author'];
				$wpdb->query("UPDATE $wpdb->flaggallery SET author = '$gallery_author' WHERE gid = '$this->gid'");
			}
	
			$this->update_pictures();
	
			//hook for other plugin to update the fields
			do_action('flag_update_gallery', $this->gid, $_POST);
	
			flagGallery::show_message(__('Update successful',"flag"));
		}
	
		if (isset ($_POST['scanfolder']))  {
		// Rescan folder
			check_admin_referer('flag_updategallery');
		
			$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = '$this->gid' ");
			flagAdmin::import_gallery($gallerypath);
		}
	}
	
	function update_pictures() {
		global $wpdb;

		//TODO:Error message when update failed
		//TODO:Combine update in one query per image
		
		$description = 	$_POST['description'];
		$alttext = 		$_POST['alttext'];
		$exclude = 		$_POST['exclude'];
		$pictures = 	$_POST['pid'];
		
		if ( is_array($description) ) {
			foreach( $description as $key => $value ) {
				$desc = $wpdb->escape($value);
				$wpdb->query( "UPDATE $wpdb->flagpictures SET description = '$desc' WHERE pid = $key");
			}
		}
		if ( is_array($alttext) ){
			foreach( $alttext as $key => $value ) {
				$alttext = $wpdb->escape($value);
				$wpdb->query( "UPDATE $wpdb->flagpictures SET alttext = '$alttext' WHERE pid = $key");
			}
		}
		if ( is_array($pictures) ){
			foreach( $pictures as $pid ){
				$pid = (int) $pid;
				if (is_array($exclude)){
					if ( array_key_exists($pid, $exclude) )
						$wpdb->query("UPDATE $wpdb->flagpictures SET exclude = 1 WHERE pid = '$pid'");
					else 
						$wpdb->query("UPDATE $wpdb->flagpictures SET exclude = 0 WHERE pid = '$pid'");
				} else {
					$wpdb->query("UPDATE $wpdb->flagpictures SET exclude = 0 WHERE pid = '$pid'");
				}
			}
		}

		return;
	}

	// Check if user can select a author
	function get_editable_user_ids( $user_id, $exclude_zeros = true ) {
		global $wpdb;
	
		$user = new WP_User( $user_id );
	
		if ( ! $user->has_cap('FlAG Manage others gallery') ) {
			if ( $user->has_cap('FlAG Manage gallery') || $exclude_zeros == false )
				return array($user->id);
			else
				return false;
		}
	
		$level_key = $wpdb->prefix . 'user_level';
		$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$level_key'";
		if ( $exclude_zeros )
			$query .= " AND meta_value != '0'";
	
		return $wpdb->get_col( $query );
	}
	
	function search_images() {
		global $flagdb;
		
		if ( empty($_GET['s']) )
			return;
		//on what ever reason I need to set again the query var
		set_query_var('s', $_GET['s']);
		$request = get_search_query();
		// looknow for the images
		$this->search_result = $flagdb->search_for_images( $request );
		// show pictures page
		$this->mode = 'edit'; 
	}

}
?>
