<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
	
	// sometimes a error feedback is better than a white screen
	@ini_set('error_reporting', E_ALL ^ E_NOTICE);

	function flag_admin_add_gallery()  {

	global $wpdb, $flagdb, $flag;
	
	// same as $_SERVER['REQUEST_URI'], but should work under IIS 6.0
	$filepath    = admin_url() . 'admin.php?page=' . $_GET['page'];
	
	// check for the max image size
	$maxsize    = flagGallery::check_memory_limit();
	
	// link for the flash file
	$swf_upload_link = FLAG_URLPATH . 'admin/upload.php';
	$swf_upload_link = wp_nonce_url($swf_upload_link, 'flag_swfupload');
	//flash doesn't seem to like encoded ampersands, so convert them back here
	$swf_upload_link = str_replace('&#038;', '&', $swf_upload_link);

	$defaultpath = $flag->options['galleryPath'];

	if ($_POST['addgallery']){
		check_admin_referer('flag_addgallery');
		$newgallery = attribute_escape( $_POST['galleryname']);
		if ( !empty($newgallery) )
			flagAdmin::create_gallery($newgallery, $defaultpath);
	}
	if ($_POST['uploadimage']){
		check_admin_referer('flag_addgallery');
		if ($_FILES['MF__F_0_0']['error'] == 0) {
			$messagetext = flagAdmin::upload_images();
		}
		else
			flagGallery::show_error( __('Upload failed!','flag') );
	}
	if ($_POST['importfolder']){
		check_admin_referer('flag_addgallery');
		$galleryfolder = $_POST['galleryfolder'];
		if ( ( !empty($galleryfolder) ) AND ($defaultpath != $galleryfolder) )
			flagAdmin::import_gallery($galleryfolder);
	}


	if (isset($_POST['swf_callback'])){
		if ($_POST['galleryselect'] == "0" )
			flagGallery::show_error(__('No gallery selected !','flag'));
		else {
			// get the path to the gallery
			$galleryID = (int) $_POST['galleryselect'];
			$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->flaggallery WHERE gid = '$galleryID' ");
			flagAdmin::import_gallery($gallerypath);
		}	
	}

	if ( isset($_POST['disable_flash']) ){
		check_admin_referer('flag_addgallery');
		$flag->options['swfUpload'] = false;	
		update_option('flag_options', $flag->options);
	}

	if ( isset($_POST['enable_flash']) ){
		check_admin_referer('flag_addgallery');
		$flag->options['swfUpload'] = true;	
		update_option('flag_options', $flag->options);
	}

	//get all galleries (after we added new ones)
	$gallerylist = $flagdb->find_all_galleries('gid', 'DESC');

?>
	
<?php if( !IS_WPMU || current_user_can('FlAG Import folder') ) { ?>
	<link rel="stylesheet" type="text/css" href="<?php echo FLAG_URLPATH; ?>admin/js/jqueryFileTree/jqueryFileTree.css" />
	<script type="text/javascript" src="<?php echo FLAG_URLPATH; ?>admin/js/jqueryFileTree/jqueryFileTree.js"></script>
	<script type="text/javascript">
	/* <![CDATA[ */
		  jQuery(function() {								 
		    jQuery("#file_browser").fileTree({
		      root: "<?php echo WINABSPATH; ?>",
		      script: "<?php echo FLAG_URLPATH; ?>admin/js/jqueryFileTree/connectors/jqueryFileTree.php",
		    }, function(file) {
		        var path = file.replace("<?php echo WINABSPATH; ?>", "");
		        jQuery("#galleryfolder").val(path);
		    });
		    
		    jQuery("span.browsefiles").show().click(function(){
		    	jQuery("#file_browser").slideToggle();
		    });	
		  });
	/* ]]> */
	</script>
<?php }
if($flag->options['swfUpload']) { ?>
	<!-- SWFUpload script -->
	<script type="text/javascript">
	/* <![CDATA[ */
		var flag_swf_upload;
			
		window.onload = function () {
			flag_swf_upload = new SWFUpload({
				// Backend settings
				upload_url : "<?php echo $swf_upload_link; ?>",
				flash_url : "<?php echo FLAG_URLPATH; ?>admin/js/swfupload.swf",
				
				// Button Settings
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 300,
				button_height: 27,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
								
				// File Upload Settings
				file_size_limit : "<?php echo wp_max_upload_size(); ?>b",
				file_types : "*.jpg;*.gif;*.png",
				file_types_description : "<?php _e('Image Files', 'flag'); ?>",
				
				// Queue handler
				file_queued_handler : fileQueued,
				
				// Upload handler
				upload_start_handler : uploadStart,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,
				
				post_params : {
					"auth_cookie" : "<?php echo $_COOKIE[AUTH_COOKIE]; ?>",
					"galleryselect" : "0"
				},
				
				// i18names
				custom_settings : {
					"remove" : "<?php _e('remove', 'flag'); ?>",
					"browse" : "<?php _e('Browse...', 'flag'); ?>",
					"upload" : "<?php _e('Upload images', 'flag'); ?>"
				},

				// Debug settings
				debug: false
				
			});
			
			// on load change the upload to swfupload
			initSWFUpload();
			
		};
	/* ]]> */
	</script>
	<div class="wrap" id="progressbar-wrap" style="display:none;">
		<div class="progressborder">
			<div class="progressbar" id="progressbar">
				<span>0%</span>
			</div>
		</div>
	</div>
<?php } else { ?>
	<!-- MultiFile script -->
	<script type="text/javascript">	
	/* <![CDATA[ */
		jQuery(document).ready(function(){
			jQuery('#imagefiles').MultiFile({
				STRING: {
			    	remove:'<?php _e('remove', 'flag'); ?>'
  				}
		 	});
		});
	/* ]]> */
	</script>
	<?php } ?>
		
	<div id="slider" class="wrap">
	
		<ul id="tabs" class="tabs">
			<li class="selected"><a href="#" rel="addgallery"><?php _e('Add new gallery', 'flag'); ?></a></li>
			<li><a href="#" rel="uploadimage"><?php _e('Upload Images', 'flag'); ?></a></li>
<?php if( !IS_WPMU || current_user_can('FlAG Import folder') ) { ?>
			<li><a href="#" rel="importfolder"><?php _e('Import image folder', 'flag'); ?></a></li>
<?php } ?>
		</ul>

		<!-- create gallery -->
		<div id="addgallery" class="cptab">
			<h2><?php _e('Create a new gallery', 'flag'); ?></h2>
			<form name="addgallery" id="addgallery_form" method="POST" action="<?php echo $filepath; ?>" accept-charset="utf-8" >
			<?php wp_nonce_field('flag_addgallery'); ?>
				<table class="form-table" style="width: auto;"> 
				<tr>
					<th scope="col" colspan="2" style="padding-bottom: 0;"><strong><?php _e('New Gallery', 'flag'); ?></strong></th> 
				</tr>
				<tr valign="top"> 
					<td><input type="text" size="65" name="galleryname" value="" /><br />
					<?php if(!IS_WPMU) { ?>
						<?php _e('Create a new , empty gallery below the folder', 'flag'); ?>  <strong><?php echo $defaultpath; ?></strong><br />
					<?php } ?>
						<i>( <?php _e('Allowed characters for file and folder names are', 'flag'); ?>: a-z, A-Z, 0-9, -, _ )</i></td>
					<?php do_action('flag_add_new_gallery_form'); ?>
					<td><div class="submit" style="margin: 0; padding: 0;"><input class="button-primary" type="submit" name= "addgallery" value="<?php _e('Add gallery', 'flag'); ?>"/></div></td>
				</tr>
				</table>
				<p>&nbsp;</p>
			</form>
		</div>
		<!-- upload images -->
		<div id="uploadimage" class="cptab">
			<h2><?php _e('Upload images', 'flag'); ?></h2>
	<script type="text/javascript">	
	/* <![CDATA[ */
		jQuery(document).ready(function(){
			if(jQuery("#galleryselect").val() == 0) {
				jQuery("#choosegalfirst").animate({opacity: "0.5"}, 600);
				jQuery("#choosegalfirst .disabledbut").show();
			}
			jQuery("#choosegalfirst .disabledbut").click(function () {
				alert("Choose gallery, please.")
			});
			jQuery("#galleryselect").change(function () {
				if(jQuery(this).val() == 0) {
					jQuery("#choosegalfirst .disabledbut").show();
					jQuery("#choosegalfirst").animate({opacity: "0.5"}, 600);
				} else { 
					jQuery("#choosegalfirst .disabledbut").hide();
					jQuery("#choosegalfirst").animate({opacity: "1"}, 600);
				}
			});
		});
	/* ]]> */
	</script>
			<form name="uploadimage" id="uploadimage_form" method="POST" enctype="multipart/form-data" action="<?php echo $filepath; ?>" accept-charset="utf-8" >
			<?php wp_nonce_field('flag_addgallery'); ?>
				<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row"><strong><?php _e('Upload image(s):', 'flag'); ?></strong></th>
					<td><span id='spanButtonPlaceholder'></span><input type="file" name="imagefiles[]" id="imagefiles" size="35" class="imagefiles"/></td>
				</tr>
				<tr valign="top"> 
					<td colspan="2"><label for="galleryselect"><?php _e('in to', 'flag'); ?></label> 
						<select name="galleryselect" id="galleryselect">
							<option value="0" ><?php _e('Choose gallery', 'flag'); ?></option>
							<?php $ingallery = isset($_GET['gid']) ? (int) $_GET['gid'] : '';
							foreach($gallerylist as $gallery) {
									if ( !flagAdmin::can_manage_this_gallery($gallery->author) )
										continue;
									$name = ( empty($gallery->title) ) ? $gallery->name : $gallery->title;
									$sel = ($ingallery == $gallery->gid) ? 'selected="selected" ' : '';
									echo '<option ' . $sel . 'value="' . $gallery->gid . '" >' . $gallery->gid . ' - ' . $name . '</option>' . "\n";
							} ?>
						</select>
						<?php echo $maxsize; ?>
						<br /><?php if ((IS_WPMU) && flagGallery::flag_wpmu_enable_function('wpmuQuotaCheck')) display_space_usage(); ?>
					</td>
				</tr> 
				</table>
				<div class="submit">
					<span class="useflashupload">
					<?php if ($flag->options['swfUpload']) { ?>
					<input type="submit" name="disable_flash" id="disable_flash" title="<?php _e('The batch upload requires Adobe Flash 10, disable it if you have problems','flag'); ?>" value="<?php _e('Disable flash upload', 'flag'); ?>" />
					<?php } else { ?>
					<input type="submit" name="enable_flash" id="enable_flash" title="<?php _e('Upload multiple files at once by ctrl/shift-selecting in dialog','flag'); ?>" value="<?php _e('Enable flash based upload', 'flag'); ?>" />
					<?php } ?>
					</span>
					<span id="choosegalfirst"><input class="button-primary" type="submit" name="uploadimage" id="uploadimage_btn" value="<?php _e('Upload images', 'flag'); ?>" /><span class="disabledbut" style="display: none;"></span></span>
					<div class="clear"></div>
				</div>
			</form>
		</div>
<?php if( !IS_WPMU || current_user_can('FlAG Import folder') ) { ?>
		<!-- import folder -->
		<div id="importfolder" class="cptab">
		<h2><?php _e('Import image folder', 'flag'); ?></h2>
			<form name="importfolder" id="importfolder_form" method="POST" action="<?php echo $filepath; ?>" accept-charset="utf-8" >
			<?php wp_nonce_field('flag_addgallery'); ?>
				<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row"><?php _e('Import from Server path:', 'flag'); ?></th> 
					<td><input type="text" size="35" id="galleryfolder" name="galleryfolder" value="<?php echo $defaultpath; ?>" /><span class="browsefiles button" style="display:none"><?php _e('Toggle DIR Browser',"flag"); ?></span>
					<div id="file_browser"></div>
					<div><?php echo $maxsize; ?>
					<?php if (SAFE_MODE) {?><br /><?php _e(' Please note : For safe-mode = ON you need to add the subfolder thumbs manually', 'flag'); ?><?php }; ?></div></td> 
				</tr>
				</table>
				<div class="submit"><input class="button-primary" type="submit" name="importfolder" value="<?php _e('Import folder', 'flag'); ?>"/></div>
			</form>
		</div>
<?php } ?>

<script type="text/javascript">
	var cptabs=new ddtabcontent("tabs");
	cptabs.setpersist(true);
	cptabs.setselectedClassTarget("linkparent");
	cptabs.init();
</script>
</div>
<?php
	}
?>