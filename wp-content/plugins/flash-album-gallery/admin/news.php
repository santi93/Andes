<?php
// include the flag function
@ require_once (dirname(dirname(__FILE__)). '/flag-config.php');
if ( current_user_can('manage_options') ) {
  extract($_POST);
  $str = file_get_contents($want2Read);
  echo $str;
} else { ?>
<div id="skins">
    <p><?php _e('Failed to load content.') ?><br /><br /><a href="http://photogallerycreator.com/grand-flagallery/">http://photogallerycreator.com/grand-flagallery/</a></p>
</div>
<?php }
?>