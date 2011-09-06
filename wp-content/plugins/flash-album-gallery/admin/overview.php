<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/**
 * flag_admin_overview()
 *
 * Add the admin overview in wp2.7 style 
 * @return mixed content
 */
function flag_admin_overview()  {	
?>
<div class="wrap flag-wrap">
	<h2><?php _e('GRAND FlAGallery Overview', 'flag'); echo ' v'.FLAGVERSION; ?></h2>
	<div id="flag-overview" class="metabox-holder">
		<div id="side-info-column" class="inner-sidebar" style="display:block;">
				<?php do_meta_boxes('flag-overview', 'side', null); ?>
		</div>
		<div id="post-body" class="has-sidebar">
			<div id="post-body-content" class="has-sidebar-content">
					<?php do_meta_boxes('flag-overview', 'normal', null); ?>
			</div>
		</div>
	</div>
</div>

<?php
wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
?>
<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function() {
		jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	// postboxes
		postboxes.add_postbox_toggles('flag-overview');
		jQuery('#side-info-column #major-publishing-actions').appendTo('#dashboard_primary');
	});
	//]]>
</script>

<?php
}

/**
 * Show the server settings
 * 
 * @return void
 */
function flag_overview_server() {
?>
<div id="dashboard_server_settings" class="dashboard-widget-holder wp_dashboard_empty">
	<div class="flag-dashboard-widget">
	  <?php if (IS_WPMU) {
	  	if (flagGallery::flag_wpmu_enable_function('wpmuQuotaCheck'))
			echo flag_SpaceManager::details();
		else {
			//TODO:WPMU message in WP2.5 style
			echo flag_SpaceManager::details();
		}
	  } else { ?>
		<div class="dashboard-widget-content">
     	<ul class="settings">
     		<?php get_serverinfo(); ?>
	  	</ul>
		</div>
	  <?php } ?>
  </div>
</div>
<?php	
}

/**
 * Show the GD ibfos
 * 
 * @return void
 */
function flag_overview_graphic_lib() {
?>
<div id="dashboard_server_settings" class="dashboard-widget-holder">
	<div class="flag-dashboard-widget">
	  	<div class="dashboard-widget-content">
	  		<ul class="settings">
			<?php flag_GD_info(); ?>
			</ul>
		</div>
    </div>
</div>
<?php	
}

/**
 * Show the Setup Box and some info for Flash Album Gallery
 * 
 * @return void
 */
function flag_overview_setup(){ 
	global $wpdb, $flag;
			
	if (isset($_POST['resetdefault'])) {	
		check_admin_referer('flag_uninstall');
					
		include_once ( dirname (__FILE__). '/flag_install.php');
		include_once( dirname (__FILE__). '/tuning.php');
		
		flag_default_options();
		flag_tune();
		$flag->define_constant();
		$flag->load_options();
		
		flagGallery::show_message(__('Reset all settings to default parameter','flag'));
	}

	if (isset($_POST['uninstall'])) {	
		
		check_admin_referer('flag_uninstall');
		
		include_once ( dirname (__FILE__).  '/flag_install.php');

		flag_uninstall();
			 	
	 	flagGallery::show_message(__('Uninstall sucessful ! Now delete the plugin and enjoy your life ! Good luck !','flag'));
	}
?>
		<div class="submitbox" id="submitpost">
			<div id="minor-publishing">
				<div id="misc-publishing-actions">
					<div class="misc-pub-section">
						<span id="plugin-home" class="icon">
							<strong><a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/flag" style="text-decoration: none;"><?php _e('Plugin Home','flag'); ?></a></strong>
						</span>
					</div>
					<div class="misc-pub-section">
						<span id="plugin-comments" class="icon">
							<a href="http://codeasily.com/wordpress-plugins/flash-album-gallery/flag#comments" style="text-decoration: none;"><?php _e('Plugin Comments','flag'); ?></a>
						</span>
					</div>
					<div class="misc-pub-section">
						<span id="rate-plugin" class="icon">
							<a href="http://wordpress.org/extend/plugins/flash-album-gallery" style="text-decoration: none;"><?php _e('Rate Plugin','flag'); ?></a>
						</span>
					</div>
					<!-- <div class="misc-pub-section">
						<span id="my-plugins" class="icon">
							<a href="http://codeasily.com/category/wordpress-plugins" style="text-decoration: none;"><?php _e('My Plugins','flag'); ?></a>
						</span>
					</div> -->
					<div class="misc-pub-section curtime misc-pub-section-last">
						<span id="contact-me" class="icon">
							<a href="http://codeasily.com/about" style="text-decoration: none;"><?php _e('Contact Me','flag'); ?></a>
						</span>
					</div>
				</div>
			</div>
		</div>
	<?php if (!IS_WPMU || flag_wpmu_site_admin() ) : ?>
	<div id="major-publishing-actions">
	<form id="resetsettings" name="resetsettings" method="post">
		<?php wp_nonce_field('flag_uninstall'); ?>
			<div id="save-action" class="alignleft">
				<input class="button" id="save-post" type="submit" name="resetdefault" value="<?php _e('Reset settings', 'flag'); ?>" onclick="javascript:check=confirm('<?php _e('Reset all options to default settings ?\n\nChoose [Cancel] to Stop, [OK] to proceed.\n','flag'); ?>');if(check==false) return false;" />
			</div>
			<div id="preview-action" class="alignright">
				<input type="submit" name="uninstall" class="button delete" value="<?php _e('Uninstall plugin', 'flag'); ?>" onclick="javascript:check=confirm('<?php _e('You are about to Uninstall this plugin from WordPress.\nThis action is not reversible.\n\nChoose [Cancel] to Stop, [OK] to Uninstall.\n','flag'); ?>');if(check==false) return false;" />
			</div>
			<br class="clear" />
	</form>
	</div>
	<?php endif; ?>

<?php
}
/**
 * Show the News Box
 * 
 * @return void
 */
function flag_news_box(){ 
?>
<script type="text/javascript">
/*<![CDATA[*/
jQuery(document).ready(function(){
jQuery("#photogallerycreator").load("<?php echo FLAG_URLPATH; ?>admin/news.php #skins", {want2Read:'http://photogallerycreator.com/grand-flagallery/'},function(){
//Write your additional jQuery script below. Use as many functions as you like, for instance:
jQuery("#photogallerycreator a").attr('target','_blank');
jQuery("#photogallerycreator a font").replaceWith('Download Demo');
});
});
/*]]>*/
</script>
		<p><?php _e("What's new at PhotoGalleryCreator.com","flag"); ?></p>
		<div id="photogallerycreator" style="text-align:center; overflow:auto; max-height:627px;">
			<script type="text/javascript" src="<?php echo FLAG_URLPATH; ?>admin/js/swfobject.js"></script>
			<script type="text/javascript">
			/*<![CDATA[*/
			swfobject.embedSWF("<?php echo FLAG_URLPATH; ?>admin/js/loader.swf", "loader", "100", "100", "9.0.45", false );
			/*]]>*/
			</script>
			<div class="swfobject" id="loader" style="text-align:center"><p>Loading...</p></div>
		</div>
<?php
}

/**
 * Show a summary of the used images
 * 
 * @return void
 */
function flag_overview_right_now() {
	global $wpdb;
	$images    = intval( $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->flagpictures") );
	$galleries = intval( $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->flaggallery") );
?>

<div class="table table_content">
<p class="sub"><?php _e('At a Glance', 'flag'); ?></p>
	<table>
		<tbody>
			<tr class="first">
				<td class="first b"><a href="admin.php?page=flag-manage-gallery&tabs=1"><?php echo $images; ?></a></td>
				<td class="t"><?php echo __ngettext( 'Image', 'Images', $images, 'flag' ); ?></td>
				<td class="b"></td>
				<td class="last"></td>
			</tr>
			<tr>
				<td class="first b"><a href="admin.php?page=flag-manage-gallery&tabs=0"><?php echo $galleries; ?></a></td>
				<td class="t"><?php echo __ngettext( 'Gallery', 'Galleries', $galleries, 'flag' ); ?></td>
				<td class="b"></td>
				<td class="last"></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="versions">
    <p>
			<?php if(current_user_can('FlAG Upload images')): ?><a class="button rbutton" href="admin.php?page=flag-manage-gallery&tabs=1"><strong><?php _e('Upload pictures', 'flag'); ?></strong></a><?php endif; ?>
			<?php _e('Here you can control your images and galleries.', 'flag'); ?></p>
		<span><?php
			$userlevel = '<span class="b">' . (current_user_can('manage_options') ? __('Gallery Administrator', 'flag') : __('Gallery Editor', 'flag')) . '</span>';
        printf(__('You currently have %s rights.', 'flag'), $userlevel);
    ?></span>
</div>
<?php
}

add_meta_box('dashboard_primary', __('Setup Box', 'flag'), 'flag_overview_setup', 'flag-overview', 'side', 'core');
add_meta_box('dashboard_news', __('News Box', 'flag'), 'flag_news_box', 'flag-overview', 'side', 'core');
add_meta_box('dashboard_right_now', __('Welcome to FlAG Gallery !', 'flag'), 'flag_overview_right_now', 'flag-overview', 'normal', 'core');
add_meta_box('flag_server', __('Server Settings', 'flag'), 'flag_overview_server', 'flag-overview', 'normal', 'core');
add_meta_box('flag_gd_lib', __('Graphic Library', 'flag'), 'flag_overview_graphic_lib', 'flag-overview', 'normal', 'core');

/**
 * Show GD Library version information
 * 
 * @return void
 */
function flag_GD_info() {
	
	if(function_exists("gd_info")){
		$info = gd_info();
		$keys = array_keys($info);
		for($i=0; $i<count($keys); $i++) {
			if(is_bool($info[$keys[$i]]))
				echo "<li> " . $keys[$i] ." : <span>" . flag_GD_Support($info[$keys[$i]]) . "</span></li>\n";
			else
				echo "<li> " . $keys[$i] ." : <span>" . $info[$keys[$i]] . "</span></li>\n";
		}
	}
	else {
		echo '<h4>'.__('No GD support', 'flag').'!</h4>';
	}
}

/**
 * Return localized Yes or no 
 * 
 * @param bool $bool
 * @return return 'Yes' | 'No'
 */
function flag_GD_Support($bool){
	if($bool) 
		return __('Yes', 'flag');
	else 
		return __('No', 'flag');
}

/**
 * Show up some server infor's
 * @author GamerZ (http://www.lesterchan.net)
 * 
 * @return void
 */
function get_serverinfo() {
	global $wpdb;
	// Get MYSQL Version
	$sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
	// GET SQL Mode
	$mysqlinfo = $wpdb->get_results("SHOW VARIABLES LIKE 'sql_mode'");
	if (is_array($mysqlinfo)) $sql_mode = $mysqlinfo[0]->Value;
	if (empty($sql_mode)) $sql_mode = __('Not set', 'flag');
	// Get PHP Safe Mode
	if(ini_get('safe_mode')) $safe_mode = __('On', 'flag');
	else $safe_mode = __('Off', 'flag');
	// Get PHP allow_url_fopen
	if(ini_get('allow_url_fopen')) $allow_url_fopen = __('On', 'flag');
	else $allow_url_fopen = __('Off', 'flag'); 
	// Get PHP Max Upload Size
	if(ini_get('upload_max_filesize')) $upload_max = ini_get('upload_max_filesize');	
	else $upload_max = __('N/A', 'flag');
	// Get PHP Output buffer Size
	if(ini_get('output_buffering')) $output_buffer = ini_get('output_buffering');	
	else $output_buffer = __('N/A', 'flag');
	// Get PHP Max Post Size
	if(ini_get('post_max_size')) $post_max = ini_get('post_max_size');
	else $post_max = __('N/A', 'flag');
	// Get PHP Max execution time
	if(ini_get('max_execution_time')) $max_execute = ini_get('max_execution_time');
	else $max_execute = __('N/A', 'flag');
	// Get PHP Memory Limit 
	if(ini_get('memory_limit')) $memory_limit = ini_get('memory_limit');
	else $memory_limit = __('N/A', 'flag');
	// Get actual memory_get_usage
	if (function_exists('memory_get_usage')) $memory_usage = round(memory_get_usage() / 1024 / 1024, 2) . __(' MByte', 'flag');
	else $memory_usage = __('N/A', 'flag');
	// required for EXIF read
	if (is_callable('exif_read_data')) $exif = __('Yes', 'flag'). " ( V" . substr(phpversion('exif'),0,4) . ")" ;
	else $exif = __('No', 'flag');
	// required for meta data
	if (is_callable('iptcparse')) $iptc = __('Yes', 'flag');
	else $iptc = __('No', 'flag');
	// required for meta data
	if (is_callable('xml_parser_create')) $xml = __('Yes', 'flag');
	else $xml = __('No', 'flag');
?>
	<li><?php _e('Operating System', 'flag'); ?> : <span><?php echo PHP_OS; ?>&nbsp;(<?php echo (PHP_INT_SIZE * 8); ?>&nbsp;Bit)</span></li>
	<li><?php _e('Server', 'flag'); ?> : <span><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></span></li>
	<li><?php _e('Memory usage', 'flag'); ?> : <span><?php echo $memory_usage; ?></span></li>
	<li><?php _e('MYSQL Version', 'flag'); ?> : <span><?php echo $sqlversion; ?></span></li>
	<li><?php _e('SQL Mode', 'flag'); ?> : <span><?php echo $sql_mode; ?></span></li>
	<li><?php _e('PHP Version', 'flag'); ?> : <span><?php echo PHP_VERSION; ?></span></li>
	<li><?php _e('PHP Safe Mode', 'flag'); ?> : <span><?php echo $safe_mode; ?></span></li>
	<li><?php _e('PHP Allow URL fopen', 'flag'); ?> : <span><?php echo $allow_url_fopen; ?></span></li>
	<li><?php _e('PHP Memory Limit', 'flag'); ?> : <span><?php echo $memory_limit; ?></span></li>
	<li><?php _e('PHP Max Upload Size', 'flag'); ?> : <span><?php echo $upload_max; ?></span></li>
	<li><?php _e('PHP Max Post Size', 'flag'); ?> : <span><?php echo $post_max; ?></span></li>
	<li><?php _e('PHP Output Buffer Size', 'flag'); ?> : <span><?php echo $output_buffer; ?></span></li>
	<li><?php _e('PHP Max Script Execute Time', 'flag'); ?> : <span><?php echo $max_execute; ?>s</span></li>
	<li><?php _e('PHP Exif support', 'flag'); ?> : <span><?php echo $exif; ?></span></li>
	<li><?php _e('PHP IPTC support', 'flag'); ?> : <span><?php echo $iptc; ?></span></li>
	<li><?php _e('PHP XML support', 'flag'); ?> : <span><?php echo $xml; ?></span></li>
<?php
}

/**
 * WPMU feature taken from Z-Space Upload Quotas
 * @author Dylan Reeve
 * @url http://dylan.wibble.net/
 *
 */
class flag_SpaceManager {
 
 	function getQuota() {
		if (function_exists(get_space_allowed))
			$quota = get_space_allowed();
		else
			$quota = get_site_option( "blog_upload_space" );
			
		return $quota;
	}
	 
	function details() {
		
		// take default seetings
		$settings = array(

			'remain'	=> array(
			'color_text'	=> 'white',
			'color_bar'		=> '#0D324F',
			'color_bg'		=> '#a0a0a0',
			'decimals'		=> 2,
			'unit'			=> 'm',
			'display'		=> true,
			'graph'			=> false
			),

			'used'		=> array(
			'color_text'	=> 'white',
			'color_bar'		=> '#0D324F',
			'color_bg'		=> '#a0a0a0',
			'decimals'		=> 2,
			'unit'			=> 'm',
			'display'		=> true,
			'graph'			=> true
			)
		);

		$quota = flag_SpaceManager::getQuota() * 1024 * 1024;
		$used = get_dirsize( constant( 'ABSPATH' ) . constant( 'UPLOADS' ) );
//		$used = get_dirsize( ABSPATH."wp-content/blogs.dir/".$blog_id."/files" );
		
		if ($used > $quota) $percentused = '100';
		else $percentused = ( $used / $quota ) * 100;

		$remaining = $quota - $used;
		$percentremain = 100 - $percentused;

		$out = '';
		$out .= '<div id="spaceused"> <h3>'.__('Storage Space','flag').'</h3>';

		if ($settings['used']['display']) {
			$out .= __('Upload Space Used:','flag') . "\n";
			$out .= flag_SpaceManager::buildGraph($settings['used'], $used,$quota,$percentused);
			$out .= "<br />";
		}

		if($settings['remain']['display']) {
			$out .= __('Upload Space Remaining:','flag') . "\n";
			$out .= flag_SpaceManager::buildGraph($settings['remain'], $remaining,$quota,$percentremain);

		}

		$out .= "</div>";

		echo $out;
	}

	function buildGraph($settings, $size, $quota, $percent) {
		$color_bar = $settings['color_bar'];
		$color_bg = $settings['color_bg'];
		$color_text = $settings['color_text'];
		
		switch ($settings['unit']) {
			case "b":
				$unit = "B";
				break;
				
			case "k":
				$unit = "KB";
				$size = $size / 1024;
				$quota = $quota / 1024;
				break;
				
			case "g":   // Gigabytes, really?
				$unit = "GB";
				$size = $size / 1024 / 1024 / 1024;
				$quota = $quota / 1024 / 1024 / 1024;
				break;
				
			default:
				$unit = "MB";
				$size = $size / 1024 / 1024;
				$quota = $quota / 1024 / 1024;
				break;
		}

		$size = round($size, (int)$settings['decimals']);

		$pct = round(($size / $quota)*100);

		if ($settings['graph']) {
			//TODO:move style to CSS
			$out = '<div style="display: block; margin: 0; padding: 0; height: 15px; border: 1px inset; width: 100%; background-color: '.$color_bg.';">'."\n";
			$out .= '<div style="display: block; height: 15px; border: none; background-color: '.$color_bar.'; width: '.$pct.'%;">'."\n";
			$out .= '<div style="display: inline; position: relative; top: 0; left: 0; font-size: 10px; color: '.$color_text.'; font-weight: bold; padding-bottom: 2px; padding-left: 5px;">'."\n";
			$out .= $size.$unit;
			$out .= "</div>\n</div>\n</div>\n";
		} else {
			$out = "<strong>".$size.$unit." ( ".number_format($percent)."%)"."</strong><br />";
		}

		return $out;
	}

}

/**
 * get_phpinfo() - Extract all of the data from phpinfo into a nested array
 * 
 * @author jon@sitewizard.ca
 * @return array
 */
function get_phpinfo() {

	ob_start();
	phpinfo();
	$phpinfo = array('phpinfo' => array());
	
	if ( preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER) )
	    foreach($matches as $match) {
	        if(strlen($match[1]))
	            $phpinfo[$match[1]] = array();
	        elseif(isset($match[3]))
	            $phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
	        else
	            $phpinfo[end(array_keys($phpinfo))][] = $match[2];
	    }
	    
	return $phpinfo;
}
?>