<?php header("content-type:text/xml;charset=utf-8"); ?>
<!--<?php 
	require_once( str_replace("\\","/", dirname(__FILE__).'/settings.php') ); 
	$file_settings = str_replace("\\","/", dirname(dirname(__FILE__)).'/'.basename( dirname(__FILE__) ).'_settings.php');
	if ( file_exists( $file_settings ) ) {
		include_once( $file_settings ); 
	}
?>-->
<?php $background = $flashBacktransparent ? '' : "0x{$flashBackcolor}"; ?>
<color>
	<!--graphic elements-->
	<background color="<?php echo $background; ?>"/>
	<buttons_bg color="0x<?php echo $buttonsBG; ?>"/>
	<buttons mouseOver="0x<?php echo $buttonsMouseOver; ?>" mouseOut="0x<?php echo $buttonsMouseOut; ?>"/>
	<categoryButtons mouseOver="0x<?php echo $catButtonsMouseOver; ?>" mouseOut="0x<?php echo $catButtonsMouseOut; ?>"/>
	<categoryButtonsText mouseOver="0x<?php echo $catButtonsTextMouseOver; ?>" mouseOut="0x<?php echo $catButtonsTextMouseOut; ?>"/>
	<thumbnail mouseOver="0x<?php echo $thumbMouseOver; ?>" mouseOut="0x<?php echo $thumbMouseOut; ?>"/>
	<!--text elements-->
	<mainTitle textColor="0x<?php echo $mainTitle; ?>"/>
	<categoryTitle textColor="0x<?php echo $categoryTitle; ?>"/>
	<item_bg color="0x<?php echo $itemBG; ?>"/>
	<itemTitle textColor="0x<?php echo $itemTitle; ?>"/>
	<itemDescription textColor="0x<?php echo $itemDescription; ?>"/>
    <autoSlideShow data="<?php echo $autoSlideShow; ?>"/>
	<slDelay data="<?php echo $slDelay; ?>"/>
</color>
