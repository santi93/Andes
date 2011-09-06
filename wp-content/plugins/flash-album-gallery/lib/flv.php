<?php // Create XML output
preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
require_once( $_m[1] . 'wp-load.php');
$flag_options = get_option ('flag_options');
if(isset($_GET['vID'])) {
	header("content-type:text/xml;charset=utf-8");
	$vid = get_post($_GET['vID']);
	if(in_array($vid->post_mime_type, array('video/x-flv'))) {
		$thumb = get_post_meta($_GET['vID'], 'thumbnail', true);
		$content = '<item id="'.$vid->ID.'">
	<properties>
		<property0>0x'.$flag_options["vmColor1"].'</property0>
		<property1>0x'.$flag_options["vmColor2"].'</property1>
		<property2>0x'.$flag_options["videoBG"].'</property2>
	</properties>
	<content>
	  	<preview>'.$vid->guid.'</preview>
  		<title><![CDATA['.$vid->post_title.']]></title>
  		<description><![CDATA['.$vid->post_content.']]></description>
  		<thumbnail>'.$thumb.'</thumbnail>
	</content>
</item>';
		echo $content;
	} else {
		echo 'wrong mime type';
	}
} else {
	echo 'no such file ID';
}
?>