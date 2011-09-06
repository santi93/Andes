<?php  
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

	function flag_wpmu_setup()  {	
		global $wpdb;
	
	//to be sure
	if (!is_site_admin())
 		die('You are not allowed to call this page.');

	// get the options
	$flag_options = get_site_option('flag_options');
	
	// same as $_SERVER['REQUEST_URI'], but should work under IIS 6.0
	$filepath    = site_url( 'wp-admin/wpmu-admin.php?page=' . $_GET['page'], 'admin' );

	if ( isset($_POST['updateoption']) ) {	
		check_admin_referer('flag_wpmu_settings');
		// get the hidden option fields, taken from WP core
		if ( $_POST['page_options'] )	
			$options = explode(',', stripslashes($_POST['page_options']));
		if ($options) {
			foreach ($options as $option) {
				$option = trim($option);
				$value = trim($_POST[$option]);
		//		$value = sanitize_option($option, $value); // This does strip slashes on those that need it
				$flag_options[$option] = $value;
			}
		}

		update_site_option('flag_options', $flag_options);
	 	$messagetext = __('Update successfully','flag');
	}		
	
	// message windows
	if(!empty($messagetext)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$messagetext.'</p></div>'; }
	
	?>

	<div class="wrap">
		<h2><?php _e('General WordPress MU Settings','flag'); ?></h2>
		<form name="generaloptions" method="post">
		<?php wp_nonce_field('flag_wpmu_settings'); ?>
		<input type="hidden" name="page_options" value="gallerypath,wpmuQuotaCheck,wpmuRoles" />
			<table class="form-table">
				<tr valign="top">
					<th align="left"><?php _e('Gallery path','flag'); ?></th>
					<td><input type="text" size="50" name="gallerypath" value="<?php echo $flag_options[gallerypath]; ?>" title="TEST" /><br />
					<?php _e('This is the default path for all blogs. With the placeholder %BLOG_ID% you can organize the folder structure better. The path must end with a /.','flag'); ?></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable upload quota check','flag'); ?>:</th>
					<td><input name="wpmuQuotaCheck" type="checkbox" value="1" <?php checked('1', $flag_options[wpmuQuotaCheck]); ?> />
					<?php _e('Should work if the gallery is bellow the blog.dir','flag'); ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Enable roles/capabilities','flag'); ?>:</th>
					<td><input name="wpmuRoles" type="checkbox" value="1" <?php checked('1', $flag_options[wpmuRoles]); ?> />
					<?php _e('Allow users to change the roles for other blog authors.','flag'); ?>
					</td>
				</tr>
			</table> 				
			<div class="submit"><input type="submit" name="updateoption" value="<?php _e('Update'); ?> &raquo;"/></div>
		</form>	
	</div>	

	<?php
}	
?>