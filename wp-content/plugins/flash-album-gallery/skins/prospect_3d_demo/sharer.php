<?php
if(isset($_GET['id'])) {
	$sharer_ids = explode('p',$_GET['id']);
	$sharer_id = $sharer_ids[0];
	$sharer_p = $sharer_ids[1];
	preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
	require_once($_m[1].'wp-load.php');
	global $wpdb;
	$sharer_image = $wpdb->get_row( $wpdb->prepare( "SELECT tt.*, t.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE tt.pid = %d ", $sharer_id ) );
	$sharer_post_title = get_the_title($sharer_p);
	$sharer_tit = ($sharer_image->alttext)? ' &raquo; '.$sharer_image->alttext : '';
	$sharer_title = $sharer_post_title.' &raquo; '.$sharer_image->title.$sharer_tit;
}
header ('Content-type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html>
<head>
<title><?php echo $sharer_title; ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<style type="text/css">
* { margin: 0; padding: 0; }
body { background: #222222; color: #ffffff; text-align: center; }
a { color: #ffffff; }
.post { width: 800px; padding: 10px; border: 1px solid #888888; margin: 10px auto; text-align: left; background: #000000; }
.fullpost { margin: 10px; background: #000000; padding: 5px; display: inline-block; }
.post .thumb { display: none; }
.post h1 { font-size: 24px; margin-bottom: 10px; }
.post #postimg { cursor: pointer; }
.post .postimg { max-width: 800px; height: auto; margin-bottom: 14px; }
.fullpost .fullsize { display: block; margin: 0 auto; }
.post .entry { font-size: 14px; }
.post .more { font-size: 12px; }
.hidden { display: none; }
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#postimg').click(function(){
    	jQuery(this).toggleClass('postimg fullsize').siblings().toggleClass('hidden').parent().toggleClass('post fullpost');
    })
});
</script>
<meta property="og:image" content="<?php echo get_option('siteurl').'/'.$sharer_image->path.'/thumbs/thumbs_'.$sharer_image->filename; ?>" />
<link rel="image_src" href="<?php echo get_option('siteurl').'/'.$sharer_image->path.'/thumbs/thumbs_'.$sharer_image->filename; ?>" />
</head>
<body>
<div class="post">
	<img id="thumb" src="<?php echo get_option('siteurl').'/'.$sharer_image->path.'/thumbs/thumbs_'.$sharer_image->filename; ?>" alt="<?php echo $sharer_image->alttext; ?>" />
	<h1><a href="<?php echo get_permalink($sharer_p).'#/'.$sharer_image->gid.'/'.$sharer_id; ?>"><?php echo $sharer_post_title; ?></a></h1>
	<img id="postimg" class="postimg" src="<?php echo get_option('siteurl').'/'.$sharer_image->path.'/'.$sharer_image->filename; ?>" alt="<?php echo $sharer_image->alttext; ?>" />
	<div class="entry"><?php echo $sharer_image->description; ?></div>
	<p class="more"><?php _e('View gallery in the post'); ?> <a href="<?php echo get_permalink($sharer_p).'#/'.$sharer_image->gid.'/'.$sharer_id; ?>"><?php echo $sharer_post_title; ?></a></p>
</div>
<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
document.getElementById('thumb').style.display = "none";
/*]]>*/
</script>
</body>
</html>