<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

// check for correct capability
if ( !is_user_logged_in() )
	die('-1');

// check for correct FlAG capability
if ( !current_user_can('FlAG Manage banners') ) 
	die('-1');	


require_once (dirname (__FILE__) . '/functions.php');
require_once (dirname (__FILE__) . '/banner.functions.php');

function flag_banner_controler() {
	$mode = isset($_REQUEST['mode'])? $_REQUEST['mode'] : 'main';
	if ($_POST['importfolder']){
		check_admin_referer('flag_addbanner');
		$bannerfolder = $_POST['bannerfolder'];
		if ( !empty($bannerfolder) ) {
			$crunch_list = flagAdmin::import_banner($bannerfolder);
			$mode = 'import';
		}
	}
	$action = isset($_REQUEST['bulkaction'])? $_REQUEST['bulkaction'] : false;
	if($action == 'no_action') {
		$action = false;
	}
	switch($mode) {
		case 'sort':
			include_once (dirname (__FILE__) . '/banner-sort.php');
			flag_b_playlist_order($_GET['playlist']);
		break;
		case 'edit':
			if(isset($_POST['updatePlaylist'])) {
				$title = $_POST['playlist_title'];
				$descr = $_POST['playlist_descr'];
				$file = $_GET['playlist'];
				foreach($_POST['item_a'] as $item_id => $item) {
					if($action=='delete_items' && in_array($item_id, $_POST['doaction']))
						continue;
					$data[] = $item_id;
				}
				flagGallery::flagSaveWpMedia();
				flagSave_bPlaylist($title,$descr,$data,$file);
			}
			if(isset($_POST['updatePlaylistSkin'])) {
				$file = $_GET['playlist'];
				flagSave_bPlaylistSkin($file);
			}
			include_once (dirname (__FILE__) . '/manage-banner.php');
			flag_b_playlist_edit($_GET['playlist']);
		break;
		case 'save':
			$title = $_POST['playlist_title'];
			$descr = $_POST['playlist_descr'];
			$data = $_POST['items_array'];
			$file = isset($_REQUEST['playlist'])? $_REQUEST['playlist'] : false;
			flagGallery::flagSaveWpMedia();
			flagSave_bPlaylist($title,$descr,$data, $file);
			if(isset($_GET['playlist'])) {
				include_once (dirname (__FILE__) . '/manage-banner.php');
				flag_b_playlist_edit($_GET['playlist']);
			} else {
				flag_created_b_playlists();
				flag_banner_wp_media_lib();
			}
		break;
	  	case 'add':
			$added = $_POST['items'];
			flag_banner_wp_media_lib($added);
		break;
		case 'delete':
			flag_b_playlist_delete($_GET['playlist']);
	  	case 'import':
			flag_crunch($crunch_list);
	  	case 'main':
			if(isset($_POST['updateMedia'])) {
				flagGallery::flagSaveWpMedia();
				flagGallery::show_message( __('Media updated','flag') );
			}
		default:
			flag_created_b_playlists();
			flag_banner_wp_media_lib();
		break;
	}

}
function flag_crunch($crunch_list) {
	$crunch_string = implode(',', $crunch_list); 
	$folder = rtrim($_POST['bannerfolder'], '/');
	$path = WINABSPATH . $folder.'/';
?>
<script type="text/javascript"> 
<!--
jQuery(document).ready(function(){
	var crunch_string = '<?php echo $crunch_string; ?>';
	var bannerfolder = '<?php echo $path; ?>';
	var crunch_list = crunch_string.split(',');
	var parts = crunch_list.length;
	function flag_crunch() {
		if(crunch_list.length) {
			jQuery.post( 
				ajaxurl, 
				{
					action: "flag_banner_crunch",
					_wpnonce: "<?php echo wp_create_nonce( 'flag-ajax' ); ?>",
					path: encodeURI(bannerfolder + crunch_list[0])
				},
				function( response ) {
					crunch_list.shift()
					var parts_done = parts - crunch_list.length;
					jQuery(".flag_crunching .flag_progress .flag_complete").animate({width:parts_done*(100/parts)+'%'}, 400);
					jQuery(".flag_crunching").append(response);
					flag_crunch();
				}
			);
		} else {
			var refpage = window.location.href;
			jQuery(".flag_crunching .txt").html('<a href="'+refpage+'"><?php _e("Import folder is complete. The page reloads after 5 seconds.", "flag"); ?></a>');
			//alert('<?php _e("Import folder complete. Refresh page.", "flag"); ?>');
			setTimeout(function(){ window.location.href=window.location.href }, 5000);
		}
	}
	flag_crunch();
});
//-->
</script>

<?php }

function flag_created_b_playlists() {

	$filepath = admin_url() . 'admin.php?page=' . $_GET['page'];

	$all_playlists = get_b_playlists();
	$total_all_playlists = count($all_playlists);
	$flag_options = get_option ('flag_options');

?>
	<div class="wrap">
		<h2><?php _e('Created playlists', 'flag'); ?></h2>
		<table class="widefat" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" width="25%"><?php _e('Title', 'flag'); ?></th>
				<th scope="col" width="55%"><?php _e('Description', 'flag'); ?></th>
				<th scope="col" ><?php _e('Quantity', 'flag'); ?></th>
				<th scope="col" ><?php _e('Shortcode', 'flag'); ?></th>
				<th scope="col" ><?php _e('Action', 'flag'); ?></th>
			</tr>
			</thead>
			<tbody>
<?php
if($all_playlists) {
	foreach((array)$all_playlists as $playlist_file => $playlist_data) {
		$query_m = get_posts(array('post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => null, 'post__in' => $playlist_data['items']));
		$class = ( !isset($class) || $class == 'class="alternate"' ) ? '' : 'class="alternate"';
		$playlist_name = basename($playlist_file, '.xml');
		if(count($query_m) != count($playlist_data['items'])) {
			flagSave_bPlaylist($playlist_data['title'],$playlist_data['description'],$playlist_data['items'],$playlist_name);
		}
?>
		<tr id="<?php echo $playlist_name; ?>" <?php echo $class; ?> >
			<td>
				<a href="<?php echo $filepath.'&amp;playlist='.$playlist_name.'&amp;mode=edit'; ?>" class='edit' title="<?php _e('Edit'); ?>" >
					<?php echo $playlist_data['title']; ?>
				</a>
			</td>
			<td><?php echo $playlist_data['description']; echo '&nbsp;('.__("player", "flag").': <strong>'.$playlist_data['skin'].'</strong>)' ?></td>
			<td><?php echo count($query_m); ?></td>
			<td style="white-space: nowrap;"><input type="text" class="shortcode1" style="width: 200px; font-size: 9px;" readonly="readonly" onfocus="this.select()" value="[grandbanner xml=<?php echo $playlist_name; ?>]" /></td>
			<td>
				<a href="<?php echo $filepath.'&amp;playlist='.$playlist_name."&amp;mode=delete"; ?>" class="delete" onclick="javascript:check=confirm( '<?php _e("Delete this playlist?",'flag')?>');if(check==false) return false;"><?php _e('Delete','flag'); ?></a>
			</td>
		</tr>
		<?php
	}
} else {
	echo '<tr><td colspan="5" align="center"><strong>'.__('No playlists found','flag').'</strong></td></tr>';
}
?>			
			</tbody>
		</table>
	</div>

<?php } ?>

<?php // *** show media list
function flag_banner_wp_media_lib($added=false) {
	global $wpdb;
	// same as $_SERVER['REQUEST_URI'], but should work under IIS 6.0
	$filepath = admin_url() . 'admin.php?page=' . $_GET['page'];
	if($added!==false) {
		$filepath .= '&amp;playlist='.$_GET['playlist'].'&amp;mode=save';
		$flag_options = get_option('flag_options');
		$playlistPath = $flag_options['galleryPath'].'playlists/banner/'.$_GET['playlist'].'.xml';
		$playlist = get_b_playlist_data(ABSPATH.$playlistPath);
		$exclude = explode(',', $added);
	}
?>
<script type="text/javascript"> 
<!--
jQuery(document).ready(function(){
    jQuery('.cb :checkbox').click(function() {
		if(jQuery(this).is(':checked')){
			var cur = jQuery(this).val();
			var arr = jQuery('#items_array').val();
			if(arr) { var del = ','; } else { var del = ''; }
			jQuery('#items_array').val(arr+del+cur);
		} else {
			var cur = jQuery(this).val();
			var arr = jQuery('#items_array').val().split(',');
			arr = jQuery.grep(arr, function(a){ return a != cur; }).join(',');
			jQuery('#items_array').val(arr);
		};
 	});
    jQuery('.del_thumb').click(function(){
      var id = jQuery(this).attr('data-id');
      jQuery('#banthumb-'+id).attr('value', '');
      jQuery('#thumb-'+id).attr('src', jQuery('#thumb-'+id).parent().attr('href'));
      return false;
    })
});
function checkAll(form)	{
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].name == "doaction[]") {
				if(form.elements[i].checked == true)
					form.elements[i].checked = false;
				else
					form.elements[i].checked = true;
			}
		}
	}
	var arr = jQuery('.cb input:checked').map(function(){return jQuery(this).val();}).get().join(',');
	jQuery('#items_array').val(arr);
}
// this function check for a the number of selected images, sumbmit false when no one selected
function checkSelected() {
	if(!jQuery('.cb input:checked')) { 
		alert('<?php echo js_escape(__("No items selected", "flag")); ?>');
		return false; 
	} 
	actionId = jQuery('#bulkaction').val();
	switch (actionId) {
		case "new_playlist":
			showDialog('new_playlist', 160);
			return false;
			break;
		case "add_to_playlist":
			return confirm('<?php echo sprintf(js_escape(__("You are about to add %s items to playlist \n \n 'Cancel' to stop, 'OK' to proceed.",'flag')), "' + numchecked + '") ; ?>');
			break;
	}
	return confirm('<?php echo sprintf(js_escape(__("You are about to start the bulk edit for %s items \n \n 'Cancel' to stop, 'OK' to proceed.",'flag')), "' + numchecked + '") ; ?>');
}

function showDialog( windowId, height ) {
	jQuery("#" + windowId + "_bulkaction").val(jQuery("#bulkaction").val());
	jQuery("#" + windowId + "_banid").val(jQuery('#items_array').val());
	tb_show("", "#TB_inline?width=640&height=" + height + "&inlineId=" + windowId + "&modal=true", false);
}
var current_image = '';
function send_to_editor(html) {
	var source = html.match(/src=\".*\" alt/);
	source = source[0].replace(/^src=\"/, "").replace(/" alt$/, "");
	jQuery('#banthumb-'+actInp).attr('value', source);
	jQuery('#thumb-'+actInp).attr('src', source);
	tb_remove();
}
//-->
</script>
	<div class="wrap">

<?php if( current_user_can('FlAG Import folder') ) { 
	$defaultpath = 'wp-content/';
?>
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
	        jQuery("#bannerfolder").val(path);
	    });
	    
	    jQuery("span.browsefiles").show().click(function(){
	    	jQuery("#file_browser").slideToggle();
	    });	
	  });
/* ]]> */
</script>

		<!-- import folder -->
		<div id="importfolder">
		<h2><?php _e('Import banners from folder', 'flag'); ?></h2>
			<form name="importfolder" id="importfolder_form" method="POST" action="<?php echo $filepath; ?>" accept-charset="utf-8" >
			<?php wp_nonce_field('flag_addbanner'); ?>
				<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row"><?php _e('Import from Server path:', 'flag'); ?></th> 
					<td><input type="text" size="35" id="bannerfolder" name="bannerfolder" value="<?php echo $defaultpath; ?>" /><span class="browsefiles button" style="display:none"><?php _e('Toggle DIR Browser',"flag"); ?></span>
						<div id="file_browser"></div><br />
						<p><label><input type="checkbox" name="delete_files" value="delete" /> &nbsp;
						<?php _e('delete files after import in WordPress Media Library','flag'); ?></label></p>
					</td> 
				</tr>
				</table>
				<div class="submit"><input class="button-primary" type="submit" name="importfolder" value="<?php _e('Import folder', 'flag'); ?>"/></div>
			</form>
		</div>
<?php } ?>

		<h2><?php _e('WordPress Image Library', 'flag'); ?></h2>
		<form id="bannerlib" class="flagform" method="POST" action="<?php echo $filepath; ?>" accept-charset="utf-8">
		<?php wp_nonce_field('flag_bulkbanner'); ?>
		<input type="hidden" name="page" value="banner-box" />
		
		<div class="tablenav">
			
			<div class="actions">
<?php if($added===false) { ?>
				<input name="updateMedia" class="button-primary" style="float: right;" type="submit" value="<?php _e('Update Media','flag'); ?>" />
				<?php if ( function_exists('json_encode') ) { ?>
				<select name="bulkaction" id="bulkaction">
					<option value="no_action" ><?php _e("No action",'flag'); ?></option>
					<option value="new_playlist" ><?php _e("Create new playlist",'flag'); ?></option>
				</select>
				<input name="showThickbox" class="button-secondary" type="submit" value="<?php _e('Apply','flag'); ?>" onclick="if ( !checkSelected() ) return false;" />
				<?php } ?>
                <a href="<?php echo admin_url( 'media-new.php'); ?>" class="button"><?php _e('Upload Banner(s)','flag'); ?></a>
				<input type="hidden" id="items_array" name="items_array" value="" />
<?php } else { ?>
				<input type="hidden" name="mode" value="save" />
				<input style="width: 80%;" type="text" id="items_array" name="items_array" value="<?php echo $added; ?>" />
				<input type="hidden" name="playlist_title" value="<?php echo $playlist['title']; ?>" />
				<input type="hidden" name="skinname" value="<?php echo $playlist['skin']; ?>" />
				<input type="hidden" name="skinaction" value="<?php echo $playlist['skin']; ?>" />
				<textarea style="display: none;" name="playlist_descr" cols="40" rows="1"><?php echo $playlist['description']; ?></textarea>
				<input name="addToPlaylist" class="button-secondary" type="submit" value="<?php _e('Update Playlist','flag'); ?>" onclick="if ( !checkSelected() ) return false;" />
<?php } ?>
			</div>
			
		</div>
		<table class="widefat" cellspacing="0">
			<thead>
			<tr>
        		<th class="cb" width="54" scope="col"><a href="#" onclick="checkAll(document.getElementById('bannerlib'));return false;"><?php _e('Check', 'flag'); ?></a></th>
        		<th class="id" width="64" scope="col"><div><?php _e('ID', 'flag'); ?></div></th>
        		<th class="thumb" width="110" scope="col"><div><?php _e('Thumbnail', 'flag'); ?></div></th>
        		<th class="title_filename" scope="col"><div><?php _e('Filename / Title / Link', 'flag'); ?></div></th>
        		<th class="description" scope="col"><div><?php _e('Description', 'flag'); ?></div></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
        		<th class="cb" scope="col"><a href="#" onclick="checkAll(document.getElementById('bannerlib'));return false;"><?php _e('Check', 'flag'); ?></a></th>
        		<th class="id" scope="col"><?php _e('Play', 'flag'); ?></th>
        		<th class="thumb" scope="col"><?php _e('Thumbnail', 'flag'); ?></th>
        		<th class="title_filename" scope="col"><?php _e('Filename / Title / Link', 'flag'); ?></th>
        		<th class="description" scope="col"><?php _e('Description', 'flag'); ?></th>
			</tr>
			</tfoot>
			<tbody>
<?php $bannerlist = get_posts( $args = array(
    'numberposts'     => -1,
    'orderby'         => 'ID',
    'order'           => 'DESC',
    'post_type'       => 'attachment',
    'post_mime_type'  => array('image') ) 
); 
$uploads = wp_upload_dir();
$flag_options = get_option('flag_options');	
if($bannerlist) {
	foreach($bannerlist as $ban) {
		$list[] = $ban->ID;
	}
    $class = ' class="alternate"';
	foreach($bannerlist as $ban) {
		$class = ( empty($class) ) ? ' class="alternate"' : '';
		$class2 = ( empty($class) ) ? '' : ' alternate';
		$ex = $checked = '';
		if($added!==false && in_array($ban->ID, $exclude) ) { 
			$ex = ' style="background-color:#DDFFBB;" title="'.__("Already Added", "flag").'"';
			$checked = ' checked="checked"';
		}
		$bg = ( !isset($class) || $class == 'class="alternate"' ) ? 'f9f9f9' : 'ffffff';
        $thumb = $banthumb = get_post_meta($ban->ID, 'thumbnail', true);
        $link = get_post_meta($ban->ID, 'link', true);
        if(empty($thumb)) {
          $thumb = wp_get_attachment_thumb_url($ban->ID);
          $banthumb = '';
        }
		$url = wp_get_attachment_url($ban->ID);
?>
		<tr id="ban-<?php echo $ban->ID; ?>"<?php echo $class.$ex; ?>>
			<td class="cb"><input name="doaction[]" type="checkbox"<?php echo $checked; ?> value="<?php echo $ban->ID; ?>" /></td>
			<td class="id"><p style="margin-bottom: 3px; white-space: nowrap;">ID: <?php echo $ban->ID; ?></p></td>
			<td class="thumb">
				<a class="thickbox" title="<?php echo basename($url); ?>" href="<?php echo $url; ?>"><img id="thumb-<?php echo $ban->ID; ?>" src="<?php echo $thumb; ?>" width="100" height="100" alt="" /></a>
				<input id="banthumb-<?php echo $ban->ID; ?>" name="item_a[<?php echo $ban->ID; ?>][post_thumb]" type="hidden" value="<?php echo $banthumb; ?>" />
			</td>
			<td class="title_filename">
				<strong><a href="<?php echo $url; ?>"><?php echo basename($url); ?></a></strong><br />
				<textarea name="item_a[<?php echo $ban->ID; ?>][post_title]" cols="20" rows="1" style="width:95%; height: 25px; overflow:hidden;"><?php echo $ban->post_title; ?></textarea><br />
				<?php _e('URL', 'flag'); ?>: <input id="banlink-<?php echo $ban->ID; ?>" name="item_a[<?php echo $ban->ID; ?>][link]" style="width:50%;" type="text" value="<?php echo $link; ?>" /><br />
    			<?php
    			$actions = array();
    			$actions['add_thumb']   = '<a class="thickbox" onclick="actInp='.$ban->ID.'" href="media-upload.php?type=image&amp;TB_iframe=1&amp;width=640&amp;height=400" title="' . __('Add an Image','flag') . '">' . __('change thumb', 'flag') . '</a>';
    			$actions['del_thumb']   = '<a class="del_thumb" data-id="'.$ban->ID.'" href="#" title="' . __('Delete an Image','flag') . '">' . __('restore thumb', 'flag') . '</a>';
    			//$actions['delete'] = '<a href="' . wp_nonce_url("admin.php?page=banner-box&amp;mode=delmedia&amp;id=".$ban->ID, 'flag_delmedia'). '" class="delete column-delete" onclick="javascript:check=confirm( \'' . attribute_escape(sprintf(__('Delete "%s"' , 'flag'), $ban->post_title)). '\');if(check==false) return false;">' . __('Delete from WP Media Library','flag') . '</a>';
    			$action_count = count($actions);
    			$i = 0;
    			echo '<p class="row-actions">';
    			foreach ( $actions as $action => $link ) {
    				++$i;
    				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
    				echo "<span class='$action'>$link$sep</span>";
    			}
    			echo '</p>';
    			?>
			</td>
			<td class="description">
				<textarea name="item_a[<?php echo $ban->ID; ?>][post_content]" style="width:95%; height: 96px; margin-top: 2px; font-size:12px; line-height:115%;" rows="1" ><?php echo $ban->post_content; ?></textarea>
			</td>
		</tr>
		<?php
	}
} else {
	echo '<tr><td colspan="5" align="center"><strong>'.__('No images in WordPress Media Library.','flag').'</strong></td></tr>';
}
?>			
			</tbody>
		</table>
		</form>
	</div>

	<!-- #new_playlist -->
	<div id="new_playlist" style="display: none;" >
		<form id="form_new_playlist" method="POST" action="<?php echo $filepath; ?>" accept-charset="utf-8">
		<?php wp_nonce_field('flag_thickbox_form'); ?>
		<input type="hidden" id="new_playlist_banid" name="items_array" value="" />
		<input type="hidden" id="new_playlist_bulkaction" name="TB_bulkaction" value="" />
		<input type="hidden" name="mode" value="save" />
		<input type="hidden" name="page" value="banner-box" />
		<table width="100%" border="0" cellspacing="3" cellpadding="3" >
			<tr valign="top">
				<th align="left" style="padding-top: 5px;"><?php _e('Playlist Title','flag'); ?></th>
				<td><input type="text" class="alignleft" name="playlist_title" value="" />
                    <div class="alignright"><strong><?php _e("Choose skin", 'flag'); ?>:</strong>
                        <select id="skinname" name="skinname" style="width: 200px; height: 24px; font-size: 11px;">
                          <?php require_once (dirname(__FILE__) . '/get_skin.php');
                            $all_skins = get_skins($skin_folder='', $type='b');
                            if(count($all_skins)) {
                            	foreach ( (array)$all_skins as $skin_file => $skin_data) {
                            		echo '<option value="'.dirname($skin_file).'">'.$skin_data['Name'].'</option>'."\n";
                            	}
                            } else {
                                echo '<option value="banner_default">'.__("No Skins", "flag").'</option>';
                            }
                          ?>
                        </select>
                    </div>
                </td>
			</tr>
			<tr valign="top">
				<th align="left" style="padding-top: 5px;"><?php _e('Playlist Description','flag'); ?></th>
				<td><textarea style="width:100%;" rows="3" cols="60" name="playlist_descr"></textarea></td>
			</tr>
		  	<tr>
				<td>&nbsp;</td>
		    	<td align="right"><input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel', 'flag'); ?>&nbsp;" onclick="tb_remove()"/>
		    		&nbsp; &nbsp; &nbsp;
                    <input class="button-primary " type="submit" name="TB_NewPlaylist" value="<?php _e('OK', 'flag'); ?>" />
		    	</td>
			</tr>
		</table>
		</form>
	</div>
	<!-- /#new_playlist -->	
<?php } ?>