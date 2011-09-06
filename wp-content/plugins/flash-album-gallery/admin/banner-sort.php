<?php

/**
 * @author Sergey Pasyuk
 * @copyright 2011
 */

function flag_b_playlist_order($playlist){
	global $wpdb;
	
	//this is the url without any presort variable
	$base_url = admin_url() . 'admin.php?page=' . $_GET['page'];
	$flag_options = get_option('flag_options');
	$playlistPath = $flag_options['galleryPath'].'playlists/banner/'.$_GET['playlist'].'.xml';
	$playlist = get_b_playlist_data(ABSPATH.$playlistPath);
	$items_a = $playlist['items'];
	$items = implode(',',$playlist['items']);
?>
<script type="text/javascript" src="<?php echo FLAG_URLPATH; ?>admin/js/jquery.tablednd_0_5.js"></script>
<script type="text/javascript" src="<?php echo FLAG_URLPATH; ?>admin/js/jquery.tablesorter.js"></script>
<div class="wrap">
			<h2><?php _e('Sort Gallery', 'flag'); ?></h2>

	<div class="alignright tablenav" style="margin-bottom: -36px;">
		<a href="<?php echo $base_url.'&amp;playlist='.$_GET['playlist'].'&amp;mode=edit'; ?>" class="button-secondary action"><?php _e('Back to playlist', 'flag'); ?></a>
	</div>
	<form id="sortPlaylist" method="POST" action="<?php echo $base_url.'&amp;playlist='.$_GET['playlist'].'&amp;mode=edit'; ?>" accept-charset="utf-8">
		<div class="alignleft tablenav">
			<?php wp_nonce_field('flag_updatesortorder'); ?>
			<input class="button-primary action" type="submit" name="updatePlaylist" value="<?php _e('Update Sort Order', 'flag'); ?>" />
		</div>
		<br clear="all" />
		<input type="hidden" name="playlist_title" value="<?php echo $playlist['title']; ?>" />
		<input type="hidden" name="skinname" value="<?php echo $playlist['skin']; ?>" />
		<input type="hidden" name="skinaction" value="<?php echo $playlist['skin']; ?>" />
		<textarea style="display: none;" name="playlist_descr" cols="40" rows="1"><?php echo $playlist['description']; ?></textarea>
<script type="text/javascript">
/*<![CDATA[*/
jQuery(document).ready(function($) {
    // Initialise the table
    jQuery("#listitems").tableDnD({
      onDragClass: "myDragClass",
	  	onDrop: function() {
				jQuery("#listitems tr:even").addClass('alternate');
				jQuery("#listitems tr:odd").removeClass('alternate');
      }
    });
		$("#flag-listitems").tablesorter({ 
        // pass the headers argument and assing a object 
        headers: { 
            // assign the secound column (we start counting zero) 
            1: { 
                // disable it by setting the property sorter to false 
                sorter: false 
            }
        } 
    });
		$("#flag-listitems").bind("sortEnd",function() { 
				jQuery("#listitems tr:even").addClass('alternate');
				jQuery("#listitems tr:odd").removeClass('alternate');
    }); 

});
/*]]>*/
</script>
<table id="flag-listitems" class="widefat fixed" cellspacing="0" >

	<thead>
	<tr>
			<th class="header" width="54"><p style="margin-right:-10px;"><?php _e('ID', 'flag'); ?></p></th>
			<th width="50"><div><?php _e('Thumb', 'flag'); ?></div></th>
			<th class="header"><p><?php _e('Filename', 'flag'); ?></p></th>
			<th class="header"><p><?php _e('Title', 'flag'); ?></p></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
			<th><?php _e('ID', 'flag'); ?></th>
			<th><?php _e('Thumb', 'flag'); ?></th>
			<th><?php _e('Filename', 'flag'); ?></th>
			<th><?php _e('Title', 'flag'); ?></th>
	</tr>
	</tfoot>
	<tbody id="listitems">
<?php
if(count($items_a)) {
	$counter	= 0;
	foreach($items_a as $item) {
		$ban = get_post($item);
        $thumb = get_post_meta($item, 'thumbnail', true);
        if(empty($thumb)) {
          $thumb = wp_get_attachment_thumb_url($ban->ID);
        }
		$alternate = ( !isset($alternate) || $alternate == 'alternate' ) ? '' : 'alternate';	
		$counter++;
		$bg = ( !isset($alternate) || $alternate == 'alternate' ) ? 'f9f9f9' : 'ffffff';
		$url = wp_get_attachment_url($ban->ID);
		?>
		<tr id="$ban-<?php echo $ban->ID; ?>" class="<?php echo $alternate; ?> iedit"  valign="top">
				<td scope="row"><input type="hidden" name="item_a[<?php echo $ban->ID; ?>][ID]" value="<?php echo $ban->ID; ?>" /><strong><?php echo $ban->ID; ?></strong></td>
				<td width="50"><a class="thickbox" title="<?php echo basename($url); ?>" href="<?php echo $url; ?>"><img id="thumb-<?php echo $ban->ID; ?>" src="<?php echo $thumb; ?>" width="40" height="40" alt="" /></a></td>
				<td><?php echo basename($url); ?></td>
				<td><?php echo $ban->post_title; ?></td>
		</tr>
		<?php
	}
} else {
	echo '<tr><td colspan="4" align="center"><strong>'.__('No entries found','flag').'</strong></td></tr>';
}
?>
		</tbody>
	</table>
	<p class="actions"><input type="submit" class="button-primary action"  name="updatePlaylist" value="<?php _e('Update Sort Order', 'flag'); ?>" /></p>
</form>	
<br class="clear"/>
</div><!-- /#wrap -->

<?php
}
?>
