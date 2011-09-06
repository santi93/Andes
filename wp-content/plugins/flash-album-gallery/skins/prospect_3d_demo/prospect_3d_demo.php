<?php
/*
Skin Name: 3D Pprospect Gallery DEMO
Skin URI: 
Description: 3D Pprospect Gallery. Contains Alternate jQuery gallery for iPhone / iPad and devices without flash player. SEO optimized. Integrated social buttons. Now you can share each photo with Facebook, Twitter and other.<br /><b style="color:red;">Worked only with GRAND FlAGallery v0.58 or higher.</b><br /><br />Details: FullScreen Mode (On/Off), Thumbnails Hints, jQuery alternative gallery, Share Buttons (Facebook, Twitter, AddToAny).
Author: PGC
Author URI: http://PhotoGalleryCreator.com
Version: 1.4
*/

function flagShowSkin($galleryID, $name, $width, $height, $skin) {
	global $post;
	$flag_options = get_option('flag_options');

	$wmode = '';
	$flashBacktransparent = '';
	$flashBackcolor = '';
	$skinpath = '';
	$skinID = 'id_'.mt_rand();
	// look up for the path
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	$js = $flag_options['skinsDirURL'].$skin."/settings/js/extention.js";
	$data = file_get_contents($skinpath . "/settings/settings.xml");
	$wmode = flagGetBetween($data,'<property0><![CDATA[',']]></property0>');
	$flashBackcolor = flagGetBetween($data,'<property1>0x','</property1>');
	$altColors['wmode'] = $wmode;
	$altColors['Background'] = $flashBackcolor;
	$altColors['BarsBG'] = flagGetBetween($data,'<property5>0x','</property5>');
	$altColors['CatBGColor'] = flagGetBetween($data,'<property8>0x','</property8>');
	$altColors['CatBGColorOver'] = flagGetBetween($data,'<property9>0x','</property9>');
	$altColors['CatColor'] = flagGetBetween($data,'<property10>0x','</property10>');
	$altColors['CatColorOver'] = flagGetBetween($data,'<property11>0x','</property11>');
	$altColors['ScrollTrackColor'] = flagGetBetween($data,'<property12>0x','</property12>');
	$altColors['ScrollButtonColor'] = flagGetBetween($data,'<property13>0x','</property13>');
	$altColors['ThumbBG'] = flagGetBetween($data,'<property14>0x','</property14>');
	$altColors['ThumbLoaderColor'] = flagGetBetween($data,'<property7>0x','</property7>');
	$altColors['TitleColor'] = flagGetBetween($data,'<property15>0x','</property15>');
	$altColors['DescrColor'] = flagGetBetween($data,'<property16>0x','</property16>');
	
	$alternate = get_include_contents($skinpath . "/alternate/jgallery.php", $galleryID, $skin, $skinID, $width, $height, $altColors);

	if(empty($wmode)) {
		$wmode = $flashBacktransparent? 'transparent' : 'window';
	}
	if(empty($flashBackcolor)) {
		$flashBackcolor = $flag_options['flashBackcolor'];
	}
	// init the flash output
	$swfobject = new flag_swfobject( $flag_options['skinsDirURL'].$skin.'/gallery.swf' , $skinID, $width, $height, '10.1.52', FLAG_URLPATH .'skins/expressInstall.swf');
	global $swfCounter;

	$swfobject->message = '<p>'. __('The <a href="http://www.macromedia.com/go/getflashplayer">Flash Player</a> and a browser with Javascript support are needed.', 'flag').'</p>';
	$swfobject->add_params('wmode', $wmode);
	$swfobject->add_params('allowfullscreen', 'true');
	$swfobject->add_params('allowScriptAccess', 'always');
	$swfobject->add_params('saling', 'lt');
	$swfobject->add_params('scale', 'noScale');
	$swfobject->add_params('menu', 'false');
	$swfobject->add_params('bgcolor', '#'.$flashBackcolor );
	$swfobject->add_attributes('styleclass', 'flashalbum');
	$swfobject->add_attributes('id', $skinID);
	$swfobject->add_attributes('name', $skinID);

	// adding the flash parameter	
	$swfobject->add_flashvars( 'path', $flag_options['skinsDirURL'].$skin.'/' );
	$swfobject->add_flashvars( 'gID', $galleryID );
	$swfobject->add_flashvars( 'galName', $name );
	$swfobject->add_flashvars( 'skinID', $skinID );
	$swfobject->add_flashvars( 'postID', $post->ID);
	$swfobject->add_flashvars( 'postTitle', urlencode($post->post_title.' ') );
	// create the output
	$out = '<div class="flashalbum">' . $swfobject->output($alternate) . '</div>';
	// add now the script code
	$out .= "\n".'<script type="text/javascript" src="'.$js.'"></script>';
	$out .= "\n".'<script type="text/javascript" defer="defer">';
	$out .= $swfobject->javascript();
	$out .= "\n".'</script>';

	$out = apply_filters('flag_show_flash_content', $out);	
			
	return $out;	
}

function get_include_contents($filename, $galleryID, $skin, $skinID, $width, $height, $altColors) {
    if (is_file($filename)) {
        ob_start();
		extract($altColors);
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}
?>