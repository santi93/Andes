<?php

/**

Custom thumbnail for FLAGallery
Author : Simone Fumagalli | simone@iliveinperego.com

Credits:
 jCrop : Kelly Hallman <khallman@wrack.org> | http://deepliquid.com/content/Jcrop.html
 
**/

require_once( dirname( dirname(__FILE__) ) . '/flag-config.php');
require_once( FLAG_ABSPATH . '/lib/image.php' );

if ( !is_user_logged_in() )
	die(__('Cheatin&#8217; uh?'));
	
if ( !current_user_can('FlAG Manage gallery') ) 
	die(__('Cheatin&#8217; uh?'));

global $wpdb;

$id = (int) $_GET['id'];

// let's get the image data
$picture = flagdb::find_image($id);

include_once( flagGallery::graphic_library() );
$flag_options=get_option('flag_options');

$thumb = new flag_Thumbnail($picture->imagePath, TRUE);
$thumb->resize(350,350);
// we need the new dimension
$resizedPreviewInfo = $thumb->newDimensions;
$thumb->destruct();

$preview_image		= FLAG_URLPATH . 'flagshow.php?pid=' . $picture->pid . '&amp;width=350&amp;height=350';
$imageInfo			= @getimagesize($picture->imagePath);
$rr = round($imageInfo[0] / $resizedPreviewInfo['newWidth'], 2);

if ( ($flag_options['thumbFix'] == 1) ) {

	$WidthHtmlPrev1  = $flag_options['thumbWidth'];
	$HeightHtmlPrev1 = $flag_options['thumbHeight'];
	$k  = $flag_options['thumbWidth']/150;
	$WidthHtmlPrev  = '150';
	$HeightHtmlPrev = round(($flag_options['thumbHeight']*150)/$flag_options['thumbWidth']);

} else {
	// H > W
	if ($imageInfo[1] > $imageInfo[0]) {

		$HeightHtmlPrev	=  $flag_options['thumbHeight'];
		$WidthHtmlPrev 	= round($imageInfo[0] / ($imageInfo[1] / $flag_options['thumbHeight']),0);
		
	} else {
		
		$WidthtHtmlPrev =  $flag_options['thumbWidth'];
		$HeightHtmlPrev = round($imageInfo[1] / ($imageInfo[0] / $flag_options['thumbWidth']),0);
		
	}
}

?>
<script src="<?php echo FLAG_URLPATH; ?>admin/js/Jcrop/js/jquery.Jcrop.js"></script>
<link rel="stylesheet" href="<?php echo FLAG_URLPATH; ?>admin/js/Jcrop/css/jquery.Jcrop.css" type="text/css" />

<script language="JavaScript">
<!--
	
	var status = 'start';
	var xT, yT, wT, hT, selectedCoords;
	var selectedImage = "thumb-<?php echo $id; ?>";

	function showPreview(coords)
	{
		
		if (status != 'edit') {
			jQuery('#actualThumb').hide();
			jQuery('#previewNewThumb').show();
			status = 'edit';	
		}
		
		var rx = <?php echo $WidthHtmlPrev; ?> / coords.w;
		var ry = <?php echo $HeightHtmlPrev; ?> / coords.h;
		
		jQuery('#imageToEditPreview').css({
			width: Math.round(rx * <?php echo $resizedPreviewInfo['newWidth']; ?>) + 'px',
			height: Math.round(ry * <?php echo $resizedPreviewInfo['newHeight']; ?>) + 'px',
			marginLeft: '-' + Math.round(rx * coords.x) + 'px',
			marginTop: '-' + Math.round(ry * coords.y) + 'px'
		});
		
		xT = coords.x;
		yT = coords.y;
		wT = coords.w;
		hT = coords.h;
		
		jQuery("#sizeThumb").html(xT+" "+yT+" "+wT+" "+hT);
		
	};
	
	function updateThumb() {
		
		if ( (wT == 0) || (hT == 0) || (wT == undefined) || (hT == undefined) ) {
			alert("<?php _e('Select with the mouse the area for the new thumbnail.', 'flag'); ?>");
			return false;			
		}
				
		jQuery.ajax({
		  url: "admin-ajax.php",
		  type : "POST",
		  data:  {x: xT, y: yT, w: wT, h: hT, action: 'flagCreateNewThumb', id: <?php echo $id; ?>, rr: <?php echo $rr; ?>},
		  cache: false,
		  success: function(data){
					var d = new Date();
					newUrl = jQuery("#"+selectedImage).attr("src") + "?" + d.getTime();
					jQuery("#"+selectedImage).attr("src" , newUrl);
					
					jQuery('#thumbMsg').html("<?php _e('Thumbnail updated', 'flag'); ?>");
					jQuery('#thumbMsg').css({'display':'block'});
					setTimeout(function(){ jQuery('#thumbMsg').fadeOut('slow'); }, 1500);
			},
		  error: function() {
		  			jQuery('#thumbMsg').html("<?php _e('Error updating thumbnail.', 'flag'); ?>");
					jQuery('#thumbMsg').css({'display':'block'});
					setTimeout(function(){ jQuery('#thumbMsg').fadeOut('slow'); }, 1500);
		    }
		});

	}
	
-->
</script>

<table width="98%" align="center" style="border:1px solid #DADADA">
	<tr>
		<td rowspan="3" valign="middle" align="center" width="350" style="background-color:#DADADA;">
			<img src="<?php echo $preview_image; ?>" alt="" id="imageToEdit" />	
		</td>
		<td width="300" style="background-color : #DADADA;">
			<small style="margin-left:6px; display:block;"><?php _e('Select the area for the thumbnail from the picture on the left.', 'flag'); ?></small>
		</td>		
	</tr>
	<tr>
		<td align="center" width="300" height="320">
			<div id="previewNewThumb" style="display:none;width:<?php echo $WidthHtmlPrev; ?>px;height:<?php echo $HeightHtmlPrev; ?>px;overflow:hidden; margin-left:5px;">
				<img src="<?php echo $preview_image; ?>" id="imageToEditPreview" />
			</div>
			<div id="actualThumb">
				<img src="<?php echo $picture->thumbURL; ?>?<?php echo time()?>" />
			</div>
		</td>
	</tr>
	<tr style="background-color:#DADADA;">
		<td>
			<input type="button" name="update" value="<?php _e('Update', 'flag'); ?>" onclick="updateThumb()" class="button-secondary" style="float:left; margin-left:4px;"/>
			<div id="thumbMsg" style="color:#FF0000; display : none;font-size:11px; float:right; width:60%; height:2em; line-height:2em;"></div>
		</td>
	</tr>
</table>

<script type="text/javascript">
<!--
	jQuery(document).ready(function(){
		jQuery('#imageToEdit').Jcrop({
			onChange: showPreview,
			onSelect: showPreview,
			aspectRatio: <?php echo round($WidthHtmlPrev/$HeightHtmlPrev,1); ?>
		});
	});
-->
</script>