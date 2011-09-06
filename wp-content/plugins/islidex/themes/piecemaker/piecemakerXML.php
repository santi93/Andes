<?php 
require_once( '../../../../../wp-load.php' );
require_once( '../../islidex.php' ); 
$islidex_options = get_option('islidex');
$w = $islidex_options['slide_size_w'];
$h = $islidex_options['slide_size_h'];
$numpost = $islidex_options['num_post'];
$catid = $islidex_options['category_id'];
$timthumb_path = ISLIDEX_PLUGIN_JS . '/timthumb.php';
$inco = str_replace("#", "0x", $islidex_options['piece_innercolor']);
$texba = str_replace("#", "0x", $islidex_options['piece_textbackground']);
?>
<?php
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="utf-8" ?>
<Piecemaker>
 <Settings>
  <imageWidth>'.$islidex_options['slide_size_w'].'</imageWidth>
  <imageHeight>'.$islidex_options['slide_size_h'].'</imageHeight>
  <segments>'.$islidex_options['piece_segments'].'</segments>
  <tweenTime>'.$islidex_options['piece_tweentime'].'</tweenTime>
  <tweenDelay>'.$islidex_options['piece_tweendelay'].'</tweenDelay>
  <tweenType>'.$islidex_options['piece_tweentype'].'</tweenType>
  <zDistance>'.$islidex_options['piece_zdistance'].'</zDistance>
  <expand>'.$islidex_options['piece_expand'].'</expand>
  <innerColor>'.$inco.'</innerColor>
  <textBackground>'.$texba.'</textBackground>
  <shadowDarkness>'.$islidex_options['piece_shadowdarkness'].'</shadowDarkness>
  <textDistance>'.$islidex_options['piece_textdistance'].'</textDistance>
  <autoplay>'.$islidex_options['piece_autoplay'].'</autoplay>
 </Settings>';
$slideposts = get_posts('numberposts='.$numpost.'&cat='.$catid.'');
	foreach($slideposts as $islidex_post) {
		$key1 = "islidex_slide"; //in case you want your own slide image and not taken from the post attachment
		$slide = get_post_meta($islidex_post->ID, $key1, true);
		$title = __($islidex_post->post_title);
		$excerpt = __($islidex_post->post_excerpt);
		$attachments = get_children( array('post_parent' => $islidex_post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'rand', 'numberposts' => 1) );
		if (function_exists('has_post_thumbnail') && has_post_thumbnail($islidex_post->ID)) {
			$image_id = get_post_thumbnail_id($islidex_post->ID);
			$feat = wp_get_attachment_image_src($image_id,'large', true);
			echo '
 <Image Filename="'.$timthumb_path.'?src='.$feat[0].'&w='.$islidex_options['slide_size_w'].';&h='.$islidex_options['slide_size_h'].';&zc=1;&q=100">
  <Text>
   <headline><a href="'.get_permalink($islidex_post->ID).'">'.$title.'</a></headline>
   <break>Ӂ</break>
   <paragraph>'.$excerpt.'</paragraph>
  </Text>
 </Image>'; // the featured image
			} elseif ($slide == true) { 
			echo '
 <Image Filename="'.$timthumb_path.'?src='.$slide.'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100">
  <Text>
   <headline><a href="'.get_permalink($islidex_post->ID).'">'.$title.'</a></headline>
   <break>Ӂ</break>
   <paragraph>'.$excerpt.'</paragraph>
  </Text>
 </Image>';
			} else if ($attachments == true) {
				foreach($attachments as $id => $attachment) {
				$img = wp_get_attachment_image_src($id, 'full');
				$img_url = parse_url($img[0], PHP_URL_PATH);
				print '
 <Image Filename="'.$timthumb_path.'?src='.$img_url.'&amp;w='.$islidex_options['slide_size_w'].'&amp;h='.$islidex_options['slide_size_h'].'&amp;zc=1&amp;q=100">
  <Text>
   <headline><a href="'.get_permalink($islidex_post->ID).'">'.$title.'</a></headline>
   <break>Ӂ</break>
   <paragraph>'.$excerpt.'</paragraph>
  </Text>
 </Image>';
				}
			}
		}
echo '
</Piecemaker>';
?>