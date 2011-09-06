<?php
/*
wSkin Name: Banner Rotator Widget Default
Skin URI:
Description:
Author: PGC
Author URI: http://PhotoGalleryCreator.com
Version: 1.0
*/

function flagShowSkin_banner_widget_default($args) {
	extract($args);
	$flag_options = get_option('flag_options');

	$skinID = 'id_'.mt_rand();
	// look up for the path
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	$playlistpath = $flag_options['galleryPath'].'playlists/banner/'.$xml.'.xml';
	$js = $flag_options['skinsDirURL'].$skin."/jquery.cycle.lite.js";
	$data = file_get_contents($skinpath.'/settings/settings.xml');
	$flashBackcolor = flagGetBetween($data,'<property1>0x','</property1>');
	if(empty($width)) {
		$width = flagGetBetween($data,'<width><![CDATA[',']]></width>');
	}
	if(empty($height)) {
		$height = flagGetBetween($data,'<height><![CDATA[',']]></height>');
	}

	$wmode = flagGetBetween($data,'<property0><![CDATA[',']]></property0>');
	if(empty($flashBackcolor)) {
		$flashBackcolor = $flag_options['flashBackcolor'];
	}
	$params['autoPlay'] = flagGetBetween($data,'<autoPlay>','</autoPlay>');
	$params['slideshowDelay'] = flagGetBetween($data,'<slideshowDelay>','</slideshowDelay>');

	$alternate = get_include_contents($skinpath . "/jgallery.php", $playlistpath, $skin, $skinID, $width, $height, $params);
	// init the flash output
	$swfobject = new flag_swfobject( $flag_options['skinsDirURL'].$skin.'/gallery.swf' , $skinID, $width, $height, '10.1.52', FLAG_URLPATH .'skins/expressInstall.swf');
	global $swfCounter;

	$swfobject->add_params('wmode', $wmode);
	$swfobject->add_params('allowfullscreen', 'true');
	$swfobject->add_params('allowScriptAccess', 'always');
	$swfobject->add_params('saling', 'lt');
	$swfobject->add_params('scale', 'noScale');
	$swfobject->add_params('menu', 'false');
	$swfobject->add_params('bgcolor', '#'.$flashBackcolor );
	$swfobject->add_attributes('id', $skinID);
	$swfobject->add_attributes('name', $skinID);

	// adding the flash parameter
	$swfobject->add_flashvars( 'path', $flag_options['skinsDirURL'].$skin.'/' );
	$swfobject->add_flashvars( 'skinID', $skinID );
	$swfobject->add_flashvars('playlist', $xml);
	// create the output
	$out = '<div class="grandbanner '.$wmode.'">' . $swfobject->output($alternate) . '</div>';
	// add now the script code
	$out .= "\n".'<script type="text/javascript" src="'.$js.'"></script>';
	$out .= "\n".'<script type="text/javascript" defer="defer">';
	$out .= $swfobject->javascript();
	$out .= "\n".'</script>';

	$out = apply_filters('flag_show_flash_w_content', $out);
			
	return $out;	
}
remove_all_filters( 'flagShowWidgetBannerSkin' );
add_filter( 'flagShowWidgetBannerSkin', 'flagShowSkin_banner_widget_default' );
?>