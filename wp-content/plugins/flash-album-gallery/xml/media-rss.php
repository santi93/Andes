<?php
/**
* Media RSS presenting the pictures in counter chronological order.
* 
*/

// Load required files and set some useful variables
require_once(dirname(__FILE__) . "/../flag-config.php");
require_once(dirname(__FILE__) . "/../lib/media-rss.php");

// Check we have the required GET parameters
$mode = $_GET["mode"];
if (!isset($mode) || $mode == '')
	$mode = 'last_pictures';

// Act according to the required mode
$rss = '';
if ($mode=='last_pictures') {
	
	// Get additional parameters
	$page = (int) $_GET["page"];
	if (!isset($page) || $page == '') {
		$page = 0;
	}
	
	$show = (int) $_GET["show"];	
	if (!isset($show) || $show == '' || $show == 0) {
		$show = 10;
	}
	
	$rss = flagMediaRss::get_last_pictures_mrss($page, $show);	
} else if ( $mode=='gallery' ) {
		
	// Get all galleries
	$galleries = $flagdb->find_all_galleries();

	if ( count($galleries) == 0 ) {
		header('content-type:text/plain;charset=utf-8');
		echo sprintf(__("No galleries have been yet created.","flag"), $gid);
		exit;
	}
	
	// Get additional parameters
	$gid = (int) $_GET['gid'];
	
	//if no gid is present, take the first gallery
	if (!isset($gid) || $gid == '' || $gid == 0) {
        $first = current($galleries);
        $gid = $first->gid;
	}
	    
	
	// Set the main gallery object
	$gallery = $galleries[$gid];
	
	if (!isset($gallery) || $gallery==null) {
		header('content-type:text/plain;charset=utf-8');
		echo sprintf(__("The gallery ID=%s does not exist.","flag"), $gid);
		exit;
	}

	// show other galleries if needed
	$prev_next = ( $_GET['prev_next'] == 'true' ) ? true : false;
	$prev_gallery = $next_gallery =  null;
	
	// Get previous and next galleries if required
	if ($prev_next) {
		reset($galleries);
		while( current($galleries) ){
 			if( key($galleries) == $gid )
				break;
			next($galleries);
		}
		// one step back
		$prev_gallery  = prev( $galleries);
		// two step forward... Could be easier ? How ?
		next($galleries);
		$next_gallery  = next($galleries);
	}

	$rss = flagMediaRss::get_gallery_mrss($gallery, $prev_gallery, $next_gallery);	
	
} else {
	header('content-type:text/plain;charset=utf-8');
	echo sprintf(__("Invalid MediaRSS command (%s).","flag"), $mode);
	exit;
}


// Output header for media RSS
header("content-type:text/xml;charset=utf-8");
echo "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>\n";
echo $rss;
?>