<?php
// Create XML output
header("content-type:text/xml;charset=utf-8");

// look up for the path
if(file_exists(dirname(dirname(dirname(__FILE__))) . "/flash-album-gallery/flag-config.php")) {
	require_once( str_replace("\\","/", dirname(dirname(dirname(__FILE__))) . "/flash-album-gallery/flag-config.php") );
} else if(file_exists(dirname(dirname(dirname(__FILE__))) . "/flag-config.php")) {
	require_once( str_replace("\\","/", dirname(dirname(dirname(__FILE__))) . "/flag-config.php") );
}

$file =  str_replace("\\","/", dirname(__FILE__).'/settings/settings.xml');
$mainXML="";
$fp = fopen($file, "r");
if(!$fp)
{
	exit( "0");//Failure - not read;
}
while(!feof($fp))
{
	$mainXML .= fgetc($fp);
}
$propertiesXML = substr ($mainXML, strpos($mainXML,"<properties>"),(strpos($mainXML,"</panel>")-strpos($mainXML,"<properties>")));

global $wpdb;

$flag_options = get_option ('flag_options');
$siteurl = get_option ('siteurl');

// get the gallery id
$gID = explode( '_', $_GET['gid'] );

if ( is_user_logged_in() ) 
	$exclude_clause = '';
else 
	$exclude_clause = ' AND exclude<>1 ';

	echo "<gallery>\n";
	if($propertiesXML)
	{
		echo $propertiesXML;
	}
// get the pictures
foreach ( $gID as $galleryID ) {
	$galleryID = (int) $galleryID;
	if ( $galleryID == 0) {
		$thepictures = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE 1=1 {$exclude_clause} ORDER BY tt.{$flag_options['galSort']} {$flag_options['galSortDir']} ");
	} else {
		$thepictures = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = '$galleryID' {$exclude_clause} ORDER BY tt.{$flag_options['galSort']} {$flag_options['galSortDir']} ");
	}

  if (is_array ($thepictures) && count($thepictures)){
	echo "	<category id='".$galleryID."'>\n";
	echo "		<properties>\n";
	echo "			<title>".attribute_escape(flagGallery::i18n(stripslashes($thepictures[0]->title)))."</title>\n";
	echo "		</properties>\n";
	echo "		<items>\n";

	if (is_array ($thepictures)){
		foreach ($thepictures as $picture) {
	echo "			<item id='".$picture->pid."'>\n";
	echo "				<thumbnail>".$siteurl."/".$picture->path."/thumbs/thumbs_".$picture->filename."</thumbnail>\n";
	echo "				<title><![CDATA[".attribute_escape(flagGallery::i18n(stripslashes($picture->alttext)))."]]></title>\n";
	echo "				<description><![CDATA[".html_entity_decode(attribute_escape(flagGallery::i18n(stripslashes($picture->description))))."]]></description>\n";
	//echo "				<link>".$picture->link."</link>\n";
	echo "				<preview_>".$siteurl."/".$picture->path."/".$picture->filename."</preview_>\n";
	echo "				<date>".$picture->imagedate."</date>\n";
	echo "			</item>\n";
		}
	}

	echo "		</items>\n";
	echo "	</category>\n";
  }
}
	echo "</gallery>\n";

?>