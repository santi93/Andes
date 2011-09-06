<?php // Create XML output
header("content-type:text/xml;charset=utf-8");
preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
require_once( $_m[1] . 'wp-load.php');
$flag_options = get_option ('flag_options');
$file = $_GET['playlist'];
$playlistPath = $_m[1].$flag_options['galleryPath'].'playlists/'.$file.'.xml';
if(file_exists($playlistPath)) {
	require_once( FLAG_ABSPATH.'admin/playlist.functions.php');
	$playlist = get_playlist_data($playlistPath);
	if(count($playlist['items'])) {
		$content = '<items>';
		foreach( $playlist['items'] as $id ) {
			$mp3 = get_post($id);
			$url = wp_get_attachment_url($mp3->ID);
			if($mp3->post_mime_type == 'audio/mpeg') {
			    $thumb = get_post_meta($id, 'thumbnail', true);
				$content .= '
	    <item id="'.$mp3->ID.'">
    	  	<track>'.$url.'</track>
      		<title><![CDATA['.$mp3->post_title.']]></title>
      		<description><![CDATA['.$mp3->post_content.']]></description>
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