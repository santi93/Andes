<?php

/**
 * @title  Add action/filter for the upload tab 
 */

function flag_wp_upload_tabs ($tabs) {

	$newtab = array('flag' => __('FlAG Gallery','flag'));
 
    return array_merge($tabs,$newtab);
}
	
add_filter('media_upload_tabs', 'flag_wp_upload_tabs');

function media_upload_flag() {
	
	// Generate TinyMCE HTML output
	if ( isset($_POST['send']) ) {
		$keys = array_keys($_POST['send']);
		$send_id = (int) array_shift($keys);
		$image = $_POST['image'][$send_id];
		$alttext = stripslashes( htmlspecialchars ($image['alttext'], ENT_QUOTES));
		$description = stripslashes (htmlspecialchars($image['description'], ENT_QUOTES));
		
		// here is no new line allowed
		$clean_description = preg_replace("/\n|\r\n|\r$/", " ", $description);
		$img = flagdb::find_image($send_id);
		$class="flag-singlepic flag-{$image['align']}";
		
		// Build output
		if ($image['size'] == "thumbnail") 
			$html = "<img src='{$image['thumb']}' alt='$alttext' class='$class' align='{$image['align']}' />";
		// Wrap the link to the fullsize image around	
		$html = "<a href='{$image['url']}' title='$clean_description'>$html</a>";

		if ($image['size'] == "full") 
			$html = "<img src='{$image['url']}' alt='$alttext' class='$class' align='{$image['align']}' />";
		
		media_upload_flag_save_image();
		
		// Return it to TinyMCE
		return media_send_to_editor($html);
	}
	
	// Save button
	if ( isset($_POST['save']) ) {
		media_upload_flag_save_image();
	}
		
	return wp_iframe( 'media_upload_flag_form', $errors );
}

add_action('media_upload_flag', 'media_upload_flag');

function media_upload_flag_save_image() {
		
		global $wpdb;
		
		check_admin_referer('flag-media-form');
		
		if ( !empty($_POST['image']) ) foreach ( $_POST['image'] as $image_id => $image ) {
		
		// Function save desription
		$alttext   		= esc_attr($image['alttext']);
		$description    = esc_attr($image['description']);
		
		$wpdb->query("UPDATE $wpdb->flagpictures SET alttext= '$alttext', description = '$description' WHERE pid = '$image_id'");

	}
}

function media_upload_flag_form($errors) {

	global $wpdb, $wp_query, $wp_locale, $type, $tab, $post_mime_types, $flag;
	
	media_upload_header();

	$post_id 	= intval($_REQUEST['post_id']);
	$galleryID 	= 0;
	$total 		= 1;
	$picarray 	= false;
	
	$form_action_url = site_url( "wp-admin/media-upload.php?type={$GLOBALS['type']}&tab=flag&post_id=$post_id", 'admin');

	// Get number of images in gallery	
	if ($_REQUEST['select_gal']){
		$galleryID = (int) $_REQUEST['select_gal'];
		$total = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->flagpictures WHERE galleryid = '$galleryID'");
	}
	
	// Build navigation
	$_GET['paged'] = intval($_GET['paged']);
	if ( $_GET['paged'] < 1 )
		$_GET['paged'] = 1;
	$start = ( $_GET['paged'] - 1 ) * 10;
	if ( $start < 1 )
		$start = 0;
		
	// Get the images
	if ( $galleryID != 0 )
		$picarray = $wpdb->get_col("SELECT pid FROM $wpdb->flagpictures WHERE galleryid = '$galleryID' AND exclude != 1 ORDER BY {$flag->options['galSort']} {$flag->options['galSortDir']} LIMIT $start, 10 ");	
		
?>

<form id="filter" action="" method="get">
<input type="hidden" name="type" value="<?php echo esc_attr( $GLOBALS['type'] ); ?>" />
<input type="hidden" name="tab" value="<?php echo esc_attr( $GLOBALS['tab'] ); ?>" />
<input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>" />

<div class="tablenav">
	<?php
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'total' => ceil($total / 10),
		'current' => $_GET['paged']
	));
	
	if ( $page_links )
		echo "<div class='tablenav-pages'>$page_links</div>";
	?>
	
	<div class="alignleft actions">
		<select id="select_gal" name="select_gal" style="width:200px;">;
			<option value="0" <?php selected('0', $galleryID); ?> ><?php esc_attr( _e('No gallery',"flag") ); ?></option>
			<?php
			// Show gallery selection
			$gallerylist = $wpdb->get_results("SELECT * FROM $wpdb->flaggallery ORDER BY gid ASC");
			if(is_array($gallerylist)) {
				foreach($gallerylist as $gallery) {
					$selected = ($gallery->gid == $galleryID )?	' selected="selected"' : "";
					echo '<option value="'.$gallery->gid.'"'.$selected.' >'.$gallery->title.'</option>'."\n";
				}
			}
			?>
		</select>
		<input type="submit" id="show-gallery" value="<?php esc_attr( _e('Select &#187;','flag') ); ?>" class="button-secondary" />
	</div>
	<br style="clear:both;" />
</div>
</form>

<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form" id="library-form">

	<?php wp_nonce_field('flag-media-form'); ?>

	<script type="text/javascript">
	<!--
	jQuery(function($){
		var preloaded = $(".media-item.preloaded");
		if ( preloaded.length > 0 ) {
			preloaded.each(function(){prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');});
			updateMediaForm();
		}
	});
	-->
	</script>
	
	<div id="media-items">
	<?php
	if(is_array($picarray)) {
		foreach ($picarray as $picid) {
			//TODO:Reduce SQL Queries
			$picture = flagdb::find_image($picid);
			?>
			<div id='media-item-<?php echo $picid; ?>' class='media-item preloaded'>
			  <div class='filename'></div>
			  <a class='toggle describe-toggle-on' href='#'><?php esc_attr( _e('Show', "flag") ); ?></a>
			  <a class='toggle describe-toggle-off' href='#'><?php esc_attr( _e('Hide', "flag") ); ?></a>
			  <div class='filename new'><?php echo ( empty($picture->alttext) ) ? wp_html_excerpt($picture->filename,60): stripslashes( wp_html_excerpt($picture->alttext,60) ); ?></div>
			  <table class='slidetoggle describe startclosed'><tbody>
				  <tr>
					<td rowspan='4'><img class='thumbnail' alt='<?php echo esc_attr( $picture->alttext ); ?>' src='<?php echo esc_attr( $picture->thumbURL ); ?>'/></td>
					<td><?php esc_attr( _e('Image ID:', "flag") ); ?><?php echo $picid; ?></td>
				  </tr>
				  <tr><td><?php echo esc_attr( $picture->filename ); ?></td></tr>
				  <tr><td><?php echo esc_attr( stripslashes($picture->alttext) ); ?></td></tr>
				  <tr><td>&nbsp;</td></tr>
				  <tr>
					<td class="label"><label for="image[<?php echo $picid; ?>][alttext]"><?php esc_attr( _e('Alt/Title text', "flag") ); ?></label></td>
					<td class="field"><input id="image[<?php echo $picid; ?>][alttext]" name="image[<?php echo $picid; ?>][alttext]" value="<?php echo esc_attr( stripslashes($picture->alttext) ); ?>" type="text"/></td>
				  </tr>	
				  <tr>
					<td class="label"><label for="image[<?php echo $picid; ?>][description]"><?php esc_attr( _e("Description","flag") ); ?></label></td>
						<td class="field"><textarea name="image[<?php echo $picid; ?>][description]" id="image[<?php echo $picid; ?>][description]"><?php echo esc_attr( stripslashes($picture->description) ); ?></textarea></td>
				  </tr>
					<tr class="align">
						<td class="label"><label for="image[<?php echo $picid; ?>][align]"><?php esc_attr( _e("Alignment") ); ?></label></td>
						<td class="field">
							<input name="image[<?php echo $picid; ?>][align]" id="image-align-none-<?php echo $picid; ?>" checked="checked" value="none" type="radio" />
							<label for="image-align-none-<?php echo $picid; ?>" class="align image-align-none-label"><?php esc_attr( _e("None") ); ?></label>
							<input name="image[<?php echo $picid; ?>][align]" id="image-align-left-<?php echo $picid; ?>" value="left" type="radio" />
							<label for="image-align-left-<?php echo $picid; ?>" class="align image-align-left-label"><?php esc_attr(  _e("Left") ); ?></label>
							<input name="image[<?php echo $picid; ?>][align]" id="image-align-center-<?php echo $picid; ?>" value="center" type="radio" />
							<label for="image-align-center-<?php echo $picid; ?>" class="align image-align-center-label"><?php esc_attr( _e("Center") ); ?></label>
							<input name="image[<?php echo $picid; ?>][align]" id="image-align-right-<?php echo $picid; ?>" value="right" type="radio" />
							<label for="image-align-right-<?php echo $picid; ?>" class="align image-align-right-label"><?php esc_attr( _e("Right") ); ?></label>
						</td>
					</tr>
					<tr class="image-size">
						<th class="label"><label for="image[<?php echo $picid; ?>][size]"><span class="alignleft"><?php esc_attr( _e("Size") ); ?></span></label>
						</th>
						<td class="field">
							<input name="image[<?php echo $picid; ?>][size]" id="image-size-thumb-<?php echo $picid; ?>" type="radio" checked="checked" value="thumbnail" />
							<label for="image-size-thumb-<?php echo $picid; ?>"><?php esc_attr( _e("Thumbnail") ); ?></label>
							<input name="image[<?php echo $picid; ?>][size]" id="image-size-full-<?php echo $picid; ?>" type="radio" value="full" />
							<label for="image-size-full-<?php echo $picid; ?>"><?php esc_attr( _e("Full size") ); ?></label>
						</td>
					</tr>
				   <tr class="submit">
						<td>
							<input type="hidden"  name="image[<?php echo $picid; ?>][thumb]" value="<?php echo $picture->thumbURL; ?>" />
							<input type="hidden"  name="image[<?php echo $picid; ?>][url]" value="<?php echo $picture->imageURL; ?>" />
						</td>
						<td class="savesend"><button type="submit" class="button" value="1" name="send[<?php echo $picid; ?>]"><?php echo esc_attr( __('Insert into Post') ); ?></button></td>
				   </tr>
			  </tbody></table>
			</div>
		<?php		  
		}
	}
	?>
	</div>
	<p class="ml-submit">
		<input type="submit" class="button savebutton" name="save" value="<?php esc_attr( _e('Save all changes','flag') ); ?>" />
	</p>
	<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
	<input type="hidden" name="select_gal" id="select_gal" value="<?php echo (int) $galleryID; ?>" />
</form>

<?php
}
?>