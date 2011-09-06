<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {	die('You are not allowed to call this page directly.');}

function flag_playlist_edit() {
	global $wpdb;
	$filepath = admin_url() . 'admin.php?page=' . $_GET['page'];
	$all_playlists = get_playlists();
	$flag_options = get_option('flag_options');
	$playlistPath = $flag_options['galleryPath'].'playlists/'.$_GET['playlist'].'.xml';
	$playlist = get_playlist_data(ABSPATH.$playlistPath);
	$items_a = $playlist['items'];
	$items = implode(',',$playlist['items']);
?>
<script type="text/javascript"> 
//<![CDATA[
function checkAll(form)
{
	form.find(':checkbox').each(function(){this.checked = !this.checked});
	return false;
}

function getNumChecked(form)
{
	var num = 0;
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].name == "doaction[]")
				if(form.elements[i].checked == true)
					num++;
		}
	}
	return num;
}

// this function check for a the number of selected images, sumbmit false when no one selected
function checkSelected() {

	var numchecked = getNumChecked(document.getElementById('updatePlaylist'));
	 
	if(numchecked < 1) { 
		alert('<?php echo js_escape(__("No items selected", "flag")); ?>');
		return false; 
	} 
	
	actionId = jQuery('#bulkaction').val();
	
	switch (actionId) {
		case "delete_items":
			return confirm('<?php echo sprintf(js_escape(__("You are about to delete %s item(s) \n \n 'Cancel' to stop, 'OK' to proceed.",'flag')), "' + numchecked + '") ; ?>');
			break;			
	}
	
	return confirm('<?php echo sprintf(js_escape(__("You are about to start the bulk edit for %s item(s) \n \n 'Cancel' to stop, 'OK' to proceed.",'flag')), "' + numchecked + '") ; ?>');
}

function showDialog( windowId, height ) {
	var form = document.getElementById('updatePlaylist');
	var elementlist = "";
	for (i = 0, n = form.elements.length; i < n; i++) {
		if(form.elements[i].type == "checkbox") {
			if(form.elements[i].name == "doaction[]")
				if(form.elements[i].checked == true)
					if (elementlist == "")
						elementlist = form.elements[i].value
					else
						elementlist += "," + form.elements[i].value ;
		}
	}
	jQuery("#" + windowId + "_bulkaction").val(jQuery("#bulkaction").val());
	jQuery("#" + windowId + "_playlist").val(elementlist);
	// console.log (jQuery("#TB_playlist").val());
	tb_show("", "#TB_inline?width=640&height=" + height + "&inlineId=" + windowId + "&modal=true", false);
}
var current_image = '';
function send_to_editor(html) {
	var source = html.match(/src=\".*\" alt/);
	source = source[0].replace(/^src=\"/, "").replace(/" alt$/, "");
	//var id = html.match(/wp-image-(\d+(\.\d)*)/ig);
	//id = id[0].match(/\d+/);
	jQuery('#mp3thumb-'+actInp).attr('value', source);
	jQuery('#thumb-'+actInp).attr('src', source);
	tb_remove();
}
jQuery(document).ready(function(){
  jQuery('.del_thumb').click(function(){
    var id = jQuery(this).attr('data-id');
	jQuery('#mp3thumb-'+id).attr('value', '');
	jQuery('#thumb-'+id).attr('src', '<?php echo site_url()."/wp-includes/images/crystal/audio.png"; ?>');
    return false;
  });
  jQuery('#skinname').change(function(){
  	var skin = jQuery(this).val();
	jQuery('#skinOptions').attr("href","<?php echo FLAG_URLPATH; ?>admin/skin_options.php?show_options=1&amp;skin="+skin+"&amp;TB_iframe=1&amp;width=600&amp;height=560");
  });
})
//]]>
</script>

<div class="wrap">
<h2><?php _e( 'Playlist', 'flag' ); ?>: <?php echo $playlist['title']; ?></h2>
<div style="float: right; margin: -20px 3px 0 0;">
<span><a href="<?php echo $filepath; ?>"><?php _e('Back to Music Box', 'flag'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
<select name="select_playlist" onchange="window.location.href=this.options[this.selectedIndex].value">
	<option selected="selected"><?php _e('Choose another playlist', 'flag'); ?></option>
<?php 
	foreach((array)$all_playlists as $playlist_file => $playlist_data) {
		$playlist_name = basename($playlist_file, '.xml');
		if ($playlist_file == $_GET['playlist']) continue; 
?>
	<option value="<?php echo $filepath."&amp;playlist=".$playlist_name."&amp;mode=edit"; ?>"><?php echo $playlist_data['title']; ?></option>
<?php 
	}
?>
</select>
</div>
<form id="updatePlaylist" class="flagform" method="POST" action="<?php echo $filepath."&amp;playlist=".$_GET['playlist']."&amp;mode=edit"; ?>" accept-charset="utf-8">
<?php wp_nonce_field('flag_updateplaylist'); ?>
<input type="hidden" name="page" value="manage-playlist" />

<div id="poststuff" class="metabox-holder">
<div id="post-body"><div id="post-body-content"><div id="normal-sortables" style="position: relative;">
	<div id="flagalleryset" class="postbox" >
		<h3 class="hndle"><span><?php _e('Playlist settings', 'flag'); ?></span></h3>
		<div class="inside">
			<table cellspacing="8" cellpadding="0" border="0">
				<tr>
					<th align="left" valign="middle" scope="row"><?php _e('Shortcode', 'flag'); ?>:</th>
					<td align="left" valign="middle"><input type="text" readonly="readonly" size="50" onfocus="this.select()" value="[grandmusic playlist=<?php echo $_GET['playlist']; ?>]" /></td>
					<td rowspan="3" align="left" valign="top"><div style="font-size:11px;"><strong style="display: inline-block; width: 100px;"><?php _e("Playlist Skin", 'flag'); ?>:</strong>
						<input id="skinaction" type="hidden" name="skinaction" value="<?php echo $playlist['skin']; ?>" />
                        <select id="skinname" name="skinname" style="width: 200px; height: 24px; font-size: 11px;">
                          <?php require_once (dirname(__FILE__) . '/get_skin.php');
                            $all_skins = get_skins($skin_folder='', $type='m');
                            if(count($all_skins)) {
                            	foreach ( (array)$all_skins as $skin_file => $skin_data) {
	                            	$cur = ($playlist['skin'] == dirname($skin_file))? ' selected="selected"' : '';
                            		echo '<option'.$cur.' value="'.dirname($skin_file).'">'.$skin_data['Name'].'</option>'."\n";
                            	}
                            } else {
                                echo '<option value="music_default">'.__("No Skins", "flag").'</option>';
                            }
                          ?>
                        </select>&nbsp;&nbsp;<a id="skinOptions" class="thickbox" href="<?php echo FLAG_URLPATH.'admin/skin_options.php?show_options=1&amp;skin='.$playlist['skin'].'&amp;TB_iframe=1&amp;width=600&amp;height=560'; ?>"><?php _e('Change Skin Options', 'flag' ); ?></a>
                    </div>
					<p style="margin:10px 0 0 100px;"><input type="submit" id="updatePlaylistSkin" name="updatePlaylistSkin" class="button-primary action"  value="<?php _e('Update skin options for this playlist', 'flag'); ?>" /></p>
					</td>
				</tr>
				<tr>
					<th align="left" valign="middle" scope="row"><?php _e('Title', 'flag'); ?>:</th>
					<td align="left" valign="middle"><input type="text" size="50" name="playlist_title" value="<?php echo $playlist['title']; ?>" /></td>
				</tr>
				<tr>
					<th align="left" valign="top" scope="row"><?php _e('Description', 'flag'); ?>:</th> 
					<td align="left" valign="top"><textarea name="playlist_descr" cols="60" rows="2" style="width: 95%" ><?php echo $playlist['description']; ?></textarea></td>
				</tr>
				<!--<tr>
					<th align="left" valign="top" scope="row"><?php _e('Path', 'flag'); ?>:</th> 
					<td align="left" colspan="2" valign="top"><?php echo $playlistPath; ?></td>
				</tr>-->
			</table>
			<div class="clear"></div>
		</div>
	</div>
</div></div></div>
</div> <!-- poststuff -->
<div class="tablenav flag-tablenav">
	<select id="bulkaction" name="bulkaction" class="alignleft">
		<option value="no_action" ><?php _e("No action",'flag')?></option>
		<option value="delete_items" ><?php _e("Delete items",'flag')?></option>
	</select>
	<input class="button-secondary alignleft" style="margin-right:10px;" type="submit" name="updatePlaylist" value="<?php _e("OK",'flag')?>" onclick="if ( !checkSelected() ) return false;" />
	<a href="<?php echo $filepath."&amp;playlist=".$_GET['playlist']."&amp;mode=sort"; ?>" class="button-secondary alignleft" style="margin:1px 10px 0 0;"><?php _e("Sort Playlist",'flag')?></a>
	<a href="<?php echo $filepath."&amp;playlist=".$_GET['playlist']."&amp;mode=add"; ?>" onClick="jQuery('#form_listitems').submit();return false;" class="button-secondary alignleft" style="margin:1px 10px 0 0;"><?php _e("Add/Remove Items from Playlist",'flag')?></a>
	<input type="submit" name="updatePlaylist" class="button-primary action alignright"  value="<?php _e("Update Playlist",'flag')?>" />
</div>

<table id="flag-listmusic" class="widefat fixed" cellspacing="0" >

	<thead>
	<tr>
		<th class="cb" width="54" scope="col"><a href="#" onclick="checkAll(document.getElementById('updatePlaylist'));return false;"><?php _e('Check', 'flag'); ?></a></th>
		<th class="id" width="134" scope="col"><div><?php _e('ID', 'flag'); ?></div></th>
		<th class="size" width="75" scope="col"><div><?php _e('Size', 'flag'); ?></div></th>
		<th class="thumb" width="110" scope="col"><div><?php _e('Thumbnail', 'flag'); ?></div></th>
		<th class="title_filename" scope="col"><div><?php _e('Filename / Title', 'flag'); ?></div></th>
		<th class="description" scope="col"><div><?php _e('Description', 'flag'); ?></div></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th class="cb" scope="col"><a href="#" onclick="checkAll(document.getElementById('updatePlaylist'));return false;"><?php _e('Check', 'flag'); ?></a></th>
		<th class="id" scope="col"><?php _e('Play', 'flag'); ?></th>
		<th class="size" scope="col"><?php _e('Size', 'flag'); ?></th>
		<th class="thumb" scope="col"><?php _e('Thumbnail', 'flag'); ?></th>
		<th class="title_filename" scope="col"><?php _e('Filename / Title', 'flag'); ?></th>
		<th class="description" scope="col"><?php _e('Description', 'flag'); ?></th>
	</tr>
	</tfoot>
	<tbody>
<?php 
if(count($items_a)) {
	$counter = 0;
    $alt = ' class="alternate"';
	$flag_options = get_option('flag_options');
	$uploads = wp_upload_dir();
	foreach($items_a as $item) {
		$mp3 = get_post($item);
        $thumb = $mp3thumb = get_post_meta($item, 'thumbnail', true);
        if(empty($thumb)) {
          $thumb = site_url().'/wp-includes/images/crystal/audio.png';
          $mp3thumb = '';
        }
		$alt = ( empty($alt) ) ? ' class="alternate"' : '';
		$alt2 = ( empty($alt) ) ? '' : ' alternate';
		$url = wp_get_attachment_url($mp3->ID);
		$counter++;
?>
		<tr id="mp3-<?php echo $mp3->ID; ?>"<?php echo $alt; ?> valign="top">
			<th class="cb" scope="row" height="24" style="padding-bottom: 0; border-bottom: none;"><input name="doaction[]" type="checkbox" value="<?php echo $mp3->ID; ?>" /></th>
			<td class="id" style="padding-bottom: 0; border-bottom: none;"><p style="margin-bottom: 3px; white-space: nowrap;">ID: <?php echo $mp3->ID; ?></p></td>
			<td class="size" style="padding-bottom: 0; border-bottom: none;"><?php 
				$path = $uploads['basedir'].str_replace($uploads['baseurl'],'',$url);
				$size = filesize($path);
				echo round($size/1024/1024,2).' Mb';
			?></td>
			<td class="thumb" rowspan="2">
				<img id="thumb-<?php echo $mp3->ID; ?>" src="<?php echo $thumb; ?>" width="100" height="100" alt="" />
				<input id="mp3thumb-<?php echo $mp3->ID; ?>" name="item_a[<?php echo $mp3->ID; ?>][post_thumb]" type="hidden" value="<?php echo $mp3thumb; ?>" />
			</td>
			<td class="title_filename" rowspan="2">
				<strong><a href="<?php echo $url; ?>"><?php echo basename($url); ?></a></strong><br />
				<textarea name="item_a[<?php echo $mp3->ID; ?>][post_title]" cols="20" rows="1" style="width:95%; height: 25px; overflow:hidden;"><?php echo $mp3->post_title; ?></textarea><br />
    			<?php
    			$actions = array();
    			$actions['add_thumb']   = '<a class="thickbox" onclick="actInp='.$mp3->ID.'" href="media-upload.php?type=image&amp;TB_iframe=1&amp;width=640&amp;height=400" title="' . __('Add an Image','flag') . '">' . __('add thumb', 'flag') . '</a>';
    			$actions['del_thumb']   = '<a class="del_thumb" data-id="'.$mp3->ID.'" href="#" title="' . __('Delete an Image','flag') . '">' . __('remove thumb', 'flag') . '</a>';
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
			<td class="description" rowspan="2">
				<textarea name="item_a[<?php echo $mp3->ID; ?>][post_content]" style="width:95%; height: 96px; margin-top: 2px; font-size:12px; line-height:115%;" rows="1" ><?php echo $mp3->post_content; ?></textarea>
			</td>
		</tr>
        <tr class="mp3-<?php echo $mp3->ID.$alt2; ?>">
            <td valign="top" class="player" colspan="3"><script type="text/javascript">swfobject.embedSWF("<?php echo FLAG_URLPATH; ?>lib/mini.swf", "c-<?php echo $mp3->ID; ?>", "250", "20", "10.1.52", "expressInstall.swf", {path:"<?php echo str_replace(array('.mp3'), array(''), $url); ?>",bgcolor:"<?php echo $flag_options['mpBG'] ?>",color1:"<?php echo $flag_options['mpColor1'] ?>",color2:"<?php echo $flag_options['mpColor2'] ?>"}, {wmode:"transparent"}, {id:"f-<?php echo $mp3->ID; ?>",name:"f-<?php echo $mp3->ID; ?>"});</script>
<div class="play" style="padding-left: 4px;"><span id="c-<?php echo $mp3->ID; ?>"></span></div></td>
        </tr>
		<?php
	}
}
 
// In the case you have no capaptibility to see the search result
if ( $counter==0 )
	echo '<tr><td colspan="5" align="center"><strong>'.__('No entries found','flag').'</strong></td></tr>';

?>
	
		</tbody>
	</table>
	<p class="submit" style="text-align: right;"><input type="submit" class="button-primary action" name="updatePlaylist" value="<?php _e("Update Playlist",'flag')?>" /></p>
	</form>	
	<form id="form_listitems" name="form_listitems" method="POST" action="<?php echo $filepath."&amp;playlist=".$_GET['playlist']."&amp;mode=add"; ?>">
		<input type="hidden" name="items" value="<?php echo $items; ?>" />
	</form>
	<br class="clear"/>
	</div><!-- /#wrap -->
	<?php
}

?>
