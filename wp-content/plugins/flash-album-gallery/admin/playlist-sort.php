<?php

/**
 * @author Sergey Pasyuk
 * @copyright 2009
 */

function flag_playlist_order($playlist){
	global $wpdb;
	
	//this is the url without any presort variable
	$base_url = admin_url() . 'admin.php?page=' . $_GET['page'];
	$flag_options = get_option('flag_options');
	$playlistPath = $flag_options['galleryPath'].'playlists/'.$_GET['playlist'].'.xml';
	$playlist = get_playlist_data(ABSPATH.$playlistPath);
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
			<th width="260"><div><?php _e('Play', 'flag'); ?></div></th>
			<th class="header"><p><?php _e('Filename', 'flag'); ?></p></th>
			<th class="header"><p><?php _e('Title', 'flag'); ?></p></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
			<th><?php _e('ID', 'flag'); ?></th>
			<th><?php _e('Play', 'flag'); ?></th>
			<th><?php _e('Filename', 'flag'); ?></th>
			<th><?php _e('Title', 'flag'); ?></th>
	</tr>
	</tfoot>
	<tbody id="listitems">
<?php
if(count($items_a)) {
	$flag_options = get_option('flag_options');
	$counter	= 0;
	foreach($items_a as $item) {
		$mp3 = get_post($item);
		$alternate = ( !isset($alternate) || $alternate == 'alternate' ) ? '' : 'alternate';	
		$counter++;
		$bg = ( !isset($alternate) || $alternate == 'alternate' ) ? 'f9f9f9' : 'ffffff';
		$url = wp_get_attachment_url($mp3->ID);
		?>
		<tr id="$mp3-<?php echo $mp3->ID; ?>" class="<?php echo $alternate; ?> iedit"  valign="top">
				<td scope="row"><input type="hidden" name="item_a[<?php echo $mp3->ID; ?>][ID]" value="<?php echo $mp3->ID; ?>" /><strong><?php echo $mp3->ID; ?></strong></td>
				<td><script type="text/javascript">swfobject.embedSWF("<?php echo FLAG_URLPATH; ?>lib/mini.swf", "c-<?php echo $mp3->ID; ?>", "250", "20", "10.1.52", "expressInstall.swf", {path:"<?php echo str_replace(array('.mp3'), array(''), $url); ?>",bgcolor:"<?php echo $flag_options['mpBG'] ?>",color1:"<?php echo $flag_options['mpColor1'] ?>",color2:"<?php echo $flag_options['mpColor2'] ?>"}, {wmode:"transparent"}, {id:"f-<?php echo $mp3->ID; ?>",name:"f-<?php echo $mp3->ID; ?>"});</script>
<div class="play"><span id="c-<?php echo $mp3->ID; ?>"></span></div></td>
				<td><?php echo basename($url); ?></td>
				<td><?php echo $mp3->post_title; ?></td>
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
