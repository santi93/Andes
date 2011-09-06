<?php function flag_skin_options() { 
$flag_options = get_option('flag_options');
$flashBacktransparent = $flag_options['flashBacktransparent'];
$flashBackcolor = str_replace('#','',$flag_options['flashBackcolor']);
$buttonsBG = str_replace('#','',$flag_options['buttonsBG']);
$buttonsMouseOver = str_replace('#','',$flag_options['buttonsMouseOver']);
$buttonsMouseOut = str_replace('#','',$flag_options['buttonsMouseOut']);
$catButtonsMouseOver = str_replace('#','',$flag_options['catButtonsMouseOver']);
$catButtonsMouseOut = str_replace('#','',$flag_options['catButtonsMouseOut']);
$catButtonsTextMouseOver = str_replace('#','',$flag_options['catButtonsTextMouseOver']);
$catButtonsTextMouseOut = str_replace('#','',$flag_options['catButtonsTextMouseOut']);
$thumbMouseOver = str_replace('#','',$flag_options['thumbMouseOver']);
$thumbMouseOut = str_replace('#','',$flag_options['thumbMouseOut']);
$mainTitle = str_replace('#','',$flag_options['mainTitle']);
$categoryTitle = str_replace('#','',$flag_options['categoryTitle']);
$itemBG = str_replace('#','',$flag_options['itemBG']);
$itemTitle = str_replace('#','',$flag_options['itemTitle']);
$itemDescription = str_replace('#','',$flag_options['itemDescription']);
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
});
</script>
	<form method="POST"><div>
		<?php wp_nonce_field('flag_settings'); ?>
		<input type="hidden" name="page_options" value="flashBackcolor,buttonsBG,flashBacktransparent,buttonsMouseOver,buttonsMouseOut,catButtonsMouseOver,catButtonsMouseOut,catButtonsTextMouseOver,catButtonsTextMouseOut,thumbMouseOver,thumbMouseOut,mainTitle,categoryTitle,itemBG,itemTitle,itemDescription" />

	<!-- Color settings -->
		<h3><?php _e('Color Settings','flag'); ?></h3>
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
		<div class="submit"><input class="button-primary" type="submit" name="updateoption" value="<?php _e('Save Changes', 'flag'); ?>"/></div>
	</div></form>
<?php } ?>
