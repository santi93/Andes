<?php
//
// jQuery File Tree PHP Connector - modified for php4/wordpress compatibility
//

// look up for the path
require_once( dirname( dirname( dirname( dirname( dirname(__FILE__) ) ) ) ) . '/flag-config.php');

global $wpdb;

if (!function_exists('php4_scandir') && !function_exists('scandir')) {
	function php4_scandir($dir,$listDirectories=false, $skipDots=true) {
	    $dirArray = array();
	    if ($handle = opendir($dir)) {
	        while (false !== ($file = readdir($handle))) {
	            if (($file != "." && $file != "..") || $skipDots == true) {
	                if($listDirectories == false) { if(is_dir($file)) { continue; } }
	                array_push($dirArray,basename($file));
	            }
	        }
	        closedir($handle);
	    }
	    return $dirArray;
	}
}

require_once(ABSPATH.'wp-admin/admin.php');

$_POST['dir'] = urldecode($_POST['dir']);

if( file_exists($root . $_POST['dir']) ) {
	if (function_exists('scandir')) {
		$files = scandir($root . $_POST['dir']);
	} else {
		$files = php4_scandir($root . $_POST['dir']);
	}
	natcasesort($files);
	if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		echo "<ul class=\"jqueryDirTree\" style=\"display: none;\">";
		// All dirs
		foreach( $files as $file ) {
			if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
				echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
			}
		}
		echo "</ul>";
	}
}

?>