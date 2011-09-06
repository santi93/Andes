<?php
preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
require_once( $_m[1] . 'wp-load.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?> - <?php bloginfo('description'); ?> </title>
</head>
<body style="margin: 0; padding: 0;">
<div id="page">
<script language="JavaScript" src="http://code.jquery.com/jquery.min.js" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo plugins_url('/flash-album-gallery/admin/js/swfobject.js'); ?>" type="text/javascript"></script>
<?php $flag_options = get_option('flag_options');
if(isset($_GET['l'])) {
	$linkto = intval($_GET['l']);
} else {
	$posts = get_posts(array("showposts" => 1));
	$linkto = $posts[0]->ID;
}
if(isset($_GET['i'])) {
	$skin = '';
	if(isset($_GET['f'])){
		$skinpath = trailingslashit( $flag_options['skinsDirABS'] ).$_GET['f'];
		if(is_dir($skinpath))
			$skin = $_GET['f'];
	}
	$h = isset($_GET['h'])? $_GET['h'] : (int) $flag_options['flashHeight'];

	$gids = $_GET['i'];
	if($gids=='all') {
		$gids='';
		if(empty($orderby)) $orderby='gid';
		if(empty($order)) $order='DESC';
	          $gallerylist = $flagdb->find_all_galleries($orderby, $order);
	          if(is_array($gallerylist)) {
			$excludelist = explode(',',$exclude);
			foreach($gallerylist as $gallery) {
				if (in_array($gallery->gid, $excludelist))
					continue;
				$gids.='_'.$gallery->gid;
			}
			$gids = ltrim($gids,'_');
		}
	}
	echo flagShowFlashAlbum($gids, $name='Gallery', $width='100%', $height=$_GET['h'], $skin, $playlist='', $wmode='opaque', $linkto); ?>

<link href="<?php echo plugins_url('/flash-album-gallery/admin/js/jquery.fancybox-1.3.4.css'); ?>" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="<?php echo plugins_url('/flash-album-gallery/admin/js/jquery.fancybox-1.3.4.pack.js'); ?>" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo plugins_url('/flash-album-gallery/admin/js/flagscroll.js'); ?>" type="text/javascript"></script>
<script language="JavaScript" src="<?php echo plugins_url('/flash-album-gallery/admin/js/script.js'); ?>" type="text/javascript"></script>

<?php } ?>

<?php 
if(isset($_GET['m'])) {
	$playlistpath = $flag_options['galleryPath'].'playlists/'.$_GET['m'].'.xml';
	if(file_exists($playlistpath))
		echo flagShowMPlayer($playlist=$_GET['m'], $width='', $height='', $wmode='opaque');
	else
		_e("Can't find playlist");
}
?>
<?php 
if(isset($_GET['v'])) {
	$playlistpath = $flag_options['galleryPath'].'playlists/video/'.$_GET['v'].'.xml';
	if(file_exists($playlistpath))
		echo flagShowVPlayer($playlist=$_GET['v'], $width='', $height='', $wmode='opaque');
	else
		_e("Can't find playlist");
}
?>
<?php 
if(isset($_GET['b'])) {
	$playlistpath = $flag_options['galleryPath'].'playlists/banner/'.$_GET['b'].'.xml';
	if(file_exists($playlistpath))
		echo flagShowBanner($playlist=$_GET['b'], $width='', $height='', $wmode='opaque');
	else
		_e("Can't find playlist");
}
?>
</div>
</body>
</html>