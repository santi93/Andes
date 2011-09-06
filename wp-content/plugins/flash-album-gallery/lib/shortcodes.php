<?php
/**
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

class FlAG_shortcodes {
	var $flag_shortcode;
	var $flag_add_script;
	var $flag_add_mousewheel;
	// register the new shortcodes
	function FlAG_shortcodes() {
	
		// do_shortcode on the_excerpt could causes several unwanted output. Uncomment it on your own risk
		// add_filter('the_excerpt', array(&$this, 'convert_shortcode'));
		// add_filter('the_excerpt', 'do_shortcode', 11);

		add_shortcode( 'flagallery', array(&$this, 'show_flashalbum' ) );
		add_shortcode( 'grandmp3', array(&$this, 'grandmp3' ) );
		add_shortcode( 'grandmusic', array(&$this, 'grandmusic' ) );
		add_shortcode( 'grandflv', array(&$this, 'grandflv' ) );
		add_shortcode( 'grandvideo', array(&$this, 'grandvideo' ) );
		add_shortcode( 'grandbanner', array(&$this, 'grandbanner' ) );
		add_shortcode( 'grandbannerwidget', array(&$this, 'grandbannerwidget' ) );
		add_action('wp_footer', array(&$this, 'add_script'));

	}

	function show_flashalbum( $atts ) {
		global $wpdb, $flagdb;

		extract(shortcode_atts(array(
			'gid' 		=> '',
			'album'		=> '',
			'name'		=> '',
			'w'		 	=> '',
			'h'		 	=> '',
			'orderby' 	=> '',
			'order'	 	=> '',
			'exclude' 	=> '',
			'skin'	 	=> '',
			'play'	 	=> '',
			'wmode' 	=> ''
		), $atts ));
		
		$out = '';
		// make an array out of the ids
        if($album) {
        	$gallerylist = $flagdb->get_album($album);
            $ids = explode( ',', $gallerylist );
			$gids = str_replace(',','_',$gallerylist);
    		foreach ($ids as $id) {
    			$galleryID = $wpdb->get_var("SELECT gid FROM $wpdb->flaggallery WHERE gid = '$id' ");
    			if(!$galleryID) return $out =  sprintf(__('[Gallery %s not found]','flag'),$id);
    		}

    		if( $galleryID )
    			$out = flagShowFlashAlbum($gids, $name, $w, $h, $skin, $playlist, $wmode);

        } elseif($gid == "all") {
			if(!$orderby) $orderby='gid';
			if(!$order) $order='DESC';
            $gallerylist = $flagdb->find_all_galleries($orderby, $order);
            if(is_array($gallerylist)) {
				$excludelist = explode(',',$exclude);
				foreach($gallerylist as $gallery) {
					if (in_array($gallery->gid, $excludelist))
						continue;
					$gids.='_'.$gallery->gid;
				}
                $gids = ltrim($gids,'_');
                $out = flagShowFlashAlbum($gids, $name, $w, $h, $skin, $playlist, $wmode);
			} else {
            	$out = __('[Gallery not found]','flag');
			}
        } else {
            $ids = explode( ',', $gid );
    		$gids = str_replace(',','_',$gid);

    		foreach ($ids as $id) {
    			$galleryID = $wpdb->get_var("SELECT gid FROM $wpdb->flaggallery WHERE gid = '$id' ");
    			if(!$galleryID) $galleryID = $wpdb->get_var("SELECT gid FROM $wpdb->flaggallery WHERE name = '$id' ");
    			if(!$galleryID) return $out =  sprintf(__('[Gallery %s not found]','flag'),$id);
    		}

    		if( $galleryID )
    			$out = flagShowFlashAlbum($gids, $name, $w, $h, $skin, $playlist, $wmode);
    		else
    			$out = __('[Gallery not found]','flag');
    	}
		$this->flag_shortcode = true;
		$this->flag_add_script = true;

		$flag_options = get_option('flag_options');
		if($skin == '') $skin = $flag_options['flashSkin'];
		$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
		if(!is_dir($skinpath)) {
			$skin = 'default';
			$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
		} 
		$swfmousewheel = false;
		if(file_exists($skinpath . "/settings/settings.xml")) {
			$data = file_get_contents($skinpath . "/settings/settings.xml");
			$swfmousewheel = flagGetBetween($data,'<swfmousewheel>','</swfmousewheel>');
		} 
		if($swfmousewheel == 'true') $this->flag_add_mousewheel = true;

        return $out;
	}

	function add_script() {
		if ( $this->flag_shortcode ) {
			wp_register_script('flagscroll', plugins_url('/admin/js/flagscroll.js', dirname(__FILE__)), array('jquery'), '1.0', true );
			wp_print_scripts('flagscroll');
		}
		if ( $this->flag_add_script ) {
			wp_register_style('fancybox', plugins_url('/admin/js/jquery.fancybox-1.3.4.css', dirname(__FILE__)) );
			wp_print_styles('fancybox');
			wp_register_script('fancybox', plugins_url('/admin/js/jquery.fancybox-1.3.4.pack.js', dirname(__FILE__)), array('jquery'), '1.3.4', true );
			wp_print_scripts('fancybox');
			wp_register_script('flagscript', plugins_url('/admin/js/script.js', dirname(__FILE__)), array('jquery'), '1.0', true );
			wp_print_scripts('flagscript');
		}
		if ( $this->flag_add_mousewheel ) {
			wp_register_script('swfmousewheel', plugins_url('/admin/js/swfmousewheel.js', dirname(__FILE__)), false, '2.0', true );
			wp_print_scripts('swfmousewheel');
		}
	}

	function grandmusic( $atts ) {

		extract(shortcode_atts(array(
			'playlist'	=> '',
			'w'		 	=> '',
			'h'		 	=> ''
		), $atts ));
		$out = sprintf(__('[Playlist %s not found]','flag'),$playlist);
		if($playlist) {
			$flag_options = get_option('flag_options');
			if(!file_exists($flag_options['galleryPath'].'playlists/'.$playlist.'.xml')) { 
				return $out;
			}
			$this->flag_shortcode = true;
			$this->flag_add_mousewheel = true;
            $out = flagShowMPlayer($playlist, $w, $h);
		}
        return $out;
	}

	function grandmp3( $atts ) {
		global $wpdb;
		extract(shortcode_atts(array(
			'id'	=> ''
		), $atts ));
		$out = '';
		$flag_options = get_option('flag_options');
		if($id) {
			$url = wp_get_attachment_url($id);
			$url = str_replace(array('.mp3'), array(''), $url);
			$out = '<script type="text/javascript">swfobject.embedSWF("'.FLAG_URLPATH.'lib/mini.swf", "c-'.$id.'", "250", "20", "10.1.52", "expressInstall.swf", {path:"'.$url.'",bgcolor:"'.$flag_options["mpBG"].'",color1:"'.$flag_options["mpColor1"].'",color2:"'.$flag_options["mpColor2"].'"}, {wmode:"transparent"}, {id:"f-'.$id.'",name:"f-'.$id.'"});</script>
<div id="c-'.$id.'"><audio src="'.$url.'.mp3" controls preload="none" autobuffer="false"></audio></div>';
		}
       	return $out;
	}

	function grandvideo( $atts ) {

		extract(shortcode_atts(array(
			'playlist'	=> '',
			'w'		 	=> '',
			'h'		 	=> ''
		), $atts ));
		$out = sprintf(__('[Playlist %s not found]','flag'),$playlist);
		if($playlist) {
			$flag_options = get_option('flag_options');
			if(!file_exists($flag_options['galleryPath'].'playlists/video/'.$playlist.'.xml')) { 
				return $out;
			}
			$data = file_get_contents($flag_options['galleryPath'].'playlists/video/'.$playlist.'.xml');
			$swfmousewheel = false;
			$swfmousewheel = flagGetBetween($data,'<swfmousewheel>','</swfmousewheel>');
			if($swfmousewheel == 'true') $this->flag_add_mousewheel = true;
			$this->flag_shortcode = true;
            $out = flagShowVPlayer($playlist, $w, $h);
			
		}
        return $out;
	}

	function grandflv( $atts ) {
		global $wpdb;
		extract(shortcode_atts(array(
			'id'		=> '',
			'w'			=> '',
			'h'			=> '',
			'autoplay'	=> ''
		), $atts ));
		$out = '';
		if($id) {
			$this->flag_shortcode = true;
            $out = flagShowVmPlayer($id, $w, $h, $autoplay);
		}
       	return $out;
	}
	
	function grandbanner( $atts ) {

		extract(shortcode_atts(array(
			'xml'	=> '',
			'w'		=> '',
			'h'		=> ''
		), $atts ));
		$out = sprintf(__('[XML %s not found]','flag'),$xml);
		if($xml) {
			$flag_options = get_option('flag_options');
			if(!file_exists($flag_options['galleryPath'].'playlists/banner/'.$xml.'.xml')) {
				return $out;
			}
			$data = file_get_contents($flag_options['galleryPath'].'playlists/banner/'.$xml.'.xml');
			$swfmousewheel = false;
			$swfmousewheel = flagGetBetween($data,'<swfmousewheel>','</swfmousewheel>');
			if($swfmousewheel == 'true') $this->flag_add_mousewheel = true;
			$this->flag_shortcode = true;
            $out = flagShowBanner($xml, $w, $h);
		}
        return $out;
	}

	function grandbannerwidget( $atts ) {

		extract(shortcode_atts(array(
			'xml'	=> '',
			'w'		=> '',
			'h'		=> '',
			'skin'	=> ''
		), $atts ));
		$out = sprintf(__('[XML %s not found]','flag'),$xml);
		if($xml && $skin) {
			$flag_options = get_option('flag_options');
			$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$skin;
			if(!file_exists($skinpath)) {
				return $out;
			}
			$data = @file_get_contents($skinpath);
			$swfmousewheel = false;
			$swfmousewheel = flagGetBetween($data,'<swfmousewheel>','</swfmousewheel>');
			if($swfmousewheel == 'true') $this->flag_add_mousewheel = true;
			$this->flag_shortcode = true;
            $out = flagShowWidgetBanner($xml, $w, $h, $skin);
		}
        return $out;
	}

}

// let's use it
$flagShortcodes = new FlAG_Shortcodes;	

?>
