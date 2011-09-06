<?php
/*
Skin Name: PhotoGallery PRO DEMO
Skin URI: 
Description: PhotoGallery PRO DEMO skin for Flash Album Gallery. <br /><br />Details: bottom hidden thumbnails, 'fullscreen' button, 'slideshhow' button, image description slide-hover button (i), category slide-hover button (+) 
Author: PGC
Author URI: http://PhotoGalleryCreator.com
Version: 1.2
*/

function flag_skin_options() { 
	require_once( str_replace("\\","/", dirname(__FILE__).'/settings.php') ); 
	$file_settings = str_replace("\\","/", dirname(dirname(__FILE__)).'/'.basename( dirname(__FILE__) ).'_settings.php');
	if ( file_exists( $file_settings ) ) {
		include_once( $file_settings ); 
	}
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#colors .colorPick').each( function(){
		var inpID = jQuery(this).attr('name');
		jQuery('#cp_'+inpID).farbtastic('#'+inpID);
		jQuery('#'+inpID).focus( function(){
		    jQuery('#cp_'+inpID).show();
		});
		jQuery('#'+inpID).blur( function(){
		    jQuery('#cp_'+inpID).hide();
		});
	});
	function tChecked() {
	if( jQuery('#flashBacktransparent').attr('checked') ) {
	   var dclone=jQuery('#flashBackcolor').clone();
	   jQuery('#flashBackcolor').hide();
		dclone.removeAttr('style').removeAttr('id').removeAttr('name').addClass('flashBackcolor').attr('disabled','disabled').insertAfter('#flashBackcolor');
	 } else {
	   jQuery('.flashBackcolor').remove();
	   jQuery('#flashBackcolor').show();
	 }
	}
	tChecked();
	jQuery("#flashBacktransparent").click(tChecked);
	jQuery("#skin_default").click(function(){
		jQuery("#flashBacktransparent").removeAttr('checked');
		jQuery("#flashBackcolor")			.val('262626');
		jQuery("#buttonsBG")				.val('000000');
		jQuery("#buttonsMouseOver")			.val('7485c2');
		jQuery("#buttonsMouseOut")			.val('717171');
		jQuery("#catButtonsMouseOver")		.val('000000');
		jQuery("#catButtonsMouseOut")		.val('000000');
		jQuery("#catButtonsTextMouseOver")	.val('7485c2');
		jQuery("#catButtonsTextMouseOut")	.val('bcbcbc');
		jQuery("#thumbMouseOver")			.val('7485c2');
		jQuery("#thumbMouseOut")			.val('000000');
		jQuery("#mainTitle")				.val('ffffff');
		jQuery("#categoryTitle")			.val('7485c2');
		jQuery("#itemBG")					.val('ffc105');
		jQuery("#itemTitle")				.val('7485c2');
		jQuery("#itemDescription")			.val('e0e0e0');
		tChecked();
		return false;
	});
});
</script>
	<form method="POST"><div>
		<?php wp_nonce_field('skin_settings'); ?>
		<input type="hidden" name="skin_options" value="flashBackcolor,buttonsBG,flashBacktransparent,buttonsMouseOver,buttonsMouseOut,catButtonsMouseOver,catButtonsMouseOut,catButtonsTextMouseOver,catButtonsTextMouseOut,thumbMouseOver,thumbMouseOut,mainTitle,categoryTitle,itemBG,itemTitle,itemDescription" />

	<!-- Color settings -->
		<h3><?php _e('Color Settings','flag'); ?> <small style="margin-left:20px;"><a href="#" id="skin_default">(<?php _e('set default settings','flag'); ?>)</a></small></h3>
		<table id="colors" class="form-table flag-options">
			<tr>
				<th style="width: 30%;"><?php _e('Background Color','flag'); ?>:</th>
				<td><input class="colorPick" type="text" size="7" maxlength="6" id="flashBackcolor" name="flashBackcolor" value="<?php echo $flashBackcolor?>" /><div id="cp_flashBackcolor" style="background:#F9F9F9;position:absolute;display:none;"></div> <label><input type="checkbox" id="flashBacktransparent" name="flashBacktransparent" value="transparent" <?php checked('transparent', $flashBacktransparent); ?> /> transparent</label></td>
			</tr>
			<tr>					
				<th><?php _e('Buttons Background Color','flag'); ?>:</th>
				<td><input class="colorPick" type="text" size="7" maxlength="6" id="buttonsBG" name="buttonsBG" value="<?php echo $buttonsBG; ?>" /><div id="cp_buttonsBG" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
			</tr>
			<tr>					
				<th><?php _e('Buttons Text Color','flag'); ?>:</th>
				<td>
					<input class="colorPick" type="text" size="7" maxlength="6" id="buttonsMouseOver" name="buttonsMouseOver" value="<?php echo $buttonsMouseOver; ?>" /> mouseOver<br />
					<div id="cp_buttonsMouseOver" style="background:#F9F9F9;position:absolute;display:none;"></div>
					<input class="colorPick" type="text" size="7" maxlength="6" id="buttonsMouseOut" name="buttonsMouseOut" value="<?php echo $buttonsMouseOut; ?>" /> mouseOut<br />
					<div id="cp_buttonsMouseOut" style="background:#F9F9F9;position:absolute;display:none;"></div>
				</td>
			</tr>
			<tr>					
				<th><?php _e('Category Buttons Color','flag'); ?>:</th>
				<td>
					<input class="colorPick" type="text" size="7" maxlength="6" id="catButtonsMouseOver" name="catButtonsMouseOver" value="<?php echo $catButtonsMouseOver; ?>" /> mouseOver<br />
					<div id="cp_catButtonsMouseOver" style="background:#F9F9F9;position:absolute;display:none;"></div>
					<input class="colorPick" type="text" size="7" maxlength="6" id="catButtonsMouseOut" name="catButtonsMouseOut" value="<?php echo $catButtonsMouseOut; ?>" /> mouseOut<br />
					<div id="cp_catButtonsMouseOut" style="background:#F9F9F9;position:absolute;display:none;"></div>
				</td>
			</tr>
			<tr>					
				<th><?php _e('Category Buttons Text Color','flag'); ?>:</th>
				<td>
					<input class="colorPick" type="text" size="7" maxlength="6" id="catButtonsTextMouseOver" name="catButtonsTextMouseOver" value="<?php echo $catButtonsTextMouseOver; ?>" /> mouseOver<br />
					<div id="cp_catButtonsTextMouseOver" style="background:#F9F9F9;position:absolute;display:none;"></div>
					<input class="colorPick" type="text" size="7" maxlength="6" id="catButtonsTextMouseOut" name="catButtonsTextMouseOut" value="<?php echo $catButtonsTextMouseOut; ?>" /> mouseOut<br />
					<div id="cp_catButtonsTextMouseOut" style="background:#F9F9F9;position:absolute;display:none;"></div>
				</td>
			</tr>
			<tr>					
				<th><?php _e('Thumbs Rollover Color','flag'); ?>:</th>
				<td>
					<input class="colorPick" type="text" size="7" maxlength="6" id="thumbMouseOver" name="thumbMouseOver" value="<?php echo $thumbMouseOver; ?>" /> mouseOver<br />
					<div id="cp_thumbMouseOver" style="background:#F9F9F9;position:absolute;display:none;"></div>
					<input class="colorPick" type="text" size="7" maxlength="6" id="thumbMouseOut" name="thumbMouseOut" value="<?php echo $thumbMouseOut; ?>" /> mouseOut<br />
					<div id="cp_thumbMouseOut" style="background:#F9F9F9;position:absolute;display:none;"></div>
				</td>
			</tr>
			<tr>					
				<th><?php _e('Main Title','flag'); ?>:</th>
				<td><input class="colorPick" type="text" size="7" maxlength="6" id="mainTitle" name="mainTitle" value="<?php echo $mainTitle; ?>" /><div id="cp_mainTitle" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
			</tr>
			<tr>					
				<th><?php _e('Category Title','flag'); ?>:</th>
				<td><input class="colorPick" type="text" size="7" maxlength="6" id="categoryTitle" name="categoryTitle" value="<?php echo $categoryTitle; ?>" /><div id="cp_categoryTitle" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
			</tr>
			<tr>					
				<th><?php _e('Item Background','flag'); ?>:</th>
				<td><input class="colorPick" type="text" size="7" maxlength="6" id="itemBG" name="itemBG" value="<?php echo $itemBG; ?>" /><div id="cp_itemBG" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
			</tr>
			<tr>					
				<th><?php _e('Item Title','flag'); ?>:</th>
				<td><input class="colorPick" type="text" size="7" maxlength="6" id="itemTitle" name="itemTitle" value="<?php echo $itemTitle; ?>" /><div id="cp_itemTitle" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
			</tr>
			<tr>					
				<th><?php _e('Item Description','flag'); ?>:</th>
				<td><input class="colorPick" type="text" size="7" maxlength="6" id="itemDescription" name="itemDescription" value="<?php echo $itemDescription; ?>" /><div id="cp_itemDescription" style="background:#F9F9F9;position:absolute;display:none;"></div></td>
			</tr>
		</table>

		<div class="clear"> &nbsp; </div>
		<div class="submit"><input class="button-primary" type="submit" name="updateskinoption" value="<?php _e('Save Changes', 'flag'); ?>"/></div>
	</div></form>
<?php } ?>
