<?php
/**
 * Install a skin from a local file.
 *
 */
function do_skin_install_local_package($package, $filename = '') {
	global $wp_filesystem;

	if ( empty($package) ) {
		show_message( __('No skin Specified', 'flag') );
		return false;
	}

	if ( empty($filename) )
		$filename = basename($package);

	$url = 'admin.php?page=flag-skins&action=upload&tabs=1';
	$url = add_query_arg(array('package' => $filename), $url);

	$url = wp_nonce_url($url, 'skin-upload');
	if ( false === ($credentials = request_filesystem_credentials($url)) )
		return false;

	if ( ! WP_Filesystem($credentials) ) {
		request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
		return false;
	}

	if ( $wp_filesystem->errors->get_error_code() ) {
		foreach ( $wp_filesystem->errors->get_error_messages() as $message )
			show_message($message);
		return false;
	}

	$result = wp_install_skin_local_package( $package, 'show_message' );

	if ( is_wp_error($result) ) {
		show_message($result);
		show_message( __('Installation Failed', 'flag') );
		return false;
	} else {
		show_message( __('The skin installed successfully.', 'flag') );
		$skin_file = basename($result);
		$install_actions = apply_filters('install_skin_complete_actions', array(
							'activate_skin' => '<a href="'.admin_url('admin.php?page=flag-skins&skin='.$skin_file).'" title="' . __('Activate this skin', 'flag') . '" target="_parent">' . __('Activate Skin', 'flag') . '</a>',
							'skins_page' => '<a href="#'.$skin_file.'" title="' . __('Goto skin overview', 'flag') . '" target="_parent">' . __('Skin overview', 'flag') . '</a>'
							), array(), $skin_file);
		if ( ! empty($install_actions) ) {
			//show_message('<strong>' . __('Actions:', 'flag') . '</strong> ' . implode(' | ', (array)$install_actions));
		}
		return $result;
	}
}

/**
 * Install skin from local package
 *
 */
function wp_install_skin_local_package($package, $feedback = '') {
	global $wp_filesystem;

	if ( !empty($feedback) )
		add_filter('install_feedback', $feedback);

	// Is a filesystem accessor setup?
	if ( ! $wp_filesystem || ! is_object($wp_filesystem) )
		WP_Filesystem();

	if ( ! is_object($wp_filesystem) )
		return new WP_Error('fs_unavailable', __('Could not access filesystem.', 'flag'));

	if ( $wp_filesystem->errors->get_error_code() )
		return new WP_Error('fs_error', __('Filesystem error', 'flag'), $wp_filesystem->errors);

	//Get the base skin folder
	$flag_options = get_option('flag_options');
	$skins_dir = $flag_options['skinsDirABS'];
	if ( empty($skins_dir) )
		return new WP_Error('fs_no_skins_dir', __('Unable to locate FlAGallery Skin directory.', 'flag'));

	//And the same for the Content directory.
	$content_dir = $wp_filesystem->wp_content_dir();
	if( empty($content_dir) )
		return new WP_Error('fs_no_content_dir', __('Unable to locate WordPress Content directory (wp-content).', 'flag'));

	$skins_dir = trailingslashit( $skins_dir );
	$content_dir = trailingslashit( $content_dir );

	if ( empty($package) )
		return new WP_Error('no_package', __('Install package not available.', 'flag'));

	$working_dir = $content_dir . 'upgrade/' . basename($package, '.zip');

	// Clean up working directory
	if ( $wp_filesystem->is_dir($working_dir) )
		$wp_filesystem->delete($working_dir, true);

	apply_filters('install_feedback', __('Unpacking the skin package', 'flag'));
	// Unzip package to working directory
	$result = unzip_file($package, $working_dir);

	// Once extracted, delete the package
	unlink($package);

	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the skin
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	if( $wp_filesystem->exists( $skins_dir . $filelist[0] ) ) {
		$wp_filesystem->delete($working_dir, true);
		return new WP_Error('install_folder_exists', __('Folder already exists.', 'flag'), $filelist[0] );
	}

	apply_filters('install_feedback', __('Installing the skin', 'flag'));
	// Copy new version of skin into place.
	$result = copy_dir($working_dir, $skins_dir);
	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($working_dir, true);
		return $result;
	}

	//Get a list of the directories in the working directory before we delete it, We need to know the new folder for the skin
	$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );

	// Remove working directory
	$wp_filesystem->delete($working_dir, true);

	if( empty($filelist) )
		return false; //We couldnt find any files in the working dir, therefor no skin installed? Failsafe backup.

	$folder = $filelist[0];
	//$skin = get_skins('/' . $folder); //Ensure to pass with leading slash
	//$skinfiles = array_keys($skin); //Assume the requested skin is the first in the list

	//Return the skin files name.
	return  $skins_dir.$folder . '/';
}

?>
