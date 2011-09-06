<?php
/*
Plugin Name: iSlidex
Plugin URI: http://www.shambix.com/en/news/wordpress-plugin-islidex
Description: Cool slideshow for posts and pages, with different themes to choose from + Widget. Settings and documentation are under Plugin -> iSlidex. Official plugin page <a href="http://www.shambix.com/en/news/wordpress-plugin-islidex/">here</a>.
Version: 2.7
Author: Shambix
Author URI: http://www.shambix.com/
*/
define ('ISLIDEX_PLUGIN_BASENAME', 	plugin_basename(dirname(__FILE__)));
define ('ISLIDEX_PLUGIN_PATH', 		WP_PLUGIN_DIR		."/".ISLIDEX_PLUGIN_BASENAME);
define ('ISLIDEX_PLUGIN_URL', 		WP_PLUGIN_URL		."/".ISLIDEX_PLUGIN_BASENAME);
define ('ISLIDEX_PLUGIN_CSS', 		ISLIDEX_PLUGIN_URL	."/css");
define ('ISLIDEX_PLUGIN_JS', 		ISLIDEX_PLUGIN_URL	."/js");
define ('ISLIDEX_PLUGIN_IMAGES', 	ISLIDEX_PLUGIN_URL	."/img");
define ('ISLIDEX_PLUGIN_WIDGET', 	ISLIDEX_PLUGIN_URL	."/widget");
define ('ISLIDEX_PLUGIN_THEMES', 	ISLIDEX_PLUGIN_URL	."/themes");

// Add Menu in Administration area for configuration page
add_action('admin_menu', 'islidex_admin_actions');

// Admin actions
function islidex_admin_actions() {
  // Add options page
  $tp_option_page = add_options_page('iSlidex Settings', 'iSlidex', 'administrator', ISLIDEX_PLUGIN_BASENAME, 'islidex_options');

  // Register settings for plugin
  add_action('admin_init', 'islidex_options_init');
  // Add color picker script
  add_action("admin_print_scripts-$tp_option_page", 'islidex_admin_scripts');
}

// Include file containing options page form
function islidex_options() {
  include('islidex_options.php');
}

// Register settings for plugin
function islidex_options_init() {
  register_setting('islidex_options', 'islidex');
}

// Add script requried in admin options page
function islidex_admin_scripts() {
  wp_enqueue_script('farbtastic', ISLIDEX_PLUGIN_JS . '/farbtastic/farbtastic.js', 'jquery');
  echo '<link rel="stylesheet" href="' . ISLIDEX_PLUGIN_JS . '/farbtastic/farbtastic.css" type="text/css" />' . "\n";
  echo '<link rel="stylesheet" href="' . ISLIDEX_PLUGIN_CSS . '/admin.css" type="text/css" />' . "\n";
}

// Add settings link on plugin list page
function islidex_settings_link($links) {
  $settings_link = '<a href="options-general.php?page='.ISLIDEX_PLUGIN_BASENAME.'">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}
add_filter("plugin_action_links_".ISLIDEX_PLUGIN_BASENAME, 'islidex_settings_link' );

// iSlidex CSS 
		// For custom CSS either upload a islidex.css to your template folder or edit the one in the plugin css folder.
		// The template CSS would have the priority

function islidexcss() {
		$islidex_options = get_option('islidex');
		$applecss_path = ISLIDEX_PLUGIN_THEMES . '/apple/islidex_apple.css';
		$nivocss_path = ISLIDEX_PLUGIN_THEMES . '/nivo/islidex_nivo.css';
		$timecss_path = ISLIDEX_PLUGIN_THEMES . '/timeline/islidex_timeline.css';
		$greekcss_path = ISLIDEX_PLUGIN_THEMES . '/greek/islidex_greek.css';
		$timecss_path_ie6 = ISLIDEX_PLUGIN_THEMES . '/timeline/islidex_timeline_ie6.css';
		$css_path =  get_template_directory_uri() . '/islidex.css';

		if ( file_exists( TEMPLATEPATH . '/islidex.css') ){
			echo '<!-- iSlidex CSS Dependencies -->
			<link rel="stylesheet" type="text/css" href="'.$css_path.'" />';
		}
		if (($islidex_options['theme']) == 'Apple' || ($islidex_options['widget_theme']) == 'Apple') {
			echo '<!-- iSlidex CSS Dependencies -->
			<link rel="stylesheet" type="text/css" href="'.$applecss_path.'" />';
			echo '<!--[if lte IE 7]><style type="text/css" media="screen">
			#slides_menuc li, #slidesw_menu li, #slides_menuc li, .fbar {float:left;}
			</style><![endif]-->';
		}
		if (($islidex_options['theme']) == 'Nivo' || ($islidex_options['widget_theme']) == 'Nivo') {
			echo '<!-- iSlidex CSS Dependencies -->
			<link rel="stylesheet" type="text/css" href="'.$nivocss_path.'" />';
		}
		if (($islidex_options['theme']) == 'Timeline' || ($islidex_options['widget_theme']) == 'Timeline') {
			echo '<!-- iSlidex CSS Dependencies -->
			<link rel="stylesheet" type="text/css" href="'.$timecss_path.'" />';
			echo '<!--[if lte IE 6]>
			<link rel="stylesheet" type="text/css" href="'.$timecss_path_ie6.'" />
			<![endif]-->';
		}
		if (($islidex_options['theme']) == 'Greek' || ($islidex_options['widget_theme']) == 'Greek') {
			$greekfullwidth = $islidex_options['slide_size_w'] + 60;
			echo '<!-- iSlidex CSS Dependencies -->
			<link rel="stylesheet" type="text/css" href="'.$greekcss_path.'" />';
			echo '<style type="text/css" media="screen">
			.jcarousel-prev-horizontal, .jcarousel-next-horizontal {height:'.$islidex_options['slide_size_h'].'px;}
			.jcarousel-container-horizontal {width:'.$greekfullwidth.'px;}
			</style>';
			echo '<!--[if lte IE 6]><style type="text/css" media="screen">
			.postImgWrap{float:left;}
			#greek_theme{padding-right:30px;}
			</style><![endif]-->';
		}
	}
	
	/* the jQuery */
	function loadjquery() {
	//wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js');
    wp_enqueue_script('jquery');
	}

	/* the iSlidex JS */
	function islidexjs() {
		$islidex_options = get_option('islidex');
		$islidexjs_path = ISLIDEX_PLUGIN_THEMES . '/apple/apple.js';
		$nivo_path = ISLIDEX_PLUGIN_THEMES . '/nivo/nivo.js';
		$time_path = ISLIDEX_PLUGIN_THEMES . '/timeline/timeline.js';
		$applewjs_path = ISLIDEX_PLUGIN_WIDGET . '/apple_w.js';
		$nivowjs_path = ISLIDEX_PLUGIN_WIDGET . '/nivo_w.js';
		$greek_path = ISLIDEX_PLUGIN_THEMES . '/greek/greek.js';
		$captify_path = ISLIDEX_PLUGIN_JS . '/captify.tiny.js';
		//$nivocjs_path = ISLIDEX_PLUGIN_WIDGET . '/nivo_c.js';
		//$applecjs_path = ISLIDEX_PLUGIN_WIDGET . '/apple_c.js';
		$slider_width = ($islidex_options['slide_size_w']);

		// Get the captions ready or not
		if (($islidex_options['usecaption']) == 1) { $cap = "true";
		} else {
		$cap = "false";
		}
		// Check which js to load depending on the theme

		if (($islidex_options['theme']) == 'Apple') {
		echo '<script src="'.$nivo_path.'" type="text/javascript"></script>
		<script type="text/javascript" src="'.$islidexjs_path.'"></script>'; 
		}			

		if (($islidex_options['theme']) == 'Nivo') {
			
			if (($islidex_options['nivo_auto']) == 1) { $nivo_auto = "true";
			} else {
			$nivo_auto = "false";
			}

			echo '<script type="text/javascript" src="'.$nivo_path.'"></script> 
			<script type="text/javascript">
			//$.noConflict();
			jQuery.noConflict(); if (typeof(window.$) === \'undefined\') { window.$ = jQuery; }
				jQuery(window).load(function() {
				jQuery("#slider").nivoSlider({
				caption:'.$cap.', // Added this option, as not everyone likes captions
				effect: "'.$islidex_options['nivoeffect'].'",
				slices:'.$islidex_options['num_post'].',
				animSpeed:'.$islidex_options['nivo_transpeed'].',
				pauseTime:'.$islidex_options['nivo_pausetime'].',
			//	startSlide:0, //Set starting Slide (0 index)
				directionNav:false, //Next & Prev
				directionNavHide:true, //Only show on hover
			//	controlNav:true, //1,2,3...
			//	controlNavThumbs:false, //Use thumbnails for Control Nav
			//	controlNavThumbsSearch: ".jpg", //Replace this with...
			//	controlNavThumbsReplace: "_thumb.jpg", //...this in thumb Image src
			//	keyboardNav:true, //Use left & right arrows
				pauseOnHover:true, //Stop animation while hovering
				manualAdvance:'.$nivo_auto.', //Force manual transitions
				captionOpacity:0.8 //Universal caption opacity
			//	beforeChange: function(){},
			//	afterChange: function(){},
			//	slideshowEnd: function(){} //Triggers after all slides have been shown
				});
			});
			</script>';
		}

		if (($islidex_options['theme']) == 'Timeline') {
		echo '<script src="'.$time_path.'" type="text/javascript"></script>'; 
		}
		
		if (($islidex_options['theme']) == 'Greek') {
		echo '<script src="'.$greek_path.'" type="text/javascript"></script>';
		echo '<script type="text/javascript">
			jQuery(document).ready(function() {
			jQuery("#mycarousel").jcarousel();
			});
			</script>';
		}

		// Captify
		if (($islidex_options['usecaption']) == 1) {
			echo '<script type="text/javascript" src="'.$captify_path.'"></script>';
			echo '
			<script type="text/javascript">
				$.noConflict();
					jQuery(function(){ jQuery("img.captify").captify({}); });
			</script>';
		}

		$applewjs_path = ISLIDEX_PLUGIN_WIDGET . '/apple_w.js';
		$nivowjs_path = ISLIDEX_PLUGIN_WIDGET . '/nivo_w.js';

		if (($islidex_options['widget_theme']) == 'Apple') {
		echo '<script type="text/javascript" src="'.$applewjs_path.'"></script>';
		} elseif (($islidex_options['widget_theme']) == 'Nivo') {
		echo '
		<script type="text/javascript" src="'.$nivowjs_path.'"></script>
		<script type="text/javascript">
			//$.noConflict();
			jQuery.noConflict(); if (typeof(window.$) === \'undefined\') { window.$ = jQuery; } 
				jQuery(window).load(function() {
				jQuery("#sliderw").nivoSliderw({
				caption:'.$cap.', // Added this option, as not everyone likes captions
				effect: "'.$islidex_options['wnivoeffect'].'",
				slices:'.$islidex_options['widget_num_post'].',
				animSpeed:'.$islidex_options['nivo_transpeed'].',
				pauseTime:'.$islidex_options['nivo_pausetime'].',
			//	startSlide:0, //Set starting Slide (0 index)
				directionNav:false, //Next & Prev
				directionNavHide:true, //Only show on hover
			//	controlNav:true, //1,2,3...
			//	controlNavThumbs:false, //Use thumbnails for Control Nav
			//	controlNavThumbsSearch: ".jpg", //Replace this with...
			//	controlNavThumbsReplace: "_thumb.jpg", //...this in thumb Image src
			//	keyboardNav:true, //Use left & right arrows
				pauseOnHover:true, //Stop animation while hovering
				manualAdvance:'.$nivo_auto.', //Force manual transitions
				captionOpacity:0.8 //Universal caption opacity
			//	beforeChange: function(){},
			//	afterChange: function(){},
			//	slideshowEnd: function(){} //Triggers after all slides have been shown
				});
			});
			</script>';
		}
}

// THE HOOKS

	/* add header hook for CSS */
	add_action('wp_head', 'islidexcss');
	/* add header hook for jQuery */
	add_action('init', 'loadjquery');
	/* add footer hook for iSlidex JS */

// THUMBS APPLE ISLIDEX
	
function islidex_thumb() {

	$islidex_options = get_option('islidex');
	$numpost = $islidex_options['num_post'];
	$catid = $islidex_options['category_id'];
	$timthumb_path = ISLIDEX_PLUGIN_JS . '/timthumb.php';
	$slideposts = get_posts('numberposts='.$numpost.'&cat='.$catid.'');
	foreach($slideposts as $islidex_thumbs) {
		$key1 = "islidex_thumb"; 
		$thumb = get_post_meta($islidex_thumbs->ID, $key1, true);
		$title = __($islidex_thumbs->post_title);
		$attachments = get_children( array('post_parent' => $islidex_thumbs->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_thumbs->ID)) {
			$image_id = get_post_thumbnail_id($islidex_thumbs->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '<li class="menuItem"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$feat[0].'&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>'; // the featured image
		} elseif ($thumb == true) { //in case you want your own thumb image (indipendent from the featured image or post image)
			echo '<li class="menuItem"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$thumb.'&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
		} elseif ($attachments == true) { //if you simply want islidex to get a random image you uploaded in the post
			foreach($attachments as $id => $attachment) {
				$img = wp_get_attachment_image_src($id, 'full');
				$img_url = parse_url($img[0], PHP_URL_PATH);
				print '<li class="menuItem"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
			}
		} else {
		print '<li class="menuItem"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_small.png&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
		}
	} wp_reset_query();
} /* end of islidex thumbs function */

// THUMBS TIMELINE ISLIDEX

function islidex_timethumb() {

	$islidex_options = get_option('islidex');
	$numpost = $islidex_options['num_post'];
	$catid = $islidex_options['category_id'];
	$timthumb_path = ISLIDEX_PLUGIN_JS . '/timthumb.php';
	$slideposts = get_posts('numberposts='.$numpost.'&cat='.$catid.'');
	foreach($slideposts as $islidex_thumbs) {
		$key1 = "islidex_thumb"; 
		$thumb = get_post_meta($islidex_thumbs->ID, $key1, true);
		$title = __($islidex_thumbs->post_title);
		$attachments = get_children( array('post_parent' => $islidex_thumbs->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_thumbs->ID)) {
			$image_id = get_post_thumbnail_id($islidex_thumbs->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '<li class="timeItem"><a href="">'.$title.'</a></li>'; // the featured image
		} elseif ($thumb == true) { //in case you want your own thumb image (indipendent from the featured image or post image)
			echo '<li class="timeItem"><a href="">'.$title.'</a></li>';
		} elseif ($attachments == true) { //if you simply want islidex to get a random image you uploaded in the post
			foreach($attachments as $id => $attachment) {
				$img = wp_get_attachment_image_src($id, 'full');
				$img_url = parse_url($img[0], PHP_URL_PATH);
				print '<li class="timeItem"><a href="">'.$title.'</a></li>';
			}
		} else {
		print '<li class="timeItem"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_small.png&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
		}
	} wp_reset_query();
} /* end of islidex timeline thumbs function */

	 // ISLIDEX

function show_islidex() {
	
	add_action('wp_footer', 'islidexjs');
	global $post;
	$islidex_options = get_option('islidex');
	$numpost = $islidex_options['num_post'];
	$catid = $islidex_options['category_id'];
	$timthumb_path = ISLIDEX_PLUGIN_JS . '/timthumb.php';

	if (($islidex_options['theme']) == 'Apple') {  // THEME 1 - APPLE ?>
	<div class="gallery" id="gallery" style="min-height:<?php echo $islidex_options['slide_size_h'] ?>px;min-width:<?php echo $islidex_options['slide_size_w'] ?>px;width:<?php echo $islidex_options['slide_size_w'] ?>px;">
	<div id="slides" style="height:<?php echo $islidex_options['slide_size_h'] ?>px;">
	
	<?php
	$slideposts = get_posts('numberposts='.$numpost.'&cat='.$catid.'');
	foreach($slideposts as $islidex_post) {
		$key1 = "islidex_slide"; //in case you want your own slide image and not taken from the post attachment
		$slide = get_post_meta($islidex_post->ID, $key1, true);
		$title = __($islidex_post->post_title);
		$attachments = get_children( array('post_parent' => $islidex_post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_post->ID)) {
			$image_id = get_post_thumbnail_id($islidex_post->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '<div class="slide">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.$feat[0].'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>'; // the featured image
		} elseif ($slide == true) {
			echo '<div class="slide">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.$slide.'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>';
		} else if ($attachments == true) {
			foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$img_url = parse_url($img[0], PHP_URL_PATH);
			print '<div class="slide">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>';
			}
		} else {
		print '<div class="slide" style="height: '.$islidex_options['slide_size_h'].'px; width: '.$islidex_options['slide_size_w'].'px;">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_big.png&amp;w=250&amp;h=250&amp;zc=0&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" style="padding-top:15%;" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>';
		}
	} 
	wp_reset_query(); ?>
	</div>
	<div id="slides_menu">
	<ul>
	<li class="fbar">&nbsp;</li>
	<?php islidex_thumb(); ?>
	</ul>
	</div>
</div>

	<?php // THEME 2 - NIVO

	} elseif (($islidex_options['theme']) == 'Nivo') { ?>

	<div id="slider" style="min-height:<?php echo $islidex_options['slide_size_h'] ?>px;min-width:<?php echo $islidex_options['slide_size_w'] ?>px;width:<?php echo $islidex_options['slide_size_w'] ?>px;height:<?php echo $islidex_options['slide_size_h'] ?>px;">
	
	<?php
	$slideposts = get_posts('numberposts='.$numpost.'&cat='.$catid.'');
	foreach($slideposts as $islidex_post) {
		$key1 = "islidex_slide"; //in case you want your own slide image and not taken from the post attachment
		$slide = get_post_meta($islidex_post->ID, $key1, true);
		$title = __($islidex_post->post_title);
		$attachments = get_children( array('post_parent' => $islidex_post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_post->ID)) {
			$image_id = get_post_thumbnail_id($islidex_post->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img src="'.$timthumb_path.'?src='.$feat[0].'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif; // the featured image
		} elseif ($slide == true) {
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img src="'.$timthumb_path.'?src='.$slide.'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
		} else if ($attachments == true) {
			foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$img_url = parse_url($img[0], PHP_URL_PATH);
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			print '<img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			}
		} else {
		if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
		print '<img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_big.png&amp;w=250&amp;h=250&amp;zc=0&amp;q=100" alt="'.$title.'" title="'.$title.'" style="padding-top:15%;" />';
		if (($islidex_options['linked']) == 1): echo '</a>'; endif;
		}
	} 
	wp_reset_query(); ?>
	</div>
	
	<?php // THEME 3 - PIECEMAKER
	
	} elseif (($islidex_options['theme']) == 'Piecemaker') {
	
	$piece_xml = ISLIDEX_PLUGIN_THEMES . '/piecemaker/piecemakerXML.php'; 
	$piece_css = ISLIDEX_PLUGIN_THEMES . '/piecemaker/islidex_piecemaker.css';
	if (($islidex_options['piece_shadow']) == 1) {
	$piece_swf = ISLIDEX_PLUGIN_THEMES . '/piecemaker/piecemaker.swf';
	} else {
	$piece_swf = ISLIDEX_PLUGIN_THEMES . '/piecemaker/piecemakerNoShadow.swf';
	}
	$swf_exp = ISLIDEX_PLUGIN_JS . '/swfobject/expressInstall.swf';
	$swfo_path = ISLIDEX_PLUGIN_JS . '/swfobject/swfobject.js';
	$piece_prep = '<script type="text/javascript" src="'.$swfo_path.'"></script>
	<script type="text/javascript">
			var flashvars = {};
			flashvars.xmlSource = "'.$piece_xml.'";
			flashvars.cssSource = "'.$piece_css.'";
			flashvars.salign = "l";
			var attributes = {};
			attributes.wmode = "transparent";
			attributes.align = "middle";
			swfobject.embedSWF("'.$piece_swf.'", "flashcontent", "'.($islidex_options['slide_size_w']+50).'", "'.($islidex_options['slide_size_h']+100).'", "10", "'.$swf_exp.'", flashvars, attributes);
	</script>'; ?>
	<div class="mypiecemaker" style="width:<?php ($islidex_options['slide_size_w']+50) ?>px;height:<?php ($islidex_options['slide_size_h']+100) ?>px;vertical-align:middle;text-align:center;margin:0;padding-top:15px;valign:middle;align:middle;display:block;">
	<div id="flashcontent">
		<p>You need to <a href="http://www.adobe.com/products/flashplayer/" target="_blank">upgrade your Flash Player</a> to version 10 or newer.</p>
	</div><!-- end flashcontent -->
	</div>

<?php echo $piece_prep; // end of Piecemaker

// THEME 4 - TIMELINE

} elseif (($islidex_options['theme']) == 'Timeline') { ?>
	<div class="timeline" id="timeline" style="min-height:<?php echo $islidex_options['slide_size_h'] ?>px;min-width:<?php echo $islidex_options['slide_size_w'] ?>px;width:<?php echo $islidex_options['slide_size_w'] ?>px;">
	<div id="times" style="height:<?php echo $islidex_options['slide_size_h'] ?>px;">
	
	<?php
	$slideposts = get_posts('numberposts='.$numpost.'&cat='.$catid.'');
	foreach($slideposts as $islidex_post) {
		$key1 = "islidex_slide"; //in case you want your own time image and not taken from the post attachment
		$time = get_post_meta($islidex_post->ID, $key1, true);
		$title = __($islidex_post->post_title);
		$attachments = get_children( array('post_parent' => $islidex_post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_post->ID)) {
			$image_id = get_post_thumbnail_id($islidex_post->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '<div class="time">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.$feat[0].'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>'; // the featured image
		} elseif ($time == true) {
			echo '<div class="time"><img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.$time.'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" /></div>';
		} else if ($attachments == true) {
			foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$img_url = parse_url($img[0], PHP_URL_PATH);
			print '<div class="time">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			print '<img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>';
			}
		} else {
		print '<div class="time" style="height: '.$islidex_options['slide_size_h'].'px; width: '.$islidex_options['slide_size_w'].'px;">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			print '<img width="'.$islidex_options['slide_size_w'].'" height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_big.png&amp;w=250&amp;h=250&amp;zc=0&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" style="padding-top:15%;" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>';
		}
	} 
	wp_reset_query(); ?>
	</div>
	<div id="times_menu">
	<ul style="width:<?php echo $islidex_options['slide_size_w']; ?>px;">
	<?php islidex_timethumb(); ?>
	</ul>
	</div>
</div>

<?php } // end of Timeline

// THEME 5 - GREEK

elseif (($islidex_options['theme']) == 'Greek') { 

// Custom lenght Excerpt
function excerpt($the_id, $num){
global $wpdb;
$query = 'SELECT post_content FROM '. $wpdb->posts .' WHERE ID = '. $the_id .' LIMIT 1';
$result = $wpdb->get_results($query, ARRAY_A);
$post_excerpt=$result[0]['post_content'];
$post_excerpt = preg_replace("/<img(.*?)>/si", "", $post_excerpt);
/*$bad_tags = array 
(        
  "<strong(.*)>", 
  "<\/strong>", 
  );
$post_excerpt = preg_replace($bad_tags,'',$post_excerpt);  */
$post_excerpt = strip_tags($post_excerpt);
strip_shortcodes($post_excerpt);
$limit = $num+1;
$excerpt = explode(' ', $post_excerpt, $limit);
array_pop($excerpt);
$excerpt = implode(" ",$excerpt)."&hellip;";
echo '<p class="postDetail">'.$excerpt.'</p>';
}

// Custom width for the Greek
$greek_width = $islidex_options['greekslidew'];
?>
<div id="greek_theme" style="width:<?php echo $islidex_options['slide_size_w'] + 60; ?>px;height:<?php echo $islidex_options['slide_size_h']; ?>px;">
	<ul id="mycarousel">
		<?php
	$slideposts = get_posts('numberposts='.$numpost.'&cat='.$catid.'');
	foreach($slideposts as $islidex_post) {
		$the_id = $islidex_post->ID;
		$key1 = "islidex_slide"; //in case you want your own time image and not taken from the post attachment
		$greek = get_post_meta($islidex_post->ID, $key1, true);
		$title = __($islidex_post->post_title);
		$attachments = get_children( array('post_parent' => $islidex_post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_post->ID)) {
			$image_id = get_post_thumbnail_id($islidex_post->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '<li><div class="postImgWrap" style="height:'.$islidex_options['slide_size_h'].'px">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img src="'.$timthumb_path.'?src='.$feat[0].'&amp;w='.$greek_width.'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo'<div class="postDesc" style="width:'.$greek_width.'px;">';
			echo'<p class="postTitle">'.$title.'</p>';
			echo'<p class="postDetail">'.excerpt($the_id,10).'</p>';
			echo'<a href="'.get_permalink($islidex_post->ID).'" class="postLink">Read More <span>&raquo;</span></a>';
			echo'</div></div></li>';// the featured image
		} elseif ($greek == true) {
			echo '<li><div class="postImgWrap" style="height:'.$islidex_options['slide_size_h'].'px">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img  src="'.$timthumb_path.'?src='.$greek.'&amp;w='.$greek_width.'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo'<div class="postDesc" style="width:'.$greek_width.'px;">';
			echo'<p class="postTitle">'.$title.'</p>';
			echo'<p class="postDetail">'.excerpt($the_id,10).'</p>';
			echo'<a href="'.get_permalink($islidex_post->ID).'" class="postLink">Read More <span>&raquo;</span></a>';
			echo'</div></div></li>';
		} else if ($attachments == true) {
			foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$img_url = parse_url($img[0], PHP_URL_PATH);
			print '<li><div class="postImgWrap" style="height:'.$islidex_options['slide_size_h'].'px">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			print '<img  src="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$greek_width.'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo'<div class="postDesc" style="width:'.$greek_width.'px;">';
			echo'<p class="postTitle">'.$title.'</p>';
			echo'<p class="postDetail">'.excerpt($the_id,10).'</p>';
			echo'<a href="'.get_permalink($islidex_post->ID).'" class="postLink">Read More <span>&raquo;</span></a>';
			echo'</div></div></li>';
			}
		} else {
			print '<li><div class="postImgWrap" style="height:'.$islidex_options['slide_size_h'].'px">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			print '<img height="'.$islidex_options['slide_size_h'].'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_big.png&amp;w='.$greek_width.'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo'<div class="postDesc" style="width:'.$greek_width.'px;">';
			echo'<p class="postTitle">'.$title.'</p>';
			echo'<p class="postDetail">'.excerpt($the_id,10).'</p>';
			echo'<a href="'.get_permalink($islidex_post->ID).'" class="postLink">Read More <span>&raquo;</span></a>';
			echo'</div></div></li>';
		}
	} 
	//wp_reset_query(); ?>
	</ul>
</div>

<?php } // end of Greek 

 } // end of iSlidex main function

// Shortcode

add_shortcode('islidex', 'show_islidex'); 

 // CUSTOM ISLIDEX

function custom_apple_js() {
 
	$applecjs_path = ISLIDEX_PLUGIN_WIDGET . '/apple_c.js';
	
	echo '
	<script type="text/javascript" src="'.$applecjs_path.'"></script>
	';
}

function custom_apple_css() {
 
	$applecss_path = ISLIDEX_PLUGIN_THEMES . '/apple/islidex_apple.css';
	
	echo '
	<link rel="stylesheet" type="text/css" href="'.$applecss_path.'" />
	';
}

function custom_nivo_css() {

	$nivocss_path = ISLIDEX_PLUGIN_THEMES . '/nivo/islidex_nivo.css';

	echo '
	<link rel="stylesheet" type="text/css" href="'.$nivocss_path.'" />
	';
}

function custom_nivo_js() { // BUGGY
 
	$nivocjs_path = ISLIDEX_PLUGIN_WIDGET . '/nivo_c.js';
	$islidex_options = get_option('islidex');
	// Get the captions ready or not
	if (($islidex_options['usecaption']) == 1) { $cap = "true";
		} else {
		$cap = "false";
		}
	
	echo '
	<script type="text/javascript" src="'.$nivocjs_path.'"></script>
	<script type="text/javascript">
			//$.noConflict();
			jQuery.noConflict(); if (typeof(window.$) === \'undefined\') { window.$ = jQuery; }
				jQuery(window).load(function() {
				jQuery("#sliderc").nivoSliderc({
				caption:'.$cap.', // Added this option, as not everyone likes captions
				effect: "'.$islidex_options['wnivoeffect'].'",
				slices:'.$islidex_options['widget_num_post'].',
				animSpeed:'.$islidex_options['nivo_transpeed'].',
				pauseTime:'.$islidex_options['nivo_pausetime'].',
			//	startSlide:0, //Set starting Slide (0 index)
				directionNav:false, //Next & Prev
				directionNavHide:true, //Only show on hover
			//	controlNav:true, //1,2,3...
			//	controlNavThumbs:false, //Use thumbnails for Control Nav
			//	controlNavThumbsSearch: ".jpg", //Replace this with...
			//	controlNavThumbsReplace: "_thumb.jpg", //...this in thumb Image src
			//	keyboardNav:true, //Use left & right arrows
				pauseOnHover:true, //Stop animation while hovering
				manualAdvance:'.$auto_adv.', //Force manual transitions
				captionOpacity:0.8 //Universal caption opacity
			//	beforeChange: function(){},
			//	afterChange: function(){},
			//	slideshowEnd: function(){} //Triggers after all slides have been shown
				});
			});
			</script>
			';
}

function custom_timeline_js() {
 
	$timelinecjs_path = ISLIDEX_PLUGIN_WIDGET . '/timeline_c.js';
	
	echo '
	<script type="text/javascript" src="'.$timelinecjs_path.'"></script>
	';
}

function custom_timeline_css() {
 
	$timelinecss_path = ISLIDEX_PLUGIN_THEMES . '/timeline/islidex_timeline.css';
	
	echo '
	<link rel="stylesheet" type="text/css" href="'.$timelinecss_path.'" />
	';
}

function show_customislidex ($customcatid,$customnumpost,$width,$height,$customtheme) {

	//add_action('wp_footer', 'islidexjs');
	
	$islidex_options = get_option('islidex');
	$timthumb_path = ISLIDEX_PLUGIN_JS . '/timthumb.php';

	if ($customtheme == 1) {  // THEME 1 - APPLE 
	
	add_action('wp_footer', 'custom_apple_js');

	if (($islidex_options['theme']) !== 'Apple' || ($islidex_options['widget_theme']) !== 'Apple') {

	add_action('wp_footer', 'custom_apple_css');

	} 

	?>
	
	<div class="gallery" id="gallery" style="min-height:<?php echo $height ?>px;min-width:<?php echo $width ?>px;width:<?php echo $width ?>px;">
	<div id="slidesc" style="height:<?php echo $height ?>px;">
	
	<?php
	$slideposts = get_posts('numberposts='.$customnumpost.'&cat='.$customcatid.'');
	foreach($slideposts as $islidex_post) {
		$key1 = "islidex_slide"; //in case you want your own slide image and not taken from the post attachment
		$slide = get_post_meta($islidex_post->ID, $key1, true);
		$title = __($islidex_post->post_title);
		$attachments = get_children( array('post_parent' => $islidex_post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_post->ID)) {
			$image_id = get_post_thumbnail_id($islidex_post->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '<div class="slidec">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.$feat[0].'&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>'; // the featured image
		} elseif ($slide == true) {
			echo '<div class="slidec">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.$slide.'&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>';
		} else if ($attachments == true) {
			foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$img_url = parse_url($img[0], PHP_URL_PATH);
			print '<div class="slidec">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			print '<img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>';
			}
		} else {
		print '<div class="slidec" style="height: '.$height.'px; width: '.$width.'px;">';
		if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
		print '<img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_big.png&amp;w=250&amp;h=250&amp;zc=0&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" style="padding-top:15%;" />';
		if (($islidex_options['linked']) == 1): echo '</a>'; endif;
		echo '</div>';
		}
	} 
	wp_reset_query(); ?>
	</div>
	<div id="slides_menuc">
	<ul>
	<li class="fbar">&nbsp;</li>
	<?php
	$slideposts = get_posts('numberposts='.$customnumpost.'&cat='.$customcatid.'');
	foreach($slideposts as $islidex_thumbs) {
		$key1 = "islidex_thumb"; 
		$thumb = get_post_meta($islidex_thumbs->ID, $key1, true);
		$title = __($islidex_thumbs->post_title);
		$attachments = get_children( array('post_parent' => $islidex_thumbs->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_thumbs->ID)) {
			$image_id = get_post_thumbnail_id($islidex_thumbs->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '<li class="menuItemc"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$feat[0].'&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>'; // the featured image
		} elseif ($thumb == true) { //in case you want your own thumb image (indipendent from the featured image or post image)
			echo '<li class="menuItemc"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$thumb.'&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
		} elseif ($attachments == true) { //if you simply want islidex to get a random image you uploaded in the post
			foreach($attachments as $id => $attachment) {
				$img = wp_get_attachment_image_src($id, 'full');
				$img_url = parse_url($img[0], PHP_URL_PATH);
				print '<li class="menuItemc"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
			}
		} else {
		print '<li class="menuItemc"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_small.png&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
		}
	} wp_reset_query();
	?>
	</ul>
	</div>
</div>

	<?php // THEME 2 - NIVO

	} elseif ($customtheme == 2) {

	add_action('wp_footer', 'custom_nivo_js');
	
	if (($islidex_options['theme']) !== 'Nivo' || ($islidex_options['widget_theme']) !== 'Nivo') {

	add_action('wp_footer', 'custom_nivo_css');

	} 

	?>
	
	<div id="sliderc" style="min-height:<?php echo $height; ?>px;min-width:<?php echo $width; ?>px;width:<?php echo $width; ?>px;height:<?php echo $height; ?>px;">
	
	<?php
	$slideposts = get_posts('numberposts='.$customnumpost.'&cat='.$customcatid.'');
	foreach($slideposts as $islidex_post) {
		$key1 = "islidex_slide"; //in case you want your own slide image and not taken from the post attachment
		$slide = get_post_meta($islidex_post->ID, $key1, true);
		$title = __($islidex_post->post_title);
		$attachments = get_children( array('post_parent' => $islidex_post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_post->ID)) {
			$image_id = get_post_thumbnail_id($islidex_post->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img src="'.$timthumb_path.'?src='.$feat[0].'&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif; // the featured image
		} elseif ($slide == true) {
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img src="'.$timthumb_path.'?src='.$slide.'&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
		} else if ($attachments == true) {
			foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$img_url = parse_url($img[0], PHP_URL_PATH);
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			print '<img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			}
		} else {
		if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
		print '<img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_big.png&amp;w=250&amp;h=250&amp;zc=0&amp;q=100" alt="'.$title.'" title="'.$title.'" style="padding-top:15%;" />';
		if (($islidex_options['linked']) == 1): echo '</a>'; endif;
		}
	} 
	wp_reset_query(); ?>
	</div>
	
	<?php // THEME 3 - PIECEMAKER
	
	} elseif ($customtheme == 3) {
	
	$piece_xml = ISLIDEX_PLUGIN_THEMES . '/piecemaker/piecemakerXML.php'; 
	$piece_css = ISLIDEX_PLUGIN_THEMES . '/piecemaker/islidex_piecemaker.css';
	if (($islidex_options['piece_shadow']) == 1) {
	$piecec_swf = ISLIDEX_PLUGIN_THEMES . '/piecemaker/piecemakerc.swf';
	} else {
	$piecec_swf = ISLIDEX_PLUGIN_THEMES . '/piecemaker/piecemakerNoShadowc.swf';
	}
	$swf_exp = ISLIDEX_PLUGIN_JS . '/swfobject/expressInstall.swf';
	$swfo_path = ISLIDEX_PLUGIN_JS . '/swfobject/swfobject.js';
	$piece_prep = '<script type="text/javascript" src="'.$swfo_path.'"></script>
	<script type="text/javascript">
			var flashvars = {};
			flashvars.xmlSource = "'.$piece_xml.'";
			flashvars.cssSource = "'.$piece_css.'";
			flashvars.salign = "l";
			var attributes = {};
			attributes.wmode = "transparent";
			attributes.align = "middle";
			swfobject.embedSWF("'.$piecec_swf.'", "flashcontentc", "'.($width+50).'", "'.($height+180).'", "10", "'.$swf_exp.'", flashvars, attributes);
	</script>'; ?>
	<div class="mycustompiecemaker" style="width:<?php echo ($width+50) ?>px;height:<?php echo ($height+100) ?>px;vertical-align:middle;text-align:center;margin:0;padding-top:25px;valign:middle;align:middle;display:block;">
	<div id="flashcontentc">
		<p>You need to <a href="http://www.adobe.com/products/flashplayer/" target="_blank">upgrade your Flash Player</a> to version 10 or newer.</p>
	</div><!-- end flashcontent -->
	</div>

<?php echo $piece_prep; 

	  // THEME 4 - TIMELINE

	} elseif ($customtheme == 4) { 
		add_action('wp_footer', 'custom_timeline_js');
		if (($islidex_options['theme']) !== 'Timeline' || ($islidex_options['widget_theme']) !== 'Timeline') {
		add_action('wp_footer', 'custom_timeline_css');
		} 
	?>
	<div class="timelinec" id="timelinec" style="min-height:<?php echo $height; ?>px;min-width:<?php echo $width; ?>px;width:<?php echo $width; ?>px;">
	<div id="timesc" style="height:<?php echo $height; ?>px;">
	
	<?php
	$slideposts = get_posts('numberposts='.$customnumpost.'&cat='.$customcatid.'');
	foreach($slideposts as $islidex_post) {
		$key1 = "islidex_slide"; //in case you want your own time image and not taken from the post attachment
		$time = get_post_meta($islidex_post->ID, $key1, true);
		$title = __($islidex_post->post_title);
		$attachments = get_children( array('post_parent' => $islidex_post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_post->ID)) {
			$image_id = get_post_thumbnail_id($islidex_post->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '<div class="timec">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			echo '<img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.$feat[0].'&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>'; // the featured image
		} elseif ($time == true) {
			echo '<div class="timec"><img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.$time.'&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" /></div>';
		} else if ($attachments == true) {
			foreach($attachments as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$img_url = parse_url($img[0], PHP_URL_PATH);
			print '<div class="timec">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			print '<img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$width.'&amp;h='.$height.'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>';
			}
		} else {
		print '<div class="timec" style="height: '.$height.'px; width: '.$width.'px;">';
			if (($islidex_options['linked']) == 1): echo '<a href="'.get_permalink($islidex_post->ID).'">'; endif;
			print '<img width="'.$width.'" height="'.$height.'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_big.png&amp;w=250&amp;h=250&amp;zc=0&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" style="padding-top:15%;" />';
			if (($islidex_options['linked']) == 1): echo '</a>'; endif;
			echo '</div>';
		}
	} 
	wp_reset_query(); ?>
	</div>
	<div id="times_menuc">
		<ul style="width:<?php echo $width; ?>px;">
			<?php
			$slideposts = get_posts('numberposts='.$customnumpost.'&cat='.$customcatid.'');
			foreach($slideposts as $islidex_thumbs) {
				$key1 = "islidex_thumb"; 
				$thumb = get_post_meta($islidex_thumbs->ID, $key1, true);
				$title = __($islidex_thumbs->post_title);
				$attachments = get_children( array('post_parent' => $islidex_thumbs->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
				$islidex_options = get_option('islidex');
				if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_thumbs->ID)) {
					$image_id = get_post_thumbnail_id($islidex_thumbs->ID);
					$feat = wp_get_attachment_image_src($image_id,'large', true);
					echo '<li class="timeItemc"><a href="">'.$title.'</a></li>'; // the featured image
				} elseif ($thumb == true) { //in case you want your own thumb image (indipendent from the featured image or post image)
					echo '<li class="timeItemc"><a href="">'.$title.'</a></li>';
				} elseif ($attachments == true) { //if you simply want islidex to get a random image you uploaded in the post
					foreach($attachments as $id => $attachment) {
						$img = wp_get_attachment_image_src($id, 'full');
						$img_url = parse_url($img[0], PHP_URL_PATH);
						print '<li class="timeItemc"><a href="">'.$title.'</a></li>';
					}
				} else {
				print '<li class="timeItemc"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_small.png&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
				}
			} wp_reset_query();?>
		</ul>
	</div>
</div>

<?php } // end of Timeline

} // end of iSlidex custom function

	// Shortcode

global $customtheme;

function custom_islidex_params($atts) {
	extract(shortcode_atts(array(
		'cat' => '1',
		'num' => '5',
		'w' => '490',
		'h' => '260',
		'theme' => '1',
		'auto' => true,
		//'cap' => 'off',
	), $atts));
	$cat = $atts['cat'];
	$num = $atts['num'];
	$w = $atts['w'];
	$h = $atts['h'];
	$customtheme = $atts['theme'];
	$auto_adv = $atts['auto'];
	
	return show_customislidex($cat,$num,$w,$h,$customtheme,$auto_adv);

	return $customtheme;
	global $customtheme;
	
	//DEBUG

	/*$apple_theme = '1';
	$nivo_theme = '2';
	$piecemaker_theme = '3';

	if ($customtheme == 1) {
	return $apple_theme;
	} elseif ($customtheme == 2) {
	return $nivo_theme;
	} elseif ($customtheme == 3) {
	return $piecemaker_theme;
	} else {}*/
}

add_shortcode('islidex_custom', 'custom_islidex_params');

//echo $customtheme; // DEBUG

 // WIDGET ISLIDEX

/* the widget function */

function islidex_thumb_widget() {
	
	$islidex_options = get_option('islidex');
	$timthumb_path = ISLIDEX_PLUGIN_JS . '/timthumb.php';
	$widgcat = $islidex_options['widget_cat'];
	$widgnum = $islidex_options['widget_num_post'];
	
	$slideposts = get_posts('numberposts='.$widgnum.'&cat='.$widgcat.'');
	foreach($slideposts as $islidex_widget_thumb) {
		$key1 = "islidex_thumb"; 
		$thumb = get_post_meta($islidex_widget_thumb->ID, $key1, true);
		$title = __($islidex_widget_thumb->post_title);
		$attachments = get_children( array('post_parent' => $islidex_widget_thumb->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_widget_thumb->ID)) {
			$image_id = get_post_thumbnail_id($islidex_widget_thumb->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '<li class="menuItemw"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$feat[0].'&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>'; // the featured image
		} elseif ($thumb == true) { //in case you want your own thumb image (indipendent from the featured image or post image)
			echo '<li class="menuItemw"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$thumb.'&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
		} elseif ($attachments == true) { //if you simply want islidex to get a random image you uploaded in the post
			foreach($attachments as $id => $attachment) {
				$img = wp_get_attachment_image_src($id, 'full');
				$img_url = parse_url($img[0], PHP_URL_PATH);
				print '<li class="menuItemw"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
			}
		} else {
		print '<li class="menuItemw"><a href=""><img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_small.png&amp;w=32&amp;h=32&amp;zc=0&amp;q=100" /></a></li>';
		}
	} wp_reset_query();
} /* end of islidex thumbs function */

// show it to me
 
if (!class_exists("islidex_widget")) {
 
	class islidex_widget extends WP_Widget {
 
		function islidex_widget() {
			$widget_ops = array('classname' => 'islidex_widget', 'description' => 'Display iSlidex in a Sidebar' );
			$this->WP_Widget('islidex_new_widget', 'iSlidex', $widget_ops);
		}
 
		// Setup

		function widget($args, $instance) {

			add_action('wp_footer', 'islidexjs');

			extract($args, EXTR_SKIP);
			$islidex_options = get_option('islidex');
			$timthumb_path = ISLIDEX_PLUGIN_JS . '/timthumb.php';
			$widgcat = $islidex_options['widget_cat'];
			$widgw = $islidex_options['widget_size_w'];
			$widgh = $islidex_options['widget_size_h'];
			$widgnum = $islidex_options['widget_num_post'];
			add_action('wp_footer', 'islidexjs');

			echo $before_widget;
			$title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
 
			if (!empty($title)) { 
				echo $before_title . $title . $after_title; 
			}
			
			if (($islidex_options['widget_theme']) == 'Apple') { // APPLE WIDGET THEME ?>
		
			<div class="gallery" id="gallery" style="width:<?php echo $islidex_options['widget_size_w']; ?>px;">
				<div id="slidesw" style="height:<?php echo $islidex_options['widget_size_h']; ?>px;">
			
			<?php
			$slideposts = get_posts('numberposts='.$widgnum.'&cat='.$widgcat.'');
			foreach($slideposts as $islidex_widget) {
				$key1 = "islidex_slide"; //in case you want your own slide image and not taken from the post attachment
				$slide = get_post_meta($islidex_widget->ID, $key1, true);
				$title = __($islidex_widget->post_title);
				$attachments = get_children( array('post_parent' => $islidex_widget->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
				if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_widget->ID)) {
				$image_id = get_post_thumbnail_id($islidex_widget->ID);
				$feat = wp_get_attachment_image_src($image_id,'large', true);
				echo '<div class="slidew">';
				if (($islidex_options['wlinked']) == 1): echo '<a href="'.get_permalink($islidex_widget->ID).'">'; endif;
				echo '<img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.$feat[0].'&amp;w='.$islidex_options['widget_size_w'].'&amp;h='.$islidex_options['widget_size_h'].'&amp;zc=0&amp;q=100" />';
				if (($islidex_options['wlinked']) == 1): echo '</a>'; endif;
				echo '</div>'; // the featured image
				} elseif ($slide == true) {
					echo '<div class="slidew">';
				if (($islidex_options['wlinked']) == 1): echo '<a href="'.get_permalink($islidex_widget->ID).'">'; endif;
				echo '<img width="'.$islidex_options['widget_size_w'].'" height="'.$islidex_options['widget_size_h'].'" src="'.$timthumb_path.'?src='.$slide.'&amp;w='.$islidex_options['widget_size_h'].'&amp;h='.$islidex_options['widget_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
				if (($islidex_options['wlinked']) == 1): echo '</a>'; endif;
				echo '</div>';
				} else if ($attachments == true) {
						foreach($attachments as $id => $attachment) {
						$img = wp_get_attachment_image_src($id, 'full');
						$img_url = parse_url($img[0], PHP_URL_PATH);
						print '<div class="slidew">';
						if (($islidex_options['wlinked']) == 1): echo '<a href="'.get_permalink($islidex_widget->ID).'">'; endif;
						print '<img width="'.$islidex_options['widget_size_w'].'" height="'.$islidex_options['widget_size_h'].'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$islidex_options['widget_size_h'].'&amp;h='.$islidex_options['widget_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
				if (($islidex_options['wlinked']) == 1): echo '</a>'; endif;
				echo '</div>';
						}
				} else {
				print '<div class="slidew">';
				if (($islidex_options['wlinked']) == 1): echo '<a href="'.get_permalink($islidex_widget->ID).'">'; endif;
				print '<img alt="'.$title.'" title="'.$title.'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_big.png&amp;w=250&amp;h=250&amp;zc=0&amp;q=100" />';
				if (($islidex_options['wlinked']) == 1): echo '</a>'; endif;
				echo '</div>';
				}
			}	
		wp_reset_query(); ?>
			</div>
			<div id="slidesw_menu">
			<ul>
			<li class="fbar">&nbsp;</li>
			
		<?php islidex_thumb_widget() ?>

			</ul>
			</div>
		</div>

		<?php // NIVO WIDGET THEME

		} elseif (($islidex_options['widget_theme']) == 'Nivo') { ?>

		<div id="sliderw" style="min-height:<?php echo $widgh ?>px;min-width:<?php echo $widgw ?>px;width:<?php echo $widgw ?>px;height:<?php echo $widgh ?>px;">
		
		<?php
		$slideposts = get_posts('numberposts='.$widgnum.'&cat='.$widgcat.'');
		foreach($slideposts as $islidex_widget) {
			$key1 = "islidex_slide"; //in case you want your own slide image and not taken from the post attachment
			$slide = get_post_meta($islidex_widget->ID, $key1, true);
			$title = __($islidex_widget->post_title);
			$attachments = get_children( array('post_parent' => $islidex_widget->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
			if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_widget->ID)) {
				$image_id = get_post_thumbnail_id($islidex_widget->ID);
				$feat = wp_get_attachment_image_src($image_id,'large', true);
				if (($islidex_options['wlinked']) == 1): echo '<a href="'.get_permalink($islidex_widget->ID).'">'; endif;
				echo '<img src="'.$timthumb_path.'?src='.$feat[0].'&amp;w='.$islidex_options['widget_size_w'].'&amp;h='.$islidex_options['widget_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" />';
				if (($islidex_options['wlinked']) == 1): echo '</a>'; endif;// the featured image
			} elseif ($slide == true) {
				if (($islidex_options['wlinked']) == 1): echo '<a href="'.get_permalink($islidex_widget->ID).'">'; endif;
				echo '<img src="'.$timthumb_path.'?src='.$slide.'&amp;w='.$islidex_options['widget_size_w'].'&amp;h='.$islidex_options['widget_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" />';
				if (($islidex_options['wlinked']) == 1): echo '</a>'; endif;
			} else if ($attachments == true) {
				foreach($attachments as $id => $attachment) {
				$img = wp_get_attachment_image_src($id, 'full');
				$img_url = parse_url($img[0], PHP_URL_PATH);
				if (($islidex_options['wlinked']) == 1): echo '<a href="'.get_permalink($islidex_widget->ID).'">'; endif;
				print '<img width="'.$islidex_options['widget_size_w'].'" height="'.$islidex_options['widget_size_h'].'" src="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$islidex_options['widget_size_w'].'&amp;h='.$islidex_options['widget_size_h'].'&amp;zc=1&amp;q=100" alt="'.$title.'" title="'.$title.'" class="captify" />';
				if (($islidex_options['wlinked']) == 1): echo '</a>'; endif;
				}
			} else {
			if (($islidex_options['wlinked']) == 1): echo '<a href="'.get_permalink($islidex_widget->ID).'">'; endif;
			print '<img width="'.$islidex_options['widget_size_w'].'" height="'.$islidex_options['widget_size_h'].'" src="'.$timthumb_path.'?src='.ISLIDEX_PLUGIN_IMAGES.'/wp_big.png&amp;w=250&amp;h=250&amp;zc=0&amp;q=100" alt="'.$title.'" title="'.$title.'" style="padding-top:5%;" />';
				if (($islidex_options['wlinked']) == 1): echo '</a>'; endif;
			}
		} 
		wp_reset_query(); ?>
		</div>

	<?php } //End of Nivo. Piecemaker is too big for widgets, so we skip it.

            echo $after_widget;
		}
 
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			return $instance;
		}
 
		/* Back end, the interface shown in Appearance -> Widgets
		 * administration interface.
		 */
		function form($instance) {
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'entry_title' => '', 'comments_title' => '' ) );
			$title = strip_tags($instance['title']);
			?>
 
<p>
<label for="<?php echo $this->get_field_id('title'); ?>">Title: 
    <input
	   class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
	   name="<?php echo $this->get_field_name('title'); ?>" type="text"
	   value="<?php echo attribute_escape($title); ?>" 
	/>
</label>
</p>
 			<?php
		}			
	}
}

//if (($islidex_options['usewidget']) == 1) {
	function islidex_new_widget_init() {
		register_widget('islidex_widget');
	}
	add_action('widgets_init', 'islidex_new_widget_init');
//}
 
$wpdpd = new islidex_widget();

 // end of widget function

?>