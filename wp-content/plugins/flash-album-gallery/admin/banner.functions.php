<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

function get_b_playlist_data( $playlist_file ) {
	global $wpdb;	
	$playlist_content = file_get_contents($playlist_file);

	$playlist_data['title'] = flagGallery::flagGetBetween($playlist_content,'<title><![CDATA[',']]></title>');
	$playlist_data['skin'] = flagGallery::flagGetBetween($playlist_content,'<skin><![CDATA[',']]></skin>');
	$playlist_data['width'] = flagGallery::flagGetBetween($playlist_content,'<width><![CDATA[',']]></width>');
	$playlist_data['height'] = flagGallery::flagGetBetween($playlist_content,'<height><![CDATA[',']]></height>');
	$playlist_data['description'] = flagGallery::flagGetBetween($playlist_content,'<description><![CDATA[',']]></description>');
	preg_match_all( '|<item id="(.*)">|', $playlist_content, $items );
	$playlist_data['items'] = $items[1];
	return $playlist_data;
}

/**
 * Check the playlists directory and retrieve all playlist files with playlist data.
 *
 */
function get_b_playlists($playlist_folder = '') {

	$flag_options = get_option('flag_options');
	$flag_playlists = array ();
	$playlist_root = ABSPATH.$flag_options['galleryPath'].'playlists/banner';
	if( !empty($playlist_folder) )
		$playlist_root = $playlist_folder;

	// Files in flagallery/playlists directory
	$playlists_dir = @ opendir( $playlist_root);
	$playlist_files = array();
	if ( $playlists_dir ) {
		while (($file = readdir( $playlists_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( substr($file, -4) == '.xml' )
				$playlist_files[] = $file;
		}
	}
	@closedir( $playlists_dir );

	if ( !$playlists_dir || empty($playlist_files) )
		return $flag_playlists;

	foreach ( $playlist_files as $playlist_file ) {
		if ( !is_readable( "$playlist_root/$playlist_file" ) )
			continue;

		$playlist_data = get_b_playlist_data( "$playlist_root/$playlist_file" );

		if ( empty ( $playlist_data['title'] ) )
			continue;

		$flag_playlists[basename( $playlist_file, ".xml" )] = $playlist_data;
	}
	uasort( $flag_playlists, create_function( '$a, $b', 'return strnatcasecmp( $a["title"], $b["title"] );' ));

	return $flag_playlists;
}

function flagSave_bPlaylist($title,$descr,$data,$file='',$skinaction='') {
	global $wpdb;
	require_once(ABSPATH . '/wp-admin/includes/image.php');
	if(!trim($title)) {
		$title = 'default';
	}
	if (!$file) {
		$file = sanitize_title($title);
	}
	if(!is_array($data))
		$data = explode(',', $data);

	$flag_options = get_option('flag_options');
    $skin = isset($_POST['skinname'])? $_POST['skinname'] : 'banner_default';
	if(!$skinaction) {
    	$skinaction = isset($_POST['skinaction'])? $_POST['skinaction'] : 'update';
	}
	$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
	$playlistPath = ABSPATH.$flag_options['galleryPath'].'playlists/banner/'.$file.'.xml';
	if( file_exists($playlistPath) && ($skin == $skinaction) ) {
		$settings = file_get_contents($playlistPath);
	} else {
		$settings = file_get_contents($skinpath . "/settings/settings.xml");
	}
	$properties = flagGallery::flagGetBetween($settings,'<properties>','</properties>');
	$w = flagGallery::flagGetBetween($properties,'<width><![CDATA[',']]></width>');
	$h = flagGallery::flagGetBetween($properties,'<height><![CDATA[',']]></height>');
	$suffix = $w.'x'.$h;
	if(count($data)) {
		$content = '<gallery>
<properties>'.$properties.'</properties>
<category id="'.$file.'">
	<properties>
		<title><![CDATA['.$title.']]></title>
		<description><![CDATA['.$descr.']]></description>
		<skin><![CDATA['.$skin.']]></skin>
	</properties>
	<items>';

		foreach( (array) $data as $id) {
			$ban = get_post($id);
			if($ban->ID) {
				$url = wp_get_attachment_url($ban->ID);
				if($skin == 'banner_default') {
					$path = get_attached_file($ban->ID);
					$info = pathinfo($path);
					$dir = $info['dirname'];
					$ext = $info['extension'];
					$name = urldecode( basename( str_replace( '%2F', '/', urlencode( $path ) ), ".$ext" ) );
					$img_file = "{$dir}/{$name}-{$suffix}.{$ext}";
					if(!file_exists($img_file)){
					    $thumb = image_resize($path,$w,$h,$cut=true,$suffix);
						if(is_string($thumb)) {
					    	$img = substr($thumb, strpos($thumb, 'wp-content'));
							$track = get_bloginfo('wpurl') . '/' .  $img;
						} else {
							$track = $url;
						}
					    
					} else {
						$track = dirname($url)."/{$name}-{$suffix}.{$ext}";
					}
				} else {
					$track = $url;
				}
			    $thumbnail = get_post_meta($id, 'thumbnail', true);
			    $link = get_post_meta($id, 'link', true);
			    $preview = get_post_meta($id, 'preview', true);
				$content .= '
		<item id="'.$ban->ID.'">
          <track>'.$track.'</track>
          <title><![CDATA['.$ban->post_title.']]></title>
          <link>'.$link.'</link>
          <preview>'.$preview.'</preview>
          <description><![CDATA['.$ban->post_content.']]></description>
          <thumbnail>'.$thumbnail.'</thumbnail>
        </item>';
			}
		}
		$content .= '
	</items>
</category>
</gallery>';
		// Save options
		$flag_options = get_option('flag_options');
		if(wp_mkdir_p(ABSPATH.$flag_options['galleryPath'].'playlists/banner/')) {
			if( flagGallery::saveFile($playlistPath,$content,'w') ){
				flagGallery::show_message(__('Playlist Saved Successfully','flag'));
			}
		} else {
			flagGallery::show_message(__('Create directory please:','flag').'"/'.$flag_options['galleryPath'].'playlists/banner/"');
		}
	}
}

function flagSave_bPlaylistSkin($file) {
	global $wpdb;
	$flag_options = get_option('flag_options');
	$playlistPath = ABSPATH.$flag_options['galleryPath'].'playlists/banner/'.$file.'.xml';
	// Save options
	$title = $_POST['playlist_title'];
	$descr = $_POST['playlist_descr'];
	$items = get_b_playlist_data($playlistPath);
	$data = $items['items'];
	flagSave_bPlaylist($title,$descr,$data,$file,$skinaction='update');
}

function flag_b_playlist_delete($playlist) {
	$flag_options = get_option('flag_options');
	$playlistXML = ABSPATH.$flag_options['galleryPath'].'playlists/banner/'.$playlist.'.xml';
	if(file_exists($playlistXML)){
		if(unlink($playlistXML)) {
			flagGallery::show_message("'".$playlist.".xml' ".__('deleted','flag'));
		}
	}
}

?>