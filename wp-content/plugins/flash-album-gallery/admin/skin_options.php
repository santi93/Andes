<?php 
require_once( dirname(dirname(__FILE__)) . '/flag-config.php');

// check for correct capability
if ( !is_user_logged_in() )
	die('-1');

// check for correct FlAG capability
if ( !current_user_can('FlAG Change skin') ) 
	die('-1');	

$flag_options = get_option('flag_options');
$act_skin = isset($_GET['skin'])? $_GET['skin'] : $flag_options['flashSkin'];
$settings = $flag_options['skinsDirABS'].$act_skin.'/settings';
$settingsXML =  $settings.'/settings.xml';

if (isset($HTTP_RAW_POST_DATA) ) {
	$flashPost = $HTTP_RAW_POST_DATA;
} else {
	$flashPost = implode("\r\n", file('php://input'));
}
if($flashPost) {
	$fp = fopen($settingsXML, "r");
	if(!$fp) {
		exit( "2");//Failure - not read;
	}
	while(!feof($fp)) {
		$mainXML .= fgetc($fp);
	}
	$fp = fopen($settingsXML, "w");
	if(!$fp)
		exit("0");//Failure
	$newProperties = preg_replace("|<properties>.*?</properties>|si", $flashPost, $mainXML);
	fwrite($fp, $newProperties);
	fclose($fp);
	echo "1";//Save
}

if(isset($_GET['show_options'])) {
	flag_skin_options();
}

function flag_skin_options() {
	$flag_options = get_option('flag_options');
	$act_skin = isset($_GET['skin'])? $_GET['skin'] : $flag_options['flashSkin'];
	$settings = $flag_options['skinsDirURL'].$act_skin.'/settings';
	$settingsXML =  $flag_options['skinsDirABS'].$act_skin.'/settings/settings.xml';
	$fp = fopen($settingsXML, "r");
	if(!$fp) {
		echo '<p style="color:#ff0000;"><b>Error! The configuration file not be found. You need to reinstall this skin.</b></p>';
	} else {
		$cPanel = FLAG_URLPATH."lib/cpanel.swf";
		$swfObject = FLAG_URLPATH."admin/js/swfobject.js?ver=2.2";
		?>
		<div id="skinOptions">
			<script type="text/javascript" src="<?php echo $swfObject ?>"></script>
			<script type="text/javascript">
				var flashvars = {
					path : "<?php echo $settings; ?>",
				};
				var params = {
					wmode : "transparent",
					scale : "noScale",
					saling : "lt",
					allowfullscreen : "false",
					menu : "false"
				};
				var attributes = {};
				swfobject.embedSWF("<?php echo $cPanel; ?>", "myContent", "600", "550", "9.0.0", "<?php echo FLAG_URLPATH; ?>skins/expressInstall.swf", flashvars, params, attributes);
			</script>
			<div id="myContent"><a href="http://www.adobe.com/go/getflash"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a>
				<p>This page requires Flash Player version 10.1.52 or higher.</p>
			</div>	
		</div> 
		<?php
	}
	fclose($fp);
}

?>