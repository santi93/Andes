<?php
/*
Skin Name: Midnight.
Skin URI: 
Description: Midnight skin for Flash Album Gallery. <br />You can use any language you want for album name, alt/title and description in FlAGallery  ;)<br /><br />Details: system font for image description, bottom thumbnails, 'Fullscreen' button, 'Slideshhow' button, image description button (i)
Author: PGC
Author URI: http://PhotoGalleryCreator.com
Version: 2.0
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
		jQuery("#itemBG")					.val('eae6ef');
		jQuery("#itemTitle")				.val('7485c2');
		jQuery("#itemDescription")			.val('e0e0e0');
		tChecked();
		return false;
	});
	jQuery("#slideshow_default").click(function(){
		jQuery('#autoSlideShow option[value="false"]').attr('selected','selected');
		jQuery('#slDelay option[value="4"]:first').attr('selected','selected');
		return false;
	});
});
</script>
	<form method="POST"><div>
		<?php wp_nonce_field('skin_settings'); ?>
		<input type="hidden" name="skin_options" value="autoSlideShow,slDelay,flashBackcolor,buttonsBG,flashBacktransparent,buttonsMouseOver,buttonsMouseOut,catButtonsMouseOver,catButtonsMouseOut,catButtonsTextMouseOver,catButtonsTextMouseOut,thumbMouseOver,thumbMouseOut,mainTitle,categoryTitle,itemBG,itemTitle,itemDescription" />
	<!-- Slideshow settings -->
		<h3><?php _e('Slideshow Settings','flag'); ?> <small style="margin-left:20px;"><a href="#" id="slideshow_default">(<?php _e('set default settings','flag'); ?>)</a></small></h3>
		<table id="slideshow" class="form-table flag-options">
			<tr>
				<th style="width: 30%;"><?php _e('Auto Slideshow','flag'); ?>:</th>
				<td><select name="autoSlideShow" id="autoSlideShow" style="width: 72px;">
					<option value="false" <?php selected('false', $autoSlideShow); ?>> <?php _e('Off', 'flag') ;?></option>
					<option value="true" <?php selected('true', $autoSlideShow); ?>> <?php _e('On', 'flag') ;?></option>
				</select></td>
			</tr>
			<tr>
				<th style="width: 30%;"><?php _e('Slideshow Delay','flag'); ?>:</th>
				<td><select name="slDelay" id="slDelay" style="width: 72px;">
					<option value="4" <?php selected('4', $slDelay); ?>> Default</option>
					<option value="1" <?php selected('1', $slDelay); ?>> 1</option>
					<option value="2" <?php selected('2', $slDelay); ?>> 2</option>
					<option value="3" <?php selected('3', $slDelay); ?>> 3</option>
					<option value="4"> 4</option>
					<option value="5" <?php selected('5', $slDelay); ?>> 5</option>
					<option value="6" <?php selected('6', $slDelay); ?>> 6</option>
					<option value="7" <?php selected('7', $slDelay); ?>> 7</option>
					<option value="8" <?php selected('8', $slDelay); ?>> 8</option>
					<option value="9" <?php selected('9', $slDelay); ?>> 9</option>
					<option value="10" <?php selected('10', $slDelay); ?>> 10</option>
					<option value="11" <?php selected('11', $slDelay); ?>> 11</option>
					<option value="12" <?php selected('12', $slDelay); ?>> 12</option>
					<option value="13" <?php selected('13', $slDelay); ?>> 13</option>
					<option value="14" <?php selected('14', $slDelay); ?>> 14</option>
					<option value="15" <?php selected('15', $slDelay); ?>> 15</option>
					<option value="16" <?php selected('16', $slDelay); ?>> 16</option>
					<option value="17" <?php selected('17', $slDelay); ?>> 17</option>
					<option value="18" <?php selected('18', $slDelay); ?>> 18</option>
					<option value="19" <?php selected('19', $slDelay); ?>> 19</option>
					<option value="20" <?php selected('20', $slDelay); ?>> 20</option>
					<option value="21" <?php selected('21', $slDelay); ?>> 21</option>
					<option value="22" <?php selected('22', $slDelay); ?>> 22</option>
					<option value="23" <?php selected('23', $slDelay); ?>> 23</option>
					<option value="24" <?php selected('24', $slDelay); ?>> 24</option>
					<option value="25" <?php selected('25', $slDelay); ?>> 25</option>
					<option value="26" <?php selected('26', $slDelay); ?>> 26</option>
					<option value="27" <?php selected('27', $slDelay); ?>> 27</option>
					<option value="28" <?php selected('28', $slDelay); ?>> 28</option>
					<option value="29" <?php selected('29', $slDelay); ?>> 29</option>
					<option value="30" <?php selected('30', $slDelay); ?>> 30</option>
				</select></td>
			</tr>
		</table>
        
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
