<?php
preg_match('|^(.*?/)(wp-content)/|i', str_replace('\\', '/', __FILE__), $_m);
require_once( $_m[1] . 'wp-load.php');
// check for correct capability
if ( !is_user_logged_in() )
	die('-1');

// check for correct FlAG capability
if ( !current_user_can('FlAG Manage video') ) 
	die('-1');	?>
<html>
<head>
  <title>Preview Video</title>
  <script type="text/javascript" src="<?php echo FLAG_URLPATH; ?>admin/js/swfobject.js"></script>
</head>
<body style="margin: 0; padding: 0; background: #555555; overflow: hidden;">
<?php echo flagShowVmPlayer($_GET['vid'], $w='520', $h='304', $autoplay=true); ?>
</body>
</html>