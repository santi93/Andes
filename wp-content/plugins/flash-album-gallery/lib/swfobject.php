<?php
/**
 * Return a script for the flash slideshow. Can be used in any tmeplate with <?php echo flagShowFlashAlbum($galleryID, $name, $width, $height, $skin) ? >
 * Require the script swfobject.js in the header or footer
 * 
 * @access public 
 * @param integer $galleryID ID of the gallery
 * @param integer $flashWidth Width of the flash container
 * @param integer $flashHeight Height of the flash container
 * @return the content
 */
function flagShowFlashAlbum($galleryID, $name='', $width='', $height='', $skin='', $playlist='', $wmode='', $linkto='') {
 	global $post;	
	require_once ( dirname(__FILE__) . '/class.swfobject.php' );

	if($linkto) {
		$post = get_post($linkto);
	} 
	$flag_options = get_option('flag_options');
	$skinID = 'sid_'.mt_rand();
	if($skin == '') $skin = $flag_options['flashSkin'];
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	if(!is_dir($skinpath)) {
		$skin = 'default';
		$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	} 
	$swfmousewheel = '';
	$flashBacktransparent = '';
	$flashBackcolor = '';
	if (empty($width) ) $width  = $flag_options['flashWidth'];
	if (empty($height)) $height = (int) $flag_options['flashHeight'];
	if(file_exists($skinpath . "/settings/settings.xml")) {
		$data = file_get_contents($skinpath . "/settings/settings.xml");
		if(empty($wmode))
			$wmode = flagGetBetween($data,'<property0><![CDATA[',']]></property0>');
		$flashBackcolor = flagGetBetween($data,'<property1>0x','</property1>');
		$swfmousewheel = flagGetBetween($data,'<swfmousewheel>','</swfmousewheel>');
	} else if(file_exists($skinpath . "_settings.php")) {
		include( $skinpath . "_settings.php");
	} else if(file_exists($skinpath . "/settings.php")) {
		include( $skinpath . "/settings.php");
	}
	if(empty($wmode)) $wmode = $flashBacktransparent? 'transparent' : 'opaque';
	if(empty($flashBackcolor)) $flashBackcolor = $flag_options['flashBackcolor'];
	
	$altColors['wmode'] = $wmode;
	$altColors['Background'] = $flashBackcolor;
	$altColors['BarsBG'] = $flag_options['BarsBG'];
	$altColors['CatBGColor'] = $flag_options['CatBGColor'];
	$altColors['CatBGColorOver'] = $flag_options['CatBGColorOver'];
	$altColors['CatColor'] = $flag_options['CatColor'];
	$altColors['CatColorOver'] = $flag_options['CatColorOver'];
	$altColors['ThumbBG'] = $flag_options['ThumbBG'];
	$altColors['ThumbLoaderColor'] = $flag_options['ThumbLoaderColor'];
	$altColors['TitleColor'] = $flag_options['TitleColor'];
	$altColors['DescrColor'] = $flag_options['DescrColor'];
	
	if($flag_options['jAlterGal']) {
		$alternate = get_include_contents(FLAG_ABSPATH."admin/jgallery.php", $galleryID, $skin, $skinID, $width, $height, $altColors);
	} else {
		$alternate = '';
	}

	// init the flash output
	$swfobject = new flag_swfobject( $flag_options['skinsDirURL'].$skin.'/gallery.swf' , $skinID, $width, $height, '10.1.52', FLAG_URLPATH .'skins/expressInstall.swf');

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

	// adding the flash parameter	
	$swfobject->add_flashvars( 'path', $flag_options['skinsDirURL'].$skin.'/' );
	$swfobject->add_flashvars( 'gID', $galleryID );
	$swfobject->add_flashvars( 'galName', $name );
	$swfobject->add_flashvars( 'skinID', $skinID );
	$swfobject->add_flashvars( 'postID', $post->ID);
	$swfobject->add_flashvars( 'postTitle', urlencode($post->post_title." "));
	// create the output
	$out = '<div class="flashalbum">' . $swfobject->output($alternate) . '</div>';
	// add now the script code
	$out .= "\n".'<script type="text/javascript" defer="defer">';
	$out .= $swfobject->javascript();
	$out .= "\n".'</script>';

	$out = apply_filters('flag_show_flash_content', $out);
			
	return $out;	
}

function flagShowMPlayer($playlist, $width, $height, $wmode='') {
	
	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
    require_once ( dirname(dirname(__FILE__)) . '/admin/playlist.functions.php');

	$flag_options = get_option('flag_options');
	$playlistPath = $flag_options['galleryPath'].'playlists/'.$playlist.'.xml';
	$playlist_data = get_playlist_data(ABSPATH.$playlistPath);
	$skin = $playlist_data['skin'];
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	include_once ( $skinpath.'/'.$skin.'.php' );
	$args = array(
		'playlist' 	=> $playlist, 
		'skin' 		=> $skin, 
		'width' 	=> $width, 
		'height' 	=> $height,
		'wmode' 	=> $wmode
	);
	$out = apply_filters( 'flagShowMusicSkin', $args );
	return $out;	
}

function flagShowVPlayer($playlist, $width, $height, $wmode='') {
	
	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
    require_once ( dirname(dirname(__FILE__)) . '/admin/video.functions.php');

	$flag_options = get_option('flag_options');
	$playlistPath = $flag_options['galleryPath'].'playlists/video/'.$playlist.'.xml';
	$playlist_data = get_v_playlist_data(ABSPATH.$playlistPath);
	$skin = $playlist_data['skin'];
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	include_once ( $skinpath.'/'.$skin.'.php' );
	$args = array(
		'playlist'	=> $playlist, 
		'skin' 		=> $skin, 
		'width' 	=> $width, 
		'height' 	=> $height,
		'wmode' 	=> $wmode
	);
	$out = apply_filters( 'flagShowVideoSkin', $args );
	return $out;
}

function flagShowVmPlayer($id, $w, $h, $autoplay) {

	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
	$flag_options = get_option('flag_options');
	$vID = 'vid_'.mt_rand();
	if (empty($w)) $w = $flag_options['vWidth'];
	if (empty($h)) $h = $flag_options['vHeight'];
	if (empty($autoplay)) $autoplay = $flag_options['vAutoplay'];

	// init the flash output
	$swfobject = new flag_swfobject( FLAG_URLPATH.'lib/video_mini.swf' , $vID, $w, $h, '10.1.52', FLAG_URLPATH .'skins/expressInstall.swf');

	$videoObject = get_post($id);
	$url = wp_get_attachment_url($videoObject->ID);
	$thumb = get_post_meta($videoObject->ID, 'thumbnail', true);
	$aimg = $thumb? '<img src="'.$thumb.'" style="float:left;margin-right:10px;width:150px;height:auto;" alt="" />' : '';
	$atitle = $videoObject->post_title? '<strong>'.$videoObject->post_title.'</strong>' : '';
	$acontent = $videoObject->post_content? '<div style="padding:4px 0;">'.$videoObject->post_content.'</div>' : '';
	$alternative = '<div id="video_'.$videoObject->ID.'" style="overflow:hidden;padding:7px 0;">'.$aimg.$atitle.$acontent.'<div style="font-size:80%;">This browser does not support flash! You can <a href="'.$url.'">download the video</a> instead.</div></div>';

	$swfobject->add_params('wmode', 'transparent');
	$swfobject->add_params('allowfullscreen', 'true');
	$swfobject->add_params('allowScriptAccess', 'always');
	$swfobject->add_params('saling', 'lt');
	$swfobject->add_params('scale', 'noScale');
	$swfobject->add_params('menu', 'false');
	$swfobject->add_params('bgcolor', '#'.$flag_options['videoBG']);
	$swfobject->add_attributes('styleclass', 'grandflv');
	$swfobject->add_attributes('id', $vID);

	// adding the flash parameter	
	$swfobject->add_flashvars( 'path', FLAG_URLPATH.'lib/' );
	$swfobject->add_flashvars( 'vID', $id );
	$swfobject->add_flashvars( 'flashID', $vID );
	$swfobject->add_flashvars( 'autoplay', $autoplay );
	// create the output
	$out = '<div class="grandflv">' . $swfobject->output($alternative) . '</div>';
	// add now the script code
	$out .= "\n".'<script type="text/javascript" defer="defer">';
	$out .= $swfobject->javascript();
	$out .= "\n".'</script>';

	$out = apply_filters('flag_flv_mini', $out);
			
	return $out;
}

function flagShowBanner($xml, $width, $height, $wmode='') {

	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
    require_once ( dirname(dirname(__FILE__)) . '/admin/banner.functions.php');

	$flag_options = get_option('flag_options');
	$playlistPath = $flag_options['galleryPath'].'playlists/banner/'.$xml.'.xml';
	$playlist_data = get_b_playlist_data(ABSPATH.$playlistPath);
	$skin = $playlist_data['skin'];
	$items = $playlist_data['items'];
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	include_once ( $skinpath.'/'.$skin.'.php' );
	$args = array(
		'xml'		=> $xml,
		'skin' 		=> $skin,
		'items' 	=> $items,
		'width' 	=> $width,
		'height' 	=> $height,
		'wmode' 	=> $wmode
	);
	$out = apply_filters( 'flagShowBannerSkin', $args );
	return $out;
}

function flagShowWidgetBanner($xml, $width, $height, $skin) {

	require_once ( dirname(__FILE__) . '/class.swfobject.php' );
    require_once ( dirname(dirname(__FILE__)) . '/admin/banner.functions.php');

	$flag_options = get_option('flag_options');
	$playlistPath = $flag_options['galleryPath'].'playlists/banner/'.$xml.'.xml';
	$playlist_data = get_b_playlist_data(ABSPATH.$playlistPath);
	if(!$skin) {
		$skin = $playlist_data['skin'];
	}
	$items = $playlist_data['items'];
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	include_once ( $skinpath.'/'.$skin.'.php' );
	$args = array(
		'xml'		=> $xml,
		'skin' 		=> $skin,
		'items' 	=> $items,
		'width' 	=> $width,
		'height' 	=> $height
	);
	$out = apply_filters( 'flagShowWidgetBannerSkin', $args );
	return $out;
}

function flagGetBetween($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}

function flagGetUserNow($userAgent) {
    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
    'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
    'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby|yandex';
    $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0);
    return $isCrawler;
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