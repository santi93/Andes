<?php

/**
 * @author Sergey Pasyuk
 * @copyright 2009
 */

function flag_sortorder($galleryID = 0){
	global $wpdb, $flag;
	
	if ($galleryID == 0) return;

	$galleryID = (int) $galleryID;
	
	if (isset ($_POST['updateSortorder']))  {
		check_admin_referer('flag_updatesortorder');
		// get variable new sortorder 
		$neworder = array();
		foreach($_POST as $id) {
			$neworder[] = (int) $id;
		} 
		$sortindex = 1;
		foreach($neworder as $pic_id) {
			$wpdb->query("UPDATE $wpdb->flagpictures SET sortorder = '$sortindex' WHERE pid = $pic_id");
			$sortindex++;
		}
		$firstImage = $wpdb->get_var("SELECT pid FROM $wpdb->flagpictures WHERE galleryid = '$galleryID' ORDER by pid DESC limit 0,1");
		if ($firstImage)
			$wpdb->query("UPDATE $wpdb->flaggallery SET previewpic = '$firstImage' WHERE gid = '$galleryID'");

		flagGallery::show_message(__('Sort order changed','flag'));

		}

	
	// get gallery values
	$act_gallery = $wpdb->get_row("SELECT * FROM $wpdb->flaggallery WHERE gid = '$galleryID' ");

	// set gallery url
	$act_gallery_url 	= get_option ('siteurl')."/".$act_gallery->path."/";
	$act_thumbnail_url 	= get_option ('siteurl')."/".$act_gallery->path.flagGallery::get_thumbnail_folder($act_gallery->path, FALSE);

	// look for presort args	
	$picturelist = $wpdb->get_results("SELECT * FROM $wpdb->flagpictures WHERE galleryid = '$galleryID' ORDER BY sortorder {$dir}");

	//this is the url without any presort variable
	$base_url = admin_url() . 'admin.php?page=flag-manage-gallery&amp;mode=sort&amp;gid=' . $galleryID;
	
?>
<script type="text/javascript" src="<?php echo FLAG_URLPATH; ?>admin/js/jquery.tablednd_0_5.js"></script>
<script type="text/javascript" src="<?php echo FLAG_URLPATH; ?>admin/js/jquery.tablesorter.js"></script>
<div class="wrap">
			<h2><?php _e('Sort Gallery', 'flag'); ?></h2>

	<form class="alignright" method="POST" action="<?php echo admin_url() . 'admin.php?page=flag-manage-gallery&amp;mode=edit&amp;gid=' . $galleryID; ?>" accept-charset="utf-8">
		<div class="alignright tablenav" style="margin-bottom: -36px;">
			<input class="button-secondary action" type="submit" name="backToGallery" value="<?php _e('Back to gallery', 'flag'); ?>" />
		</div>
	</form>
	<form id="sortGallery" method="POST" action="<?php echo $base_url; ?>" accept-charset="utf-8">
		<div class="alignleft tablenav">
			<?php wp_nonce_field('flag_updatesortorder'); ?>
			<input class="button-primary action" type="submit" name="updateSortorder" value="<?php _e('Update Sort Order', 'flag'); ?>" />
		</div>
		<br clear="all" />
<script type="text/javascript">
/*<![CDATA[*/
jQuery(document).ready(function($) {
    // Initialise the table
    jQuery("#listimages").tableDnD({
      onDragClass: "myDragClass",
	  	onDrop: function() {
				jQuery("#listimages tr:even").addClass('alternate');
				jQuery("#listimages tr:odd").removeClass('alternate');
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
				jQuery("#listimages tr:even").addClass('alternate');
				jQuery("#listimages tr:odd").removeClass('alternate');
    }); 

});
/*]]>*/
</script>
<table id="flag-listitems" class="widefat fixed" cellspacing="0" >

	<thead>
	<tr>
			<th class="header" width="30px"><p style="margin-right:-10px;"><?php _e('ID', 'flag'); ?></p></th>
			<th width="80"><?php _e('Thumb', 'flag'); ?></th>
			<th class="header"><p><?php _e('Filename', 'flag'); ?></p></th>
			<th class="header" width="130"><p><?php _e('Date', 'flag'); ?></p></th>
			<th class="header"><p><?php _e('Alt &amp; Title Text', 'flag'); ?></p></th>
	</tr>
	</thead>
	<tfoot>
	<tr>
			<th><?php _e('ID', 'flag'); ?></th>
			<th><?php _e('Thumb', 'flag'); ?></p></th>
			<th><?php _e('Filename', 'flag'); ?></th>
			<th><?php _e('Date', 'flag'); ?></th>
			<th><?php _e('Alt &amp; Title Text', 'flag'); ?></th>
	</tr>
	</tfoot>
	<tbody id="listimages">
<?php
if($picturelist) {
	
		$alternate = '';
	foreach($picturelist as $picture) {

		$pid       = (int) $picture->pid;
		$alternate = ( $alternate == 'alternate' ) ? '' : 'alternate';	
		$date = mysql2date(get_option('date_format'), $picture->imagedate);
		$time = mysql2date(get_option('time_format'), $picture->imagedate);
		
		?>
		<tr id="picture-<?php echo $pid; ?>" class="<?php echo $alternate; ?> iedit"  valign="top">
				<td scope="row"><strong><?php echo $pid; ?></strong><input type="hidden" name="sortpid-<?php echo $pid; ?>" value="<?php echo $pid; ?>" /></td>
				<td><a href="<?php echo $act_gallery_url.$picture->filename; ?>" class="thickbox" title="<?php echo $picture->filename; ?>">
					<img class="thumb" src="<?php echo $act_thumbnail_url ."thumbs_" .$picture->filename; ?>" style="width:40px; height:auto;" id="thumb-<?php echo $pid; ?>" />
				</a></td>
				<td><?php echo $picture->filename; ?></td>
				<td><?php echo $date; ?></td>
				<td><?php echo stripslashes($picture->alttext); ?></td>
		</tr>
		<?php
	}
} else {
	echo '<tr><td colspan="5" align="center"><strong>'.__('No entries found','flag').'</strong></td></tr>';
}
?>
	
		</tbody>
	</table>
	<p class="actions"><input type="submit" class="button-primary action"  name="updateSortorder" onclick="saveImageOrder()" value="<?php _e('Update Sort Order', 'flag'); ?>" /></p>
</form>	
<br class="clear"/>
</div><!-- /#wrap -->

<?php
}
?>
