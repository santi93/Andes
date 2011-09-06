<?php 
preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
require_once( $_m[1] . 'wp-load.php');
if ( !current_user_can('FlAG Change skin') ) die(0);

$settingsXML =  str_replace("\\","/", dirname(__FILE__).'/settings.xml');

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
	$settingsXML =  str_replace("\\","/", dirname(__FILE__).'/settings.xml');
	$fp = fopen($settingsXML, "r");
	if(!$fp) {
		echo '<p style="color:#ff0000;"><b>Error! The configuration file not be found. You need to reinstall this skin.</b></p>';
	} else {
		$flag_options = get_option('flag_options');
		$settingsPath = $flag_options['skinsDirURL'].basename( dirname(dirname(__FILE__)) ).'/settings'; 
		$cPanel = $settingsPath."/cpanel.swf";
		$swfObject = FLAG_URLPATH."admin/js/swfobject.js?ver=2.2";
		?>
		<div id="skinOptions">
			<script type="text/javascript" src="<?php echo $swfObject ?>"></script>
			<script type="text/javascript">
				var flashvars = {
					path : "<?php echo $settingsPath; ?>",
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