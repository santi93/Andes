<?php
/*
Plugin Name: GRAND Flash Album Gallery
Plugin URI: http://codeasily.com/wordpress-plugins/flash-album-gallery/flag/
Description: The GRAND FlAGallery plugin - provides a comprehensive interface for managing photos and images through a set of admin pages, and it displays photos in a way that makes your web site look very professional.
Version: 1.48
Author: Sergey Pasyuk
Author URI: http://codeasily.com/

-------------------

		Copyright 2009  Sergey Pasyuk  (email : pasyuk@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

//ini_set('display_errors', '1');
//ini_set('error_reporting', E_ALL);


if (!class_exists('flagLoad')) {
class flagLoad {
	
	var $version     = '1.48';
	var $dbversion   = '1.24';
	var $minium_WP   = '3.0';
	var $minium_WPMU = '2.8';
	var $options     = '';
	var $manage_page;
	var $add_PHP5_notice = false;
	
	function flagLoad() {

		// Load the language file
		$this->load_textdomain();
		
		// Stop the plugin if we missed the requirements
		if ( ( !$this->required_version() ) || ( !$this->check_memory_limit() ) )
			return;
			
		// Get some constants first
		$this->load_options();
		$this->define_constant();
		$this->define_tables();
		$this->load_dependencies();
		
		$this->plugin_name = plugin_basename(__FILE__);

		// Init options & tables during activation & deregister init option
		register_activation_hook( $this->plugin_name, array(&$this, 'activate') );
		add_action( 'init', array(&$this, 'wp_flag_tune_messages') );
		register_deactivation_hook( $this->plugin_name, array(&$this, 'deactivate') );	

		// Register a uninstall hook to remove all tables & option automatic
		register_uninstall_hook( $this->plugin_name, array('flagLoader', 'uninstall') );

		// Start this plugin once all other plugins are fully loaded
		add_action( 'plugins_loaded', array(&$this, 'start_plugin') );
		
		// Add a message for PHP4 Users, can disable the update message later on
		if (version_compare(PHP_VERSION, '5.0.0', '<'))
			add_filter('transient_update_plugins', array(&$this, 'disable_upgrade'));
		
		//Add some message on the plugin page
		add_action( 'after_plugin_row', array(&$this, 'flag_check_message_version') );

	}
	
	function start_plugin() {

		// Content Filters
		add_filter('flag_gallery_name', 'sanitize_title');

		// Load the admin panel or the frontend functions
		if ( is_admin() ) {	
			
			// Pass the init check or show a message
			if (get_option( "flag_init_check" ) != false )
				add_action( 'admin_notices', create_function('', 'echo \'<div id="message" class="error"><p><strong>' . get_option( "flag_init_check" ) . '</strong></p></div>\';') );
				
		} else {			
			
			// Add MRSS to wp_head
			if ( $this->options['useMediaRSS'] )
				add_action('wp_head', array('flagMediaRss', 'add_mrss_alternate_link'));
			
			// Add the script and style files
			add_action('wp_print_scripts', array(&$this, 'load_scripts') );

			// Add a version number to the header
			add_action('wp_head', create_function('', 'echo "\n<meta name=\'GRAND FlAGallery\' content=\'' . $this->version . '\' />\n";') );

		}	
	}

	function wp_flag_tune_messages() {
		if($this->options['flagVersion'] != $this->version) {
			// upgrade plugin
			require_once(FLAG_ABSPATH . 'admin/tuning.php');
			$ok = flag_tune($show_error=false);

			include_once (dirname (__FILE__) . '/admin/flag_install.php');
			// check for tables
			flag_capabilities();
		}
	}

	function required_version() {
		
		global $wp_version, $wpmu_version;
		
		// Check for WPMU installation
		if (!defined ('IS_WPMU'))
			define('IS_WPMU', version_compare($wpmu_version, $this->minium_WPMU, '>=') );
		
 		// Check for WP version installation
		$wp_ok  =  version_compare($wp_version, $this->minium_WP, '>=');
		
		if ( ($wp_ok == FALSE) and (IS_WPMU == FALSE) ) {
			add_action('admin_notices', create_function('', 'global $flag; printf (\'<div id="message" class="error"><p><strong>\' . __(\'Sorry,GRAND Flash Album Gallery works only under WordPress %s or higher\', "flag" ) . \'</strong></p></div>\', $flag->minium_WP );'));
			return false;
		}
		return true;
		
	}
	
	function check_memory_limit() {
		
		$memory_limit = (int) substr( ini_get('memory_limit'), 0, -1);
		//This works only with enough memory, 8MB is silly, wordpress requires already 7.9999
		if ( ($memory_limit != 0) && ($memory_limit < 12 ) ) {
			add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error"><p><strong>' . __('Sorry, GRAND Flash Album Gallery works only with a Memory Limit of 16 MB higher', 'flag') . '</strong></p></div>\';'));
			return false;
		}
		
		return true;
		
	}
	
	function define_tables() {		
		global $wpdb;
		
		// add database pointer 
		$wpdb->flagpictures					= $wpdb->prefix . 'flag_pictures';
		$wpdb->flaggallery					= $wpdb->prefix . 'flag_gallery';
		$wpdb->flagcomments					= $wpdb->prefix . 'flag_comments';
		$wpdb->flagalbum					= $wpdb->prefix . 'flag_album';
		
	}
	
	function define_constant() {
		
		define('FLAGVERSION', $this->version);
		// Minimum required database version
		define('FLAG_DBVERSION', $this->dbversion);

		// required for Windows & XAMPP
		define('WINABSPATH', str_replace("\\", "/", ABSPATH) );
			
		// define URL
		define('FLAGFOLDER', plugin_basename( dirname(__FILE__)) );
		
		define('FLAG_ABSPATH', str_replace("\\","/", WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) . '/' ));
		define('FLAG_URLPATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
		
		// get value for safe mode
		if ( (gettype( ini_get('safe_mode') ) == 'string') ) {
			// if sever did in in a other way
			if ( ini_get('safe_mode') == 'off' ) define('SAFE_MODE', FALSE);
			else define( 'SAFE_MODE', ini_get('safe_mode') );
		} else
		define( 'SAFE_MODE', ini_get('safe_mode') );
		
	}
	
	function load_dependencies() {
		global $flagdb;
	
		// Load global libraries												
		require_once (dirname (__FILE__) . '/lib/core.php'); 
		require_once (dirname (__FILE__) . '/lib/flag-db.php');
		require_once (dirname (__FILE__) . '/lib/image.php');
		require_once (dirname (__FILE__) . '/widgets/widgets.php');

		// We didn't need all stuff during a AJAX operation
		if ( defined('DOING_AJAX') )
			require_once (dirname (__FILE__) . '/admin/ajax.php');
		else {
			require_once (dirname (__FILE__) . '/lib/meta.php');
			require_once (dirname (__FILE__) . '/lib/media-rss.php');
			include_once (dirname (__FILE__) . '/admin/tinymce/tinymce.php');

			// Load backend libraries
			if ( is_admin() ) {	
				require_once (dirname (__FILE__) . '/admin/admin.php');
				require_once (dirname (__FILE__) . '/admin/media-upload.php');
				$this->flagAdminPanel = new flagAdminPanel();
				
			// Load frontend libraries							
			} else {
				require_once (dirname (__FILE__) . '/lib/swfobject.php');
				require_once (dirname (__FILE__) . '/lib/shortcodes.php');
			}	
		}
	}
	
	function load_textdomain() {
		
		load_plugin_textdomain('flag', false, dirname( plugin_basename(__FILE__) ) . '/lang');

	}
	
	function load_scripts() {

		wp_enqueue_script('jquery');
		// Let's override WP's bundled swfobject, cause as of WP 2.9, it's still using 2.1 
		wp_deregister_script('swfobject');
		// and register our own.
		wp_register_script('swfobject', plugins_url('/admin/js/swfobject.js', __FILE__), array(), '2.2');
		wp_enqueue_script('swfobject');
		wp_register_script('swfaddress', plugins_url('/admin/js/swfaddress.js', __FILE__), array(), '2.4');
		wp_enqueue_script('swfaddress');

	}
	
	function load_options() {
		// Load the options
		$this->options = get_option('flag_options');
	}

	function activate() {
		//Since version 0.40 it's tested only with PHP5.2, currently we keep PHP4 support a while
        //if (version_compare(PHP_VERSION, '5.2.0', '<')) { 
        //        deactivate_plugins(plugin_basename(__FILE__)); // Deactivate ourself
        //        wp_die("Sorry, but you can't run this plugin, it requires PHP 5.2 or higher."); 
		//		return; 
        //} 
		include_once (dirname (__FILE__) . '/admin/flag_install.php');
		// check for tables
		flag_install();
	}
	
	function deactivate() {
		// remove & reset the init check option
		delete_option( 'flag_init_check' );
	}

	function uninstall() {
	  	include_once (dirname (__FILE__) . '/admin/flag_install.php');
	    flag_uninstall();
	}

	function disable_upgrade($option){

	 	$this_plugin = plugin_basename(__FILE__);
	 	
		// PHP5.2 is required for FlAG V0.40 
		if ( version_compare($option->response[ $this_plugin ]->new_version, '0.40', '>=') )
			return $option;

	    if( isset($option->response[ $this_plugin ]) ){
	        //TODO:Clear its download link, not now but maybe later
	        //$option->response[ $this_plugin ]->package = '';
	        
	        //Add a notice message
	        if ($this->add_PHP5_notice == false){
   	    		add_action( "in_plugin_update_message-$this->plugin_name", create_function('', 'echo \'<br /><span style="color:red">Please update to PHP5.2 as soon as possible, the plugin is not tested under PHP4 anymore</span>\';') );
	    		$this->add_PHP5_notice = true;
			}
		}
	    return $option;
	}
	
	// PLUGIN MESSAGE ON PLUGINS PAGE
	function flag_check_message_version($file)
	{
		static $this_plugin;
		global $wp_version;
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

		if ($file == $this_plugin ){
			$checkfile = "http://codeasily.com/flagallery.chk";

			$message = wp_remote_fopen($checkfile);

			if($message)
			{
				preg_match( '|flag040:(.*)$|mi', $message, $theMessage );
				
				$columns = substr($wp_version, 0, 3) == "2.8" ? 3 : 5;

				if ( !empty( $theMessage ) )
				{
					$theMessage = trim($theMessage[1]);
					echo '<td colspan="'.$columns.'" class="plugin-update" style="line-height:1.2em; font-size:11px; padding:1px;"><div id="flag-update-msg" style="padding-bottom:1px;" >'.$theMessage.'</div></td>';
				} else {
					return;
				}
			}
		}
	}

}
	// Let's start the holy plugin
	global $flag;
	$flag = new flagLoad();

}
?>
