<?php
/*
mSkin Name: Default Player
Skin URI:
Description:
Author: PGC
Author URI: http://PhotoGalleryCreator.com
Version: 1.1
*/

function flagShowSkin_music_default($args) {
	extract($args);
	$flag_options = get_option('flag_options');

	$skinID = 'id_'.mt_rand();
	// look up for the path
	$playlistpath = $flag_options['galleryPath'].'playlists/'.$playlist.'.xml';
	$data = file_get_contents($playlistpath);
	$flashBackcolor = flagGetBetween($data,'<property1>0x','</property1>');
	if(empty($width)) {
		$width = flagGetBetween($data,'<width><![CDATA[',']]></width>');
	}
	if(empty($height)) {
		$height = flagGetBetween($data,'<height><![CDATA[',']]></height>');
	}

	if(empty($wmode)) {
		$wmode = flagGetBetween($data,'<property0><![CDATA[',']]></property0>');
	}
	if(empty($flashBackcolor)) {
		$flashBackcolor = $flag_options['flashBackcolor'];
	}

	require_once( FLAG_ABSPATH.'admin/playlist.functions.php');
	$playlist_data = get_playlist_data($playlistpath);
	$alternative = '';
	if(count($playlist_data['items'])) {
		foreach( $playlist_data['items'] as $id ) {
			$mp3Object = get_post($id);
			$url = wp_get_attachment_url($mp3Object->ID);
			$thumb = get_post_meta($mp3Object->ID, 'thumbnail', true);
			$aimg = $thumb? '<img src="'.$thumb.'" style="float:left;margin-right:10px;width:150px;height:auto;" alt="" />' : '';
			$atitle = $mp3Object->post_title? '<strong style="display:block;">'.$mp3Object->post_title.'</strong>' : '';
			$acontent = $mp3Object->post_content? '<div style="padding:4px 0;">'.$mp3Object->post_content.'</div>' : '';
			$alternative .= '<div id="video_'.$mp3Object->ID.'" style="overflow:hidden;padding:7px 0;">'.$aimg.$atitle.$acontent.'<p><audio src="'.$url.'" controls preload="none" autobuffer="false"></audio></p></div>';
		}
	}

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
	$swfobject->add_flashvars('playlist', $playlist);
	// create the output
	$out = '<div class="grandmusic">' . $swfobject->output($alternative) . '</div>';
	// add now the script code
	$out .= "\n".'<script type="text/javascript" defer="defer">';
	$out .= $swfobject->javascript();
	$out .= "\n".'</script>';

	$out = apply_filters('flag_show_flash_content', $out);	
			
	return $out;	
}
remove_all_filters( 'flagShowMusicSkin' );
add_filter( 'flagShowMusicSkin', 'flagShowSkin_music_default' );
?>
