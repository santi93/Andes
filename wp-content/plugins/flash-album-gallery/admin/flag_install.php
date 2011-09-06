<?php
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/**
 * creates all tables for the gallery
 * called during register_activation hook
 * 
 * @access internal
 * @return void
**/

function flag_install () {
	global $wpdb, $wp_version;

	// Check for capability
	if ( !current_user_can('activate_plugins') ) 
		return;

	flag_capabilities();
	
	// upgrade function changed in WordPress 2.3	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	// add charset & collate like wp core
	$charset_collate = '';

	if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}
		
    $flagpictures					= $wpdb->prefix . 'flag_pictures';
	$flaggallery					= $wpdb->prefix . 'flag_gallery';
	$flagcomments					= $wpdb->prefix . 'flag_comments';
	$flagalbum						= $wpdb->prefix . 'flag_album';
   
	if($wpdb->get_var("show tables like '$flagpictures'") != $flagpictures) {

		$sql = "CREATE TABLE " . $flagpictures . " (
		pid BIGINT(20) NOT NULL AUTO_INCREMENT ,
		galleryid BIGINT(20) DEFAULT '0' NOT NULL ,
		filename VARCHAR(255) NOT NULL ,
		description MEDIUMTEXT NULL ,
		alttext MEDIUMTEXT NULL ,
		imagedate DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
		exclude TINYINT NULL DEFAULT '0',
		sortorder BIGINT(20) DEFAULT '0' NOT NULL ,
		location TEXT,
		city TINYTEXT,
		state TINYTEXT,
		country TINYTEXT,
		credit TEXT,
		copyright TEXT,
		commentson INT(1) UNSIGNED NOT NULL DEFAULT '1',
		hitcounter INT(11) UNSIGNED DEFAULT '0',
		total_value INT(11) UNSIGNED DEFAULT '0',
		total_votes INT(11) UNSIGNED DEFAULT '0',
		used_ips LONGTEXT,
		meta_data LONGTEXT,
		PRIMARY KEY pid (pid)
		) $charset_collate;";
	
      dbDelta($sql);
    }


	if($wpdb->get_var("show tables like '$flagallery'") != $flaggallery) {
      
		$sql = "CREATE TABLE " . $flaggallery . " (
		gid BIGINT(20) NOT NULL AUTO_INCREMENT ,
		name VARCHAR(255) NOT NULL ,
		path MEDIUMTEXT NULL ,
		title MEDIUMTEXT NULL ,
		galdesc MEDIUMTEXT NULL ,
		previewpic BIGINT(20) NULL DEFAULT '0' ,
		sortorder BIGINT(20) DEFAULT '0' NOT NULL ,
		author BIGINT(20) NOT NULL DEFAULT '0' ,
		PRIMARY KEY gid (gid)
		) $charset_collate;";
	
      dbDelta($sql);
    }

	if($wpdb->get_var("show tables like '$flagcomments'") != $flagcomments) {
		$sql = "CREATE TABLE " . $flagcomments . " (
		cid int(11) unsigned NOT NULL auto_increment,
		ownerid int(11) unsigned NOT NULL default '0',
		name varchar(255) NOT NULL default '',
		email varchar(255) NOT NULL default '',
		website varchar(255) default NULL,
		date datetime default NULL,
		comment text,
		inmoderation int(1) unsigned NOT NULL default '0',
		PRIMARY KEY  (cid),
		KEY ownerid (ownerid)
		) $charset_collate;";
	
      dbDelta($sql);
	}

	if( !$wpdb->get_var( "SHOW TABLES LIKE '$flagalbum'" )) {
      
		$sql = "CREATE TABLE " . $flagalbum . " (
		id BIGINT(20) NOT NULL AUTO_INCREMENT ,
		name VARCHAR(255) NOT NULL ,
		previewpic BIGINT(20) DEFAULT '0' NOT NULL ,
		albumdesc MEDIUMTEXT NULL ,
		categories LONGTEXT NOT NULL,
		PRIMARY KEY id (id)
		) $charset_collate;";
	
      dbDelta($sql);
    }


	// check one table again, to be sure
	if( !$wpdb->get_var( "SHOW TABLES LIKE '$flagpictures'" ) ) {
		update_option( "flag_init_check", __('Flash Album Gallery : Tables could not created, please check your database settings','flag') );
		return;
	}

	$options = get_option('flag_options');
	// set the default settings, if we didn't upgrade
	if ( empty( $options ) )	
 		flag_default_options();
 	
	
	// if all is passed , save the VERSIONs
	add_option("flag_db_version", FLAG_DBVERSION);
	add_option("flagVersion", FLAGVERSION);
}

function flag_capabilities() {
	global $wp_roles;

	// Set the capabilities for the administrator
	$role = get_role('administrator');
	// We need this role, no other chance
	if ( empty($role) ) {
		update_option( "flag_init_check", __('Sorry, Flash Album Gallery works only with a role called administrator','flag') );
		return;
	}
	
	$role->add_cap('FlAG overview');
	$role->add_cap('FlAG Use TinyMCE');
	$role->add_cap('FlAG Upload images');
	$role->add_cap('FlAG Import folder');
	$role->add_cap('FlAG Manage gallery');
	$role->add_cap('FlAG Manage others gallery');
	$role->add_cap('FlAG Change skin');
	$role->add_cap('FlAG Add skins');
	$role->add_cap('FlAG Delete skins');
	$role->add_cap('FlAG Change options');
	$role->add_cap('FlAG Manage music');
	$role->add_cap('FlAG Manage video');
	$role->add_cap('FlAG Manage banners');
	$role->add_cap('FlAG Facebook page');

}

/**
 * Setup the default option array for the gallery
 * 
 * @access internal
 * @return void
 */
function flag_default_options() {
	
	global $blog_id, $flag;

	$flag_options = flag_list_options();
	// special overrides for WPMU	
	if (IS_WPMU) {
		// get the site options
		$flag_wpmu_options = get_site_option('flag_options');
		// get the default value during installation
		if (!is_array($flag_wpmu_options)) {
			$flag_wpmu_options['galleryPath'] = 'wp-content/blogs.dir/%BLOG_ID%/files/';
			update_site_option('flag_options', $flag_wpmu_options);
		}
		$flag_options['galleryPath']  		= str_replace("%BLOG_ID%", $blog_id , $flag_wpmu_options['galleryPath']);
	} 

	update_option('flag_options', $flag_options);

}

function flag_list_options() {
	$flag_options['galleryPath']			= 'wp-content/flagallery/';  		// set default path to the gallery
	$flag_options['swfUpload']				= true;								// activate the batch upload
	$flag_options['deleteImg']				= true;								// delete Images
	$flag_options['useMediaRSS']			= false;							// activate the global Media RSS file
	
	// Sort Settings
	$flag_options['galSort']				= 'sortorder';						// Sort order
	$flag_options['galSortDir']				= 'ASC';							// Sort direction

	// Flash settings
	$flag_options['skinsDirABS']			= str_replace("\\","/", WP_PLUGIN_DIR . '/flagallery-skins/' );
	$flag_options['skinsDirURL']			= WP_PLUGIN_URL . '/flagallery-skins/';
	$flag_options['flashSkin']				= 'default'; 
	$flag_options['flashWidth']				= '100%'; 
	$flag_options['flashHeight']			= '500';

	// Image Settings
	$flag_options['imgWidth']				= 800;  							// Image Width
	$flag_options['imgHeight']				= 600;  							// Image height
	$flag_options['imgQuality']				= 85;								// Image Quality
	
	// Thumbnail Settings
	$flag_options['thumbWidth']				= 115;  							// Thumb Width
	$flag_options['thumbHeight']			= 100;  							// Thumb height
	$flag_options['thumbFix']				= true;								// Fix the dimension
	$flag_options['thumbQuality']			= 100;  							// Thumb Quality

	// Flash default skin colors settings
	$flag_options['flashBacktransparent'] 	= false;
	$flag_options['flashBackcolor']			= '262626';
	$flag_options['buttonsBG']				= '000000';
	$flag_options['buttonsMouseOver']		= '7485c2';
	$flag_options['buttonsMouseOut']		= '717171';
	$flag_options['catButtonsMouseOver']	= '000000';
	$flag_options['catButtonsMouseOut']		= '000000';
	$flag_options['catButtonsTextMouseOver']= '7485c2';
	$flag_options['catButtonsTextMouseOut']	= 'bcbcbc';
	$flag_options['thumbMouseOver']			= '7485c2';
	$flag_options['thumbMouseOut']			= '000000';
	$flag_options['mainTitle']				= 'ffffff';
	$flag_options['categoryTitle']			= '7485c2';		
	$flag_options['itemBG']					= 'eae6ef';		
	$flag_options['itemTitle']				= '7485c2';		
	$flag_options['itemDescription']		= 'e0e0e0';		

	// Alternative gallery colors
	$flag_options['jAlterGal']				= true;
	$flag_options['BarsBG']					= '292929';
	$flag_options['CatBGColor']				= '292929';
	$flag_options['CatBGColorOver']			= '737373';
	$flag_options['CatColor']				= 'ffffff';
	$flag_options['CatColorOver']			= 'ffffff';
	$flag_options['ThumbBG']				= 'ffffff';
	$flag_options['ThumbLoaderColor']		= '4a4a4a';
	$flag_options['TitleColor']				= 'ff9900';
	$flag_options['DescrColor']				= 'cfcfcf';

	// Single player colors
	$flag_options['videoBG']				= '000000';
	$flag_options['vmColor1']				= 'ffffff';
	$flag_options['vmColor2']				= '3283A7';
	$flag_options['vmAutoplay']				= 'true';
	$flag_options['vmWidth']				= '520';
	$flag_options['vmHeight']				= '304';

	$flag_options['mpBG']					= '4f4f4f';
	$flag_options['mpColor1']				= 'ffffff';
	$flag_options['mpColor2']				= '3283A7';

	$flag_options['advanced']				= false;  							// Advanced options
	
	return $flag_options;
}

/**
 * Deregister a capability from all classic roles
 * 
 * @access internal
 * @param string $capability name of the capability which should be deregister
 * @return void
 */
function flag_remove_capability($capability){
	// this function remove the $capability only from the classic roles
	$check_order = array("subscriber", "contributor", "author", "editor", "administrator");

	foreach ($check_order as $role) {

		$role = get_role($role);
		$role->remove_cap($capability) ;
	}

}

/**
 * Uninstall all settings and tables
 * Called via Setup and register_unstall hook
 * 
 * @access internal
 * @return void
 */
function flag_uninstall() {
	global $wpdb;
	
	// first remove all tables
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}flag_pictures");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}flag_gallery");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}flag_comments");
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}flag_album");
	
	// then remove all options
	delete_option( 'flag_options' );
	delete_option( 'flag_db_version' );
	delete_option( 'flagVersion' );

	// now remove the capability
	flag_remove_capability("FlAG overview");
	flag_remove_capability("FlAG Use TinyMCE");
	flag_remove_capability("FlAG Upload images");
	flag_remove_capability("FlAG Import folder");
	flag_remove_capability("FlAG Manage gallery");
	flag_remove_capability('FlAG Manage others gallery');
	flag_remove_capability("FlAG Change skin");
	flag_remove_capability('FlAG Add skins');
	flag_remove_capability('FlAG Delete skins');
	flag_remove_capability("FlAG Change options");
	flag_remove_capability("FlAG Manage music");
	flag_remove_capability("FlAG Manage video");
	flag_remove_capability("FlAG Manage banners");
	flag_remove_capability("FlAG Facebook page");
}



?>
