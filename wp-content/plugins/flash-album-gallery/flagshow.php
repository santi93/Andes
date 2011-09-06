<?php
// Load wp-config
require_once( dirname(__FILE__) . '/flag-config.php');

// reference thumbnail class
include_once( flagGallery::graphic_library() );
include_once('lib/core.php');

// get the plugin options
$flag_options = get_option('flag_options');	

// Some parameters from the URL
$pictureID = (int) $_GET['pid'];

// let's get the image data
$picture  = flagdb::find_image( $pictureID );
$thumb = new flag_Thumbnail( $picture->imagePath );

// Resize if necessary
if ( !empty($_GET['width']) || !empty($_GET['height']) )
	$thumb->resize( intval($_GET['width']), intval($_GET['height']) );

// Show thumbnail
$thumb->show();
$thumb->destruct();

exit;
?>