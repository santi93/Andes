<?php
$flag_options = get_option ('flag_options'); 
$siteurl = get_option ('siteurl');
$isCrawler = flagGetUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crowler
?>
<style type="text/css">
<?php if (!$isCrawler){ ?> 
#<?php echo $skinID ?>_jq { display: none; }
<?php } ?>
div#<?php echo $skinID; ?>_jq { position: relative; width: <?php echo $width; if(strpos($width, '%') === false) echo 'px'; ?>; height: <?php echo $height; ?>px; overflow: hidden; }
div#<?php echo $skinID; ?>_next { position: absolute; padding: 5px 9px; right: 10px; bottom: 10px; background: #000; color: #fff; font: bold 20px/20px Arial; border: 1px solid #000; z-index: 100; cursor: pointer; 
-webkit-border-radius:16px;
-khtml-border-radius:16px;
-moz-border-radius:16px;
border-radius:16px;
opacity: 0.7;
filter:alpha(opacity=70);
-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=70)";
}
div#<?php echo $skinID; ?>_prev { position: absolute; padding: 5px 9px; left: 10px; bottom: 10px; background: #000; color: #fff; font: bold 20px/20px Arial; border: 1px solid #000; z-index: 100; cursor: pointer;
-webkit-border-radius:16px;
-khtml-border-radius:16px;
-moz-border-radius:16px;
border-radius:16px;
opacity: 0.7;
filter:alpha(opacity=70);
-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=70)";
}
div#<?php echo $skinID; ?>_next:hover {
opacity: 0.9;
filter:alpha(opacity=90);
-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=90)";
}
div#<?php echo $skinID; ?>_prev:hover {
opacity: 0.9;
filter:alpha(opacity=90);
-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=90)";
}
.grandBannerAlternative { clear: both; width: <?php echo $width; if(strpos($width, '%') === false) echo 'px'; ?>; height: <?php echo $height; ?>px; overflow: hidden; }
.grandBannerAlternative > div { display: none; position: relative; width: 100%; }
.grandBannerAlternative div img { width: 100%; max-width: 100%; height: auto; }
.grandBannerAlternative > div:first-child { display: block; }
.grandBannerAlternative a { display: block; width: <?php echo $width; if(strpos($width, '%') === false) echo 'px'; ?>; height: <?php echo $height; ?>px; overflow: hidden; }
.grandBannerAlternative span { display: block; position: absolute; left: 0px; top: 10px; padding: 5px 10px; background: #000000; color: #ffffff; font-size: 12px; }
.grandBannerAlternative span strong { font-size: 14px; margin-bottom: 3px; }
</style>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function() {
	var fv = swfobject.getFlashPlayerVersion();
	if(fv.major<9){	
		jQuery('#<?php echo $skinID; ?>_jq').show();
		var d = jQuery('#<?php echo $skinID; ?>_jq .gban:first').html();
		d = d.replace(/\[/g, '<');
		d = d.replace(/\]/g, ' />');
		jQuery('#<?php echo $skinID; ?>_jq .gban:first').addClass('loaded').html(d);
		jQuery('#<?php echo $skinID; ?>_jq .gban span').css({opacity: 0.6});
		jQuery('#<?php echo $skinID; ?>_jq .grandBannerAlternative').cycle({
		    after: function(currSlideElement, nextSlideElement, options, forwardFlag) {
		    	if(jQuery(nextSlideElement).next().length){
			    	if(jQuery(nextSlideElement).next().hasClass('loaded')) { return }
			    	var d = jQuery(nextSlideElement).next().html().replace(/\[/g, '<').replace(/\]/g, ' />');
			    	jQuery(nextSlideElement).next().addClass('loaded').html(d);
				}
		    },
		    before: function(currSlideElement, nextSlideElement, options, forwardFlag) {
		    	if(jQuery(nextSlideElement).length){
			    	if(jQuery(nextSlideElement).hasClass('loaded')) { return }
			    	var d = jQuery(nextSlideElement).html().replace(/\[/g, '<').replace(/\]/g, ' />');
			    	jQuery(nextSlideElement).addClass('loaded').html(d);
				}
		    },
		    containerResize: 0,   // resize container to fit largest slide 
		    fx:            'fade',// name of transition effect (or comma separated names, ex: 'fade,scrollUp,shuffle') 
		    next:          '#<?php echo $skinID; ?>_next',  // element, jQuery object, or jQuery selector string for the element to use as event trigger for next slide 
		    pause:         1,     // true to enable "pause on hover" 
		    prev:          '#<?php echo $skinID; ?>_prev',  // element, jQuery object, or jQuery selector string for the element to use as event trigger for previous slide 
		    requeueOnImageNotLoaded: true, // requeue the slideshow if any image slides are not yet loaded 
		    speed:         1000,  // speed of the transition (any valid fx speed value) 
		    startingSlide: 0,     // zero-based index of the first slide to be displayed 
		    timeout:       <?php if($autoPlay){ echo ($slideshowDelay * 1000); } else { echo '0'; } ?>,  // milliseconds between slide transitions (0 to disable auto advance) 
		});
	}
});
//]]>	
</script>
<div id="<?php echo $skinID; ?>_jq"><div class="grandBannerAlternative">
<?php 
require_once( FLAG_ABSPATH.'admin/banner.functions.php');
$playlist = get_b_playlist_data($galleryID);
if(count($playlist['items'])) {
	$content = '';
	foreach( $playlist['items'] as $id ) {
		$ban = get_post($id);
		if($ban->ID) {
			$url = wp_get_attachment_url($ban->ID);
			$path = get_attached_file($ban->ID);
			$info = pathinfo($path);
			$dir = $info['dirname'];
			$ext = $info['extension'];
			$name = urldecode( basename( str_replace( '%2F', '/', urlencode( $path ) ), ".$ext" ) );
			$img_file = "{$dir}/{$name}-{$suffix}.{$ext}";
			if(!file_exists($img_file)){
				$track = $url;
			} else {
				$track = dirname($url)."/{$name}-{$suffix}.{$ext}";
			}
		    $thumbnail = get_post_meta($id, 'thumbnail', true);
		    $link = get_post_meta($id, 'link', true);
		    $preview = get_post_meta($id, 'preview', true);
			$content .= '
<div id="gban_'.$ban->ID.'" class="gban">';
			if($link){ $content .= '<a href="'.$link.'">'; }
			if ($isCrawler){
				$content .= '<img src="'.$track.'" alt="" />';
			} else {
				$content .= '[img src="'.$track.'" alt=""]';
			}
			if($link){ $content .= '</a>'; }
          	$content .= '<span class="gban-title"><strong>'.$ban->post_title.'</strong><br />'.$ban->post_content.'</span></div>';
		}
	}
}
echo $content;
?>
</div>
<div id="<?php echo $skinID; ?>_prev">&lt;</div>
<div id="<?php echo $skinID; ?>_next">&gt;</div>
</div> 
