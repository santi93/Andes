<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
// look up for the path
require_once( dirname(dirname(__FILE__)) . '/flag-config.php');

// check for correct capability
if ( !is_user_logged_in() )
	die('-1');

// check for correct FlAG capability
if ( !current_user_can('FlAG Change skin') )
	die('-1');

require_once (dirname (__FILE__) . '/get_skin.php');

if( isset($_POST['installskin']) ) {
	require_once (dirname (__FILE__) . '/skin_install.php');
}
add_action('install_skins_upload', 'upload_skin');
function upload_skin() {

	echo '<div id="uploadaction">';
	echo '<h3>'.__('Install info', 'flag').'</h3>';

	if ( ! ( ( $uploads = wp_upload_dir() ) && false === $uploads['error'] ) ) {
		echo "<p>".$uploads['error']."</p>\n";
	} else {
		if ( !empty($_FILES) ) {
			$filename = $_FILES['skinzip']['name'];
		}	else if ( isset($_GET['package']) ) {
			$filename = $_GET['package'];
		}
		if ( !$filename ) {
			echo "<p>".__('No skin Specified', 'flag')."</p>\n";
		} else {
			check_admin_referer('skin-upload');
			echo '<h4>', sprintf( __('Installing Skin from file: %s', 'flag'), basename($filename) ), '</h4>';

			//Handle a newly uploaded file, Else assume it was
			if ( !empty($_FILES) ) {
				$filename = wp_unique_filename( $uploads['basedir'], $filename );
				$local_file = $uploads['basedir'] . '/' . $filename;

				// Move the file to the uploads dir
				if ( false === @move_uploaded_file( $_FILES['skinzip']['tmp_name'], $local_file) )
					echo "<p>".sprintf( __('The uploaded file could not be moved to %s.', 'flag'), $uploads['path'])."</p>\n";
			} else {
				$local_file = $uploads['basedir'] . '/' . $filename;
			}
			if( $installed_skin = do_skin_install_local_package($local_file, $filename) ) {
				if ( file_exists($installed_skin.basename($installed_skin).'.png') ) {
					@rename($installed_skin.basename($installed_skin).'.png', $installed_skin.'screenshot.png');
				}
				if( !file_exists( $installed_skin.'settings.php' ) ) {
					if( file_exists( $installed_skin.'xml.php' ) ) {
						if ( !@copy(dirname($installed_skin).'/default/old_colors.php', $installed_skin.'colors.php') ) {
							echo "<p>".sprintf(__('Failed to copy and rename %1$s to %2$s','flag'),
								dirname($installed_skin).'/default/old_colors.php', $installed_skin.'colors.php').'</p>';
						}
						$content = file_get_contents($installed_skin.'xml.php');
						$pos = strpos($content,'/../../flash-album-gallery/flag-config.php');
						if($pos === false) {
							$content = str_replace('/../../flag-config.php','/../../flash-album-gallery/flag-config.php',$content);
							$fp = fopen($installed_skin.'xml.php','w');
							if( fwrite($fp,$content) === FALSE ) {
								echo "<p>".sprintf(__("Failed to search string '/../../flag-config.php' and replace with '/../../flash-album-gallery/flag-config.php' in file '%1$s'",'flag'),
									$installed_skin.'xml.php').'</p>';
							}
							fclose($fp);
						}
					}
				}
			}
		}
	}
	echo '</div>';
}

/**
 * Get skin options
 *
 */
function flag_skin_options_tab() {
	//Get the active skin
	$flag_options = get_option('flag_options');
	$active_skin_settings = $flag_options['skinsDirABS'].$flag_options['flashSkin'].'/settings/settings.xml';
	if(!file_exists($active_skin_settings)) {
		$active_skin = $flag_options['skinsDirABS'].$flag_options['flashSkin'].'/'.$flag_options['flashSkin'].'.php';
		include_once($active_skin);
	} else {
		include_once(dirname(__FILE__).'/skin_options.php');
	}
	if(function_exists('flag_skin_options')) {
		flag_skin_options();
	} else {
		include_once(FLAG_ABSPATH.'admin/db_skin_color_scheme.php');
		flag_skin_options();
	}
}


if ( isset($_POST['updateskinoption']) ) {
	check_admin_referer('skin_settings');
	// get the hidden option fields, taken from WP core
	if ( $_POST['skin_options'] )
		$options = explode(',', stripslashes($_POST['skin_options']));
	elseif ( $_POST['skinoptions'] )
		$options = explode(',', stripslashes($_POST['skinoptions']));
	if ($options) {
		$settings_content = '<?php '."\n";
		foreach ($options as $option) {
			$option = trim($option);
			$value = trim($_POST[$option]);
			$flag->options[$option] = $value;
			$settings_content .= '$'.$option.' = \''.str_replace('#','',$value)."';\n";
		}
		$settings_content .= '?>'."\n";
		// the path should always end with a slash
		$flag->options['galleryPath']    = trailingslashit($flag->options['galleryPath']);
	}
	// Save options
	$flag_options = get_option('flag_options');
	update_option('flag_options', $flag->options);
	if( flagGallery::saveFile($flag_options['skinsDirABS'].$flag_options['flashSkin'].'_settings.php',$settings_content,'w') ){
		flagGallery::show_message(__('Update Successfully','flag'));
	}
}

if ( isset($_POST['updateoption']) ) {
	check_admin_referer('flag_settings');
	// get the hidden option fields, taken from WP core
	if ( $_POST['page_options'] )
		$options = explode(',', stripslashes($_POST['page_options']));
	if ($options) {
		foreach ($options as $option) {
			$option = trim($option);
			$value = trim($_POST[$option]);
			$flag->options[$option] = $value;
		}
		// the path should always end with a slash
		$flag->options['galleryPath']    = trailingslashit($flag->options['galleryPath']);
	}
	// Save options
	update_option('flag_options', $flag->options);
 	flagGallery::show_message(__('Update Successfully','flag'));
}


if ( isset($_GET['delete']) ) {
	$delskin = $_GET['delete'];
	if ( current_user_can('FlAG Delete skins') ) {
		$flag_options = get_option('flag_options');
		if ( $flag_options['flashSkin'] != $delskin ) {
			$skins_dir = trailingslashit( $flag_options['skinsDirABS'] );
			$skin = $skins_dir.$delskin.'/';
			if ( is_dir($skin) ) {
				if( flagGallery::flagFolderDelete($skin) ) {
					flagGallery::show_message( __('Skin','flag').' \''.$delskin.'\' '.__('deleted successfully','flag') );
				} else {
					flagGallery::show_message( __('Can\'t find skin directory ','flag').' \''.$delskin.'\' '.__('. Try delete it manualy via ftp','flag') );
				}
			}
		} else {
			flagGallery::show_message( __('You need activate another skin before delete it','flag') );
		}
	} else {
		wp_die(__('You do not have sufficient permissions to delete skins of GRAND FlAGallery.'));
	}
}

if( isset($_GET['skin']) ) {
	$set_skin = $_GET['skin'];
	$flag_options = get_option('flag_options');
	if($flag_options['flashSkin'] != $set_skin) {
		$flag_options['flashSkin'] = $set_skin;
		$active_skin = $flag_options['skinsDirABS'].$set_skin.'/'.$set_skin.'.php';
		include_once($active_skin);
		update_option('flag_options', $flag_options);
		flagGallery::show_message( __('Skin','flag').' \''.$set_skin.'\' '.__('activated successfully','flag') );
	}
}
$type = isset($_GET['type'])? $_GET['type'] : '';

if( isset($_GET['skins_refresh']) ) {
	// upgrade plugin
	require_once(FLAG_ABSPATH . 'admin/tuning.php');
	$ok = flag_tune();
	if($ok)
		flagGallery::show_message( __('Skins refreshed successfully','flag') );
}
?>
<div id="slider" class="wrap">
	<ul id="tabs" class="tabs">
		<li class="selected"><a href="#" rel="addskin"><?php _e('Add new skin', 'flag'); ?></a></li>
		<li><a href="#" rel="skinoptions"><?php _e('Active Skin Options', 'flag'); ?></a></li>
	</ul>

	<div id="addskin" class="cptab">
		<h2><?php _e('Add new skin', 'flag'); ?></h2>
<?php if( current_user_can('FlAG Add skins') ) { ?>
		<h4><?php _e('Install a skin in .zip format', 'flag'); ?></h4>
		<p><?php _e('If you have a skin in a .zip format, You may install it by uploading it here.', 'flag'); ?></p>
		<form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin.php?page=flag-skins'); ?>">
			<?php wp_nonce_field( 'skin-upload'); ?>
			<p><input type="file" name="skinzip" />
			<input type="submit" class="button" name="installskin" value="<?php _e('Install Now', 'flag'); ?>" /></p>
		</form>
		<?php if( isset($_POST['installskin']) ) {
			do_action('install_skins_upload');
		} ?>
<?php } else { ?>
		<p><?php _e('You do not have sufficient permissions to install skin through admin page. You can install it by uploading via ftp to /flagallery-skins/ folder.', 'flag'); ?></p>
<?php } ?>
	</div>

	<div id="skinoptions" class="cptab">
		<h2><?php _e('Active Skin Options', 'flag'); ?></h2>
		<?php flag_skin_options_tab(); ?>
	</div>
	<script type="text/javascript">
		/* <![CDATA[ */
		var cptabs=new ddtabcontent("tabs");
		cptabs.setpersist(false);
		cptabs.setselectedClassTarget("linkparent");
		cptabs.init();
		/* ]]> */
	</script>
</div>

<div class="wrap">
<h2><?php _e('Skins', 'flag'); ?>:</h2>
<p style="float: right"><a class="button" href="<?php echo admin_url('admin.php?page=flag-skins&amp;skins_refresh=1'); ?>"><?php _e('Refresh / Update Skins', 'flag'); ?></a></p>
<p><a class="button<?php if(!$type) echo '-primary'; ?>" href="<?php echo admin_url('admin.php?page=flag-skins'); ?>"><span style="font-size: 14px;"><?php _e('image gallery skins', 'flag'); ?></span></a>&nbsp;&nbsp;&nbsp;
<a class="button<?php if($type == 'm') echo '-primary'; ?>" href="<?php echo admin_url('admin.php?page=flag-skins&amp;type=m'); ?>"><span style="font-size: 14px;"><?php _e('music gallery skins', 'flag'); ?></span></a>&nbsp;&nbsp;&nbsp;
<a class="button<?php if($type == 'v') echo '-primary'; ?>" href="<?php echo admin_url('admin.php?page=flag-skins&amp;type=v'); ?>"><span style="font-size: 14px;"><?php _e('video gallery skins', 'flag'); ?></span></a>&nbsp;&nbsp;&nbsp;
<a class="button<?php if($type == 'b') echo '-primary'; ?>" href="<?php echo admin_url('admin.php?page=flag-skins&amp;type=b'); ?>"><span style="font-size: 14px;"><?php _e('banner skins', 'flag'); ?></span></a>&nbsp;&nbsp;&nbsp;
<a class="button<?php if($type == 'w') echo '-primary'; ?>" href="<?php echo admin_url('admin.php?page=flag-skins&amp;type=w'); ?>"><span style="font-size: 14px;"><?php _e('widget skins', 'flag'); ?></span></a>
</p>

<?php
$all_skins = get_skins(false,$type);
$total_all_skins = count($all_skins);
$flag_options = get_option ('flag_options');
?>
<table class="widefat" cellspacing="0" id="skins-table">
	<thead>
	<tr>
		<th scope="col" class="manage-column"><?php _e('Skin', 'flag'); ?></th>
		<th scope="col" class="manage-column"><?php _e('Description', 'flag'); ?></th>
		<th scope="col" class="action-links"><?php _e('Action', 'flag'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<th scope="col" class="manage-column"><?php _e('Skin', 'flag'); ?></th>
		<th scope="col" class="manage-column"><?php _e('Description', 'flag'); ?></th>
		<th scope="col" class="action-links"><?php _e('Action', 'flag'); ?></th>
	</tr>
	</tfoot>

	<tbody class="skins">
<?php

	if ( empty($all_skins) ) {
		echo '<tr>
			<td colspan="3">' . __('No skins to show') . '</td>
		</tr>';
	}
	foreach ( (array)$all_skins as $skin_file => $skin_data) {
		$class = ( dirname($skin_file) == $flag_options['flashSkin'] ) ? 'active' : 'inactive';
		echo "
	<tr id='".basename($skin_file, '.php')."' class='$class first'>
		<td class='skin-title'><strong>{$skin_data['Name']}</strong></td>
		<td class='desc'>";
		$skin_meta = array();
		if ( !empty($skin_data['Version']) )
			$skin_meta[] = sprintf(__('Version %s', 'flag'), $skin_data['Version']);
		if ( !empty($skin_data['Author']) ) {
			$author = $skin_data['Author'];
			if ( !empty($skin_data['AuthorURI']) )
				$author = '<a href="' . $skin_data['AuthorURI'] . '" title="' . __( 'Visit author homepage', 'flag' ) . '">' . $skin_data['Author'] . '</a>';
			$skin_meta[] = sprintf( __('By %s', 'flag'), $author );
		}
		if ( ! empty($skin_data['SkinURI']) )
			$skin_meta[] = '<a href="' . $skin_data['SkinURI'] . '" title="' . __( 'Visit skin site', 'flag' ) . '">' . __('Visit skin site', 'flag' ) . '</a>';

		echo implode(' | ', $skin_meta);
		echo "</td>";
		echo "<td class='skin-activate action-links'>";
		if(isset($_GET['type'])) {
		} else {
			if ( dirname($skin_file) != $flag_options['flashSkin'] ) {
				echo '<strong><a href="'.admin_url('admin.php?page=flag-skins&skin='.dirname($skin_file)).'" title="' . __( 'Activate this skin', 'flag' ) . '">' . __('Activate', 'flag' ) . '</a></strong>';
	 		} else {
	 			echo "<strong>".__('Activated by default', 'flag' )."</strong>";
	 		}
		}
		echo "</td>";

	echo "</tr>
	<tr class='$class second'>
		<td class='skin-title'><img src='".WP_PLUGIN_URL."/flagallery-skins/".dirname($skin_file)."/screenshot.png' alt='{$skin_data['Name']}' title='{$skin_data['Name']}' /></td>
		<td class='desc'><p>{$skin_data['Description']}</p></td>";
 // delete link
		echo "<td class='skin-delete action-links'>";
		$settings = $flag_options['skinsDirABS'].dirname($skin_file).'/settings';
		if(is_dir($settings)) {
			echo '<a class="thickbox" href="'.FLAG_URLPATH.'admin/skin_options.php?show_options=1&amp;skin='.dirname($skin_file).'&amp;TB_iframe=1&amp;width=600&amp;height=560">' . __('Options', 'flag' ) . '</a>';
		}
		if ( current_user_can('FlAG Delete skins') ) {
		if ( dirname($skin_file) != $flag_options['flashSkin'] ) {
			echo '<br /><br /><a class="delete" onclick="javascript:check=confirm( \'' . attribute_escape(sprintf(__('Delete "%s"' , 'flag'), $skin_data['Name'])). '\');if(check==false) return false;" href="'.admin_url('admin.php?page=flag-skins&delete='.dirname($skin_file)).'" title="' . __( 'Delete this skin', 'flag' ) . '">' . __('Delete', 'flag' ) . '</a>';
		}
 		}
		echo "</td>";
	echo "</tr>\n";
	}
?>
	</tbody>
</table>
</div>
<?php ?>