<?php  

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {	die('You are not allowed to call this page directly.');}

function flag_picturelist() {
// *** show picture list
	global $wpdb, $flagdb, $user_ID, $flag;
	
	// Look if its a search result
	$is_search = isset ($_GET['s']) ? true : false;
	
	if ($is_search) {

		// fetch the imagelist 
		$picturelist = $flag->manage_page->search_result;
		
		// we didn't set a gallery or a pagination
		$act_gid     = 0;
		$_GET['paged'] = 1;
		$page_links = false;
		
	} else {
		
		// GET variables
		$act_gid    = $flag->manage_page->gid;
		
		// Load the gallery metadata
		$gallery = $flagdb->find_gallery($act_gid);

		if (!$gallery) {
			flagGallery::show_error(__('Gallery not found.', 'flag'));
			return;
		}
		
		// Check if you have the correct capability
		if (!flagAdmin::can_manage_this_gallery($gallery->author)) {
			flagGallery::show_error(__('Sorry, you have no access here', 'flag'));
			return;
		}	
		
		// look for pagination	
		if ( ! isset( $_GET['paged'] ) || $_GET['paged'] < 1 )
			$_GET['paged'] = 1;
		
		$start = ( $_GET['paged'] - 1 ) * 50;
		
		// get picture values
		$picturelist = $flagdb->get_gallery($act_gid, $flag->options['galSort'], $flag->options['galSortDir'], false, 50, $start );
		
		// build pagination
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $flagdb->paged['max_objects_per_page'],
			'current' => $_GET['paged']
		));
		
		// get the current author
		$act_author_user    = get_userdata( (int) $gallery->author );

	}	
		
	// list all galleries
	$gallerylist = $flagdb->find_all_galleries();
	
	//get the columns
	$gallery_columns = flag_manage_gallery_columns();
	$hidden_columns  = get_hidden_columns('flag-manage-images');
	$num_columns     = count($gallery_columns) - count($hidden_columns);	
?>
<!--[if lt IE 8]>
	<style type="text/css">
		.custom_thumb {
			display : none;
		}
	</style>
<![endif]-->

<script type="text/javascript"> 
//<![CDATA[
function showDialog( windowId, height ) {
	var form = document.getElementById('updategallery');
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
	jQuery("#" + windowId + "_imagelist").val(elementlist);
	// console.log (jQuery("#TB_imagelist").val());
	tb_show("", "#TB_inline?width=640&height=" + height + "&inlineId=" + windowId + "&modal=true", false);
}

function checkAll(form)
{
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

	var numchecked = getNumChecked(document.getElementById('updategallery'));
	 
	if(numchecked < 1) { 
		alert('<?php echo js_escape(__("No images selected", "flag")); ?>');
		return false; 
	} 
	
	actionId = jQuery('#bulkaction').val();
	
	switch (actionId) {
		case "copy_to":
		case "move_to":
			showDialog('selectgallery', 120);
			return false;
			break;
		case "resize_images":
			showDialog('resize_images', 120);
			return false;
			break;
		case "new_thumbnail":
			showDialog('new_thumbnail', 160);
			return false;
			break;			
	}
	
	return confirm('<?php echo sprintf(js_escape(__("You are about to start the bulk edit for %s images \n \n 'Cancel' to stop, 'OK' to proceed.",'flag')), "' + numchecked + '") ; ?>');
}

jQuery(document).ready( function() {
	// close postboxes that should be closed
	jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
	postboxes.add_postbox_toggles('flag-manage-gallery'); 

});
//]]>
</script>

<div class="wrap">

<?php if ($is_search) :?>
<h2><?php printf( __('Search results for &#8220;%s&#8221;', 'flag'), wp_specialchars( get_search_query() ) ); ?></h2>
<form class="search-form" action="" method="get">
<p class="search-box">
	<label class="hidden" for="media-search-input"><?php _e( 'Search Images', 'flag' ); ?>:</label>
	<input type="hidden" id="page-name" name="page" value="flag-manage-gallery" />
	<input type="text" id="media-search-input" name="s" value="<?php the_search_query(); ?>" />
	<input type="submit" value="<?php _e( 'Search Images', 'flag' ); ?>" class="button" />
</p>
</form>

<br style="clear: both;" />

<form id="updategallery" class="flagform" method="POST" action="<?php echo $flag->manage_page->base_page . '&amp;mode=edit&amp;s=' . $_GET['s']; ?>" accept-charset="utf-8">
<?php wp_nonce_field('flag_updategallery'); ?>
<input type="hidden" name="page" value="manage-images" />

<?php else :?>

<h2><?php echo _n( 'Gallery', 'Galleries', 1, 'flag' ); ?> : <?php echo flagGallery::i18n($gallery->title); ?></h2>
<select name="select_gid" style="width:180px; float: right; margin: -20px 3px 0 0;" onchange="window.location.href=this.options[this.selectedIndex].value">
	<option selected="selected"><?php _e('Choose another gallery', 'flag'); ?></option>
<?php 
	foreach ($gallerylist as $gal) { 
		if ($gal->gid != $act_gid) { 
?>
	<option value="<?php echo wp_nonce_url( $flag->manage_page->base_page . "&amp;mode=edit&amp;gid=" . $gal->gid, 'flag_editgallery')?>" ><?php echo $gal->gid; ?> - <?php echo attribute_escape(stripslashes($gal->title)); ?></option>
<?php 
		} 
	}
?>
</select>

<form id="updategallery" class="flagform" method="POST" action="<?php echo $flag->manage_page->base_page . '&amp;mode=edit&amp;gid=' . $act_gid . '&amp;paged=' . $_GET['paged']; ?>" accept-charset="utf-8">
<?php wp_nonce_field('flag_updategallery'); ?>
<input type="hidden" name="page" value="manage-images" />

<div id="poststuff" class="metabox-holder">
	<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
<div id="post-body"><div id="post-body-content"><div id="normal-sortables" class="meta-box-sortables ui-sortable" style="position: relative;">
	<div id="flagalleryset" class="postbox <?php echo postbox_classes('flagalleryset', 'flag-manage-gallery'); ?>" >
		<div class="handlediv" title="Click to toggle"><br/></div>
		<h3 class="hndle"><span><?php _e('Gallery settings', 'flag'); ?></span></h3>
		<div class="inside">
			<table class="form-table" >
				<tr>
					<th align="left" scope="row"><?php _e('Title', 'flag'); ?>:</th>
					<td align="left"><input type="text" size="50" name="title" value="<?php echo $gallery->title; ?>"  /></td>
				</tr>
				<tr>
					<th align="left" scope="row"><?php _e('Description', 'flag'); ?>:</th> 
					<td align="left"><textarea name="gallerydesc" cols="30" rows="3" style="width: 95%" ><?php echo $gallery->galdesc; ?></textarea></td>
				</tr>
				<tr>
					<th align="left" scope="row"><?php _e('Path', 'flag'); ?>:</th> 
					<td align="left"><input <?php if (IS_WPMU) echo 'readonly = "readonly"'; ?> type="text" size="50" name="path" value="<?php echo $gallery->path; ?>"  /></td>
				</tr>
				<tr>
					<th align="right" scope="row"><?php _e('Author', 'flag'); ?>:</th>
					<td align="left"> 
					<?php
						$editable_ids = $flag->manage_page->get_editable_user_ids( $user_ID );
						if ( $editable_ids && count( $editable_ids ) > 1 )
							wp_dropdown_users( array('include' => $editable_ids, 'name' => 'author', 'selected' => empty( $gallery->author ) ? 0 : $gallery->author ) ); 
						else
							echo $act_author_user->display_name;
					?>
						<input type="hidden" name="previewpic" value="<?php if(is_array($picturelist)) echo $picturelist[0]->pid; ?>" />
					</td>
				</tr>
			</table>
			
			<div class="submit">
				<input type="submit" class="button-secondary" name="scanfolder" value="<?php _e("Scan Folder for new images",'flag')?> " />
				<input type="submit" class="button-primary action" name="updatepictures" value="<?php _e("Save Changes",'flag')?>" />
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div></div></div>
</div> <!-- poststuff -->
<?php endif; ?>

<div class="tablenav flag-tablenav">
	<?php if ( $page_links ) : ?>
	<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
		number_format_i18n( ( $_GET['paged'] - 1 ) * $flagdb->paged['objects_per_page'] + 1 ),
		number_format_i18n( min( $_GET['paged'] * $flagdb->paged['objects_per_page'], $flagdb->paged['total_objects'] ) ),
		number_format_i18n( $flagdb->paged['total_objects'] ),
		$page_links
	); echo $page_links_text; ?></div>
	<?php endif; ?>
	<div class="alignleft actions">
	<select id="bulkaction" name="bulkaction" class="alignleft">
		<option value="no_action" ><?php _e("No action",'flag')?></option>
		<option value="new_thumbnail" ><?php _e("Create new thumbnails",'flag')?></option>
		<option value="resize_images" ><?php _e("Resize images",'flag')?></option>
		<option value="delete_images" ><?php _e("Delete images",'flag')?></option>
		<option value="import_meta" ><?php _e("Import metadata",'flag')?></option>
		<option value="copy_meta" ><?php _e("Metadata to description",'flag')?></option>
		<option value="copy_to" ><?php _e("Copy to...",'flag')?></option>
		<option value="move_to"><?php _e("Move to...",'flag')?></option>
	</select>
	<input class="button-secondary alignleft" style="margin-right:10px;" type="submit" name="showThickbox" value="<?php _e("OK",'flag')?>" onclick="if ( !checkSelected() ) return false;" />
	
<?php if (($flag->options['galSort'] == "sortorder") && (!$is_search) ) { ?>
	<a href="<?php echo wp_nonce_url( $flag->manage_page->base_page . "&amp;mode=sort&amp;gid=" . $act_gid, 'flag_sortgallery')?>" class="button-secondary alignleft" style="margin:1px 10px 0 0;"><?php _e("Sort gallery",'flag')?></a>
<?php }
	 if(current_user_can('FlAG Upload images') && (!$is_search)){ ?>
	<a href="<?php echo wp_nonce_url( $flag->manage_page->base_page . "&amp;gid=" . $act_gid . "&amp;tabs=1", 'flag_addimages')?>" class="button-secondary alignleft" style="margin:1px 10px 0 0;"><?php _e("Add Images",'flag')?></a>
<?php } ?>
	<input type="submit" name="updatepictures" class="button-primary action alignleft"  value="<?php _e("Save Changes",'flag')?>" />
	</div>
</div>

<table id="flag-listimages" class="widefat fixed" cellspacing="0" >

	<thead>
	<tr>
<?php print_column_headers('flag-manage-images'); ?>
	</tr>
	</thead>
	<tfoot>
	<tr>
<?php print_column_headers('flag-manage-images', false); ?>
	</tr>
	</tfoot>
	<tbody>
<?php
if($picturelist) {
	
	$thumbsize = '';
	$counter	= 0;

	if ($flag->options['thumbFix']) {
		$thumbsize = 'width="'.$flag->options['thumbWidth'].'" height="'.$flag->options['thumbHeight'].'"';
	}
	
	foreach($picturelist as $picture) {

		//for search result we need to check the capatibiliy
		if ( !flagAdmin::can_manage_this_gallery($picture->author) && $is_search )
			continue;
			
		$counter++;
		$pid       = (int) $picture->pid;
		$alternate = ( !isset($alternate) || $alternate == 'alternate' ) ? '' : 'alternate';	
		$exclude   = ( $picture->exclude ) ? 'checked="checked"' : '';
		$date = mysql2date(get_option('date_format'), $picture->imagedate);
		$time = mysql2date(get_option('time_format'), $picture->imagedate);

		?>
		<tr id="picture-<?php echo $pid; ?>" class="<?php echo $alternate; ?> iedit"  valign="top">
			<?php
			foreach($gallery_columns as $gallery_column_key => $column_display_name) {
				$class = "class=\"$gallery_column_key column-$gallery_column_key\"";
		
				$style = '';
				if ( in_array($gallery_column_key, $hidden_columns) )
					$style = ' style="display:none;"';
		
				$attributes = "$class$style";
				
				switch ($gallery_column_key) {
					case 'cb' :
						?> 
						<th <?php echo $attributes; ?> scope="row"><input name="doaction[]" type="checkbox" value="<?php echo $pid; ?>" /></th>
						<?php
					break;
					case 'id' :
						?>
						<td <?php echo $attributes; ?>><?php echo $pid; ?>
							<input type="hidden" name="pid[]" value="<?php echo $pid; ?>" />
						</td>
						<?php						
					break;
					case 'thumbnail' :
						?>
						<td <?php echo $attributes; ?>><a href="<?php echo $picture->imageURL; ?>" class="thickbox" title="<?php echo $picture->filename; ?>">
								<img class="thumb" src="<?php echo $picture->thumbURL; ?>" <?php echo $thumbsize; ?> id="thumb-<?php echo $pid; ?>" />
							</a>
						</td>
						<?php
					break;
					case 'filename' :
						?>
						<td <?php echo $attributes; ?>>
							<strong><a href="<?php echo $picture->imageURL; ?>" class="thickbox" title="<?php echo $picture->filename; ?>">
								<?php echo $picture->filename; ?>
							</a></strong>
							<br /><?php echo $date; ?>
							<?php if ( !empty($picture->meta_data['width']) ) { 
								echo '<br />'.__('Size: ', 'flag').$picture->meta_data['width'].'x'.$picture->meta_data['height'].' '.__('pixel', 'flag');
							} else {
								$imgpath = WINABSPATH.$picture->path."/".$picture->filename; 
								$img = @getimagesize($imgpath); 
								if($img) echo '<br />'.__('Size: ', 'flag').$img[0].'x'.$img[1].' '.__('pixel', 'flag'); 
							} ?>
							<p>
							<?php
							$actions = array();
							$actions['view']   = '<a class="thickbox" href="' . $picture->imageURL . '" title="' . attribute_escape(sprintf(__('View "%s"'), $picture->filename)) . '">' . __('View', 'flag') . '</a>';
							$actions['meta']   = '<a class="thickbox" href="' . FLAG_URLPATH . 'admin/showmeta.php?id=' . $pid . '" title="' . __('Show Meta data','flag') . '">' . __('Meta', 'flag') . '</a>';
							$actions['custom_thumb']   = '<a class="thickbox" href="' . FLAG_URLPATH . 'admin/manage_thumbnail.php?id=' . $pid . '" title="' . __('Customize thumbnail','flag') . '">' . __('Edit thumb', 'flag') . '</a>';
							$actions['delete'] = '<a class="submitdelete" href="' . wp_nonce_url("admin.php?page=flag-manage-gallery&amp;mode=delpic&amp;gid=".$act_gid."&amp;pid=".$pid, 'flag_delpicture'). '" class="delete column-delete" onclick="javascript:check=confirm( \'' . attribute_escape(sprintf(__('Delete "%s"' , 'flag'), $picture->filename)). '\');if(check==false) return false;">' . __('Delete','flag') . '</a>';
							$action_count = count($actions);
							$i = 0;
							echo '<div class="row-actions">';
							foreach ( $actions as $action => $link ) {
								++$i;
								( $i == $action_count ) ? $sep = '' : $sep = ' | ';
								echo "<span class='$action'>$link$sep</span>";
							}
							echo '</div>';
							?></p>
						</td>
						<?php						
					break;
					case 'alt_title_desc' :
						?>
						<td <?php echo $attributes; ?>>
							<input name="alttext[<?php echo $pid; ?>]" type="text" style="width:95%; margin-bottom: 2px;" value="<?php echo stripslashes($picture->alttext); ?>" /><br/>
							<textarea name="description[<?php echo $pid; ?>]" style="width:95%; margin-top: 2px;" rows="2" ><?php echo stripslashes($picture->description); ?></textarea>
						</td>
						<?php						
					break;
					case 'exclude' :
						?>
						<td <?php echo $attributes; ?>><input name="exclude[<?php echo $pid; ?>]" type="checkbox" value="1" <?php echo $exclude; ?> /></td>
						<?php						
					break;
					/* 
                    case 'views' :
						?>
						<td <?php echo $attributes; ?>><?php echo $picture->hitcounter; ?></td>
						<?php						
					break;
                    */
					default : 
						?>
						<td <?php echo $attributes; ?>><?php do_action('flag_manage_gallery_custom_column', $gallery_column_key, $pid); ?></td>
						<?php
					break;
				}
			?>
			<?php } ?>
		</tr>
		<?php
	}
}
 
// In the case you have no capaptibility to see the search result
if ( $counter==0 )
	echo '<tr><td colspan="' . $num_columns . '" align="center"><strong>'.__('No entries found','flag').'</strong></td></tr>';

?>
	
		</tbody>
	</table>
	<p class="submit"><input type="submit" class="button-primary action" name="updatepictures" value="<?php _e("Save Changes",'flag')?>" /></p>
	</form>	
	<br class="clear"/>
	</div><!-- /#wrap -->

	<!-- #selectgallery -->
	<div id="selectgallery" style="display: none;" >
		<form id="form-select-gallery" method="POST" accept-charset="utf-8">
		<?php wp_nonce_field('flag_thickbox_form'); ?>
		<input type="hidden" id="selectgallery_imagelist" name="TB_imagelist" value="" />
		<input type="hidden" id="selectgallery_bulkaction" name="TB_bulkaction" value="" />
		<input type="hidden" name="page" value="manage-images" />
		<table width="100%" border="0" cellspacing="3" cellpadding="3" >
		  	<tr>
		    	<th>
		    		<?php _e('Select the destination gallery:', 'flag'); ?>&nbsp;
		    		<select name="dest_gid" style="width:90%" >
		    			<?php 
		    				foreach ($gallerylist as $gallery) { 
		    					if ($gallery->gid != $act_gid) { 
		    			?>
						<option value="<?php echo $gallery->gid; ?>" ><?php echo $gallery->gid; ?> - <?php echo stripslashes($gallery->title); ?></option>
						<?php 
		    					} 
		    				}
		    			?>
		    		</select>
		    	</th>
		  	</tr>
		  	<tr align="right">
		    	<td class="submit">
		    		<input type="submit" class="button-primary" name="TB_SelectGallery" value="<?php _e("OK",'flag')?>" />
		    		&nbsp;
		    		<input class="button-secondary" type="reset" value="<?php _e("Cancel",'flag')?>" onclick="tb_remove()"/>
		    	</td>
			</tr>
		</table>
		</form>
	</div>
	<!-- /#selectgallery -->

	<!-- #resize_images -->
	<div id="resize_images" style="display: none;" >
		<form id="form-resize-images" method="POST" accept-charset="utf-8">
		<?php wp_nonce_field('flag_thickbox_form'); ?>
		<input type="hidden" id="resize_images_imagelist" name="TB_imagelist" value="" />
		<input type="hidden" id="resize_images_bulkaction" name="TB_bulkaction" value="" />
		<input type="hidden" name="page" value="manage-images" />
		<table width="100%" border="0" cellspacing="3" cellpadding="3" >
			<tr valign="top">
				<td>
					<strong><?php _e('Resize Images to', 'flag'); ?>:</strong> 
				</td>
				<td>
					<input type="text" size="5" name="imgWidth" value="<?php echo $flag->options['imgWidth']; ?>" /> x <input type="text" size="5" name="imgHeight" value="<?php echo $flag->options['imgHeight']; ?>" />
					<br /><small><?php _e('Width x height (in pixel). GRAND FlAGallery will keep ratio size','flag'); ?></small>
				</td>
			</tr>
		  	<tr align="right">
		    	<td colspan="2" class="submit">
		    		<input class="button-primary" type="submit" name="TB_ResizeImages" value="<?php _e('OK', 'flag'); ?>" />
		    		&nbsp;
		    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel', 'flag'); ?>&nbsp;" onclick="tb_remove()"/>
		    	</td>
			</tr>
		</table>
		</form>
	</div>
	<!-- /#resize_images -->

	<!-- #new_thumbnail -->
	<div id="new_thumbnail" style="display: none;" >
		<form id="form-new-thumbnail" method="POST" accept-charset="utf-8">
		<?php wp_nonce_field('flag_thickbox_form'); ?>
		<input type="hidden" id="new_thumbnail_imagelist" name="TB_imagelist" value="" />
		<input type="hidden" id="new_thumbnail_bulkaction" name="TB_bulkaction" value="" />
		<input type="hidden" name="page" value="manage-images" />
		<table width="100%" border="0" cellspacing="3" cellpadding="3" >
			<tr valign="top">
				<th align="left"><?php _e('Width x height (in pixel)','flag'); ?></th>
				<td><input type="text" size="5" maxlength="5" name="thumbWidth" value="<?php echo $flag->options['thumbWidth']; ?>" /> x <input type="text" size="5" maxlength="5" name="thumbHeight" value="<?php echo $flag->options['thumbHeight']; ?>" />
				<br /><small><?php _e('These values are maximum values ','flag'); ?></small></td>
			</tr>
			<tr valign="top">
				<th align="left"><?php _e('Set fix dimension','flag'); ?></th>
				<td><input type="checkbox" name="thumbFix" value="1" <?php checked('1', $flag->options['thumbFix']); ?> />
				<br /><small><?php _e('Ignore the aspect ratio, no portrait thumbnails','flag'); ?></small></td>
			</tr>
		  	<tr align="right">
		    	<td colspan="2" class="submit">
		    		<input class="button-primary" type="submit" name="TB_NewThumbnail" value="<?php _e('OK', 'flag'); ?>" />
		    		&nbsp;
		    		<input class="button-secondary" type="reset" value="&nbsp;<?php _e('Cancel', 'flag'); ?>&nbsp;" onclick="tb_remove()"/>
		    	</td>
			</tr>
		</table>
		</form>
	</div>
	<!-- /#new_thumbnail -->	

	<script type="text/javascript">
	/* <![CDATA[ */
	jQuery(document).ready(function(){columns.init('flag-manage-images');});	
	/* ]]> */
	</script>
	<?php
}

// define the columns to display, the syntax is 'internal name' => 'display name'
function flag_manage_gallery_columns() {
	global $flag;
	
	$gallery_columns = array();
	
	$gallery_columns['cb'] = '<input name="checkall" type="checkbox" onclick="checkAll(document.getElementById(\'updategallery\'));" />';
	$gallery_columns['id'] = __('ID');
	$gallery_columns['thumbnail'] = __('Thumbnail', 'flag');
	$gallery_columns['filename'] = __('Filename', 'flag');
	$gallery_columns['alt_title_desc'] = __('Alt &amp; Title Text', 'flag') . ' / ' . __('Description', 'flag');
	$gallery_columns['exclude'] = '<img src="'.FLAG_URLPATH.'admin/images/lock.png" alt="member view" title="'.__('Only for logged in users', 'flag').'" />';
	//$gallery_columns['views'] = '<img src="'.FLAG_URLPATH.'admin/images/hits.png" alt="total views" title="'.__('Views', 'flag').'" />';
	
	$gallery_columns = apply_filters('flag_manage_gallery_columns', $gallery_columns);

	return $gallery_columns;
}

?>
