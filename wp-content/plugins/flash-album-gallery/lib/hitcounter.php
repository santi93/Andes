<?php
// include the flag function
@ require_once (dirname(dirname(__FILE__)). '/flag-config.php');
if ( is_user_logged_in() ) {
  if( isset($_GET['pid']) ) {
  	$pictureID = intval($_GET['pid']) ;
  	flag_update_hitcounter($pictureID);
  }
  if ( isset($_POST['pid']) ) {
  	$pictureID = intval($_POST['pid']);
  	flag_update_hitcounter($pictureID);
  }
}
/**
 * Update image hitcounter in the database
 * 
 * @param int $pid   id of the image
 * @param string | int $galleryid
 */
function flag_update_hitcounter($pid, $sethits = false) {
	global $wpdb;

	if( $sethits === FALSE ) 
		$sethits = "`hitcounter`+1";
	
	if ( $pid )
		$result = $wpdb->query( "UPDATE $wpdb->flagpictures SET `hitcounter` = $sethits WHERE pid = $pid" );

}
?>