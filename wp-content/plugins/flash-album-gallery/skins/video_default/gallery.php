<?php // Create XML output
header("content-type:text/xml;charset=utf-8");
preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
require_once( $_m[1] . 'wp-load.php');
$flag_options = get_option ('flag_options');
$file = $_GET['playlist'];
$playlistPath = $_m[1].$flag_options['galleryPath'].'playlists/video/'.$file.'.xml';
if(file_exists($playlistPath)) {
	require_once( FLAG_ABSPATH.'admin/video.functions.php');
	$playlist = get_v_playlist_data($playlistPath);
	if(count($playlist['items'])) {
		$content = '<items>';
		foreach( $playlist['items'] as $id ) {
			$flv = get_post($id);
			$url = wp_get_attachment_url($flv->ID);
			if($flv->post_mime_type == 'video/x-flv') {
			    $thumb = get_post_meta($id, 'thumbnail', true);
				$content .= '
	    <item id="'.$flv->ID.'">
    	  	<track>'.$url.'</track>
      		<title><![CDATA['.$flv->post_title.']]></title>
      		<description><![CDATA['.$flv->post_content.']]></description>
      		<thumbnail>'.$thumb.'</thumbnail>
    	    </item>';
			}
		}
		$content .= '
	</items>';
	}
	$xml = file_get_contents($playlistPath);
	$newXML = preg_replace("|<items>.*?</items>|si", $content, $xml);
	echo $newXML;
} else {
	echo 'no such file or directory';
}
?>