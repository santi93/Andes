<?php

// look up for the path
require_once( dirname( dirname( dirname(__FILE__) ) ) . '/flag-config.php');
require_once (dirname( dirname(__FILE__) ) . '/get_skin.php');
require_once (dirname( dirname(__FILE__) ) . '/playlist.functions.php');

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));

global $flag, $flagdb, $wp_query;

$all_skins = get_skins();
$all_playlists = get_playlists();

if($_REQUEST['riched'] == "false") {
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e("Insert Flash Album with one or more galleries", 'flag'); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo FLAG_URLPATH; ?>admin/js/tabs.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo FLAG_URLPATH; ?>admin/tinymce/popup.css" />
<base target="_self" />
</head>
<body id="link">
<?php } else { ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e("Insert Flash Album with one or more galleries", 'flag'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo FLAG_URLPATH; ?>admin/tinymce/tinymce.js"></script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('galleries').focus();" style="display: none">
<?php } ?>
<form name="FlAG" action="#">
<?php 
if($_REQUEST['riched'] == "false") {
?>
	<div class="cptabs_wrapper">
		<ul id="tabs" class="tabs">
			<li class="selected"><a href="#" rel="gallery_panel"><span><?php _e( 'Galleries', 'flag' ); ?></span></a></li>
			<li><a href="#" rel="album_panel"><span><?php _e( 'Albums', 'flag' ); ?></span></a></li>
			<li><a href="#" rel="sort_panel"><span><?php _e('Sort', 'flag'); ?></span></a></li>
			<li><a href="#" rel="custom_panel"><span><?php _e( 'Skin', 'flag' ); ?></span></a></li>
			<li style="display:none;"><a href="#" rel="music_panel"><span><?php _e( 'Music', 'flag' ); ?></span></a></li>
		</ul>
<?php } else { ?>
	<div class="tabs" style="position:relative; overflow:hidden; margin-bottom:-1px;">
		<ul>
			<li id="gallery_tab" class="current"><span><a href="javascript:mcTabs.displayTab('gallery_tab','gallery_panel');" onmousedown="return false;"><?php _e( 'Galleries', 'flag' ); ?></a></span></li>
			<li id="album_tab"><span><a href="javascript:mcTabs.displayTab('album_tab','album_panel');" onmousedown="return false;"><?php _e( 'Albums', 'flag' ); ?></a></span></li>
			<li id="sort_tab"><span><a href="javascript:mcTabs.displayTab('sort_tab','sort_panel');" onmousedown="return false;"><?php _e('Sort', 'flag'); ?></a></span></li>
			<li id="custom_tab"><span><a href="javascript:mcTabs.displayTab('custom_tab','custom_panel');" onmousedown="return false;"><?php _e( 'Skin', 'flag' ); ?></a></span></li>
			<li id="music_tab" style="display:none;"><span><a href="javascript:mcTabs.displayTab('music_tab','music_panel');" onmousedown="return false;"><?php _e( 'Music', 'flag' ); ?></a></span></li>
		</ul>
	</div>
	<div class="panel_wrapper" style="border:1px solid #919B9C; height:130px;">
<?php } ?>
	
		<!-- gallery panel -->
		<div id="gallery_panel" class="panel cptab current">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="galleryname"><?php _e("Album Name", 'flag'); ?>:<span style="color:red;"> *</span></label></td>
            <td valign="middle"><input id="galleryname" name="galleryname" value="Gallery" type="text" style="width: 200px" /></td>
         </tr>
         <tr>
            <td nowrap="nowrap" valign="top"><label for="galleries"><?php _e("Select galleries", 'flag'); ?>:<span style="color:red;"> *</span></label><br /><small><?php _e("(album categories)", 'flag'); ?></small></td>
            <td><select id="galleries" name="galleries" style="width: 200px" size="6" multiple="multiple">
                    <option value="all" selected="selected" onclick="javascript:document.getElementById('sort_tab').style.display='block'" style="font-weight:bold">* - <?php _e("all galleries", 'flag'); ?></option>
				<?php
					$gallerylist = $flagdb->find_all_galleries('gid', 'ASC');
					if(is_array($gallerylist)) {
						foreach($gallerylist as $gallery) {
							$name = ( empty($gallery->title) ) ? $gallery->name : $gallery->title;
							echo '<option value="' . $gallery->gid . '" >' . $gallery->gid . ' - ' . $name . '</option>' . "\n";
						}
					}
				?>
            </select></td>
         </tr>
        </table>
		</div>
		<!-- /gallery panel -->
		<!-- album panel -->
		<div id="album_panel" class="panel cptab">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap" valign="top"><label for="album"><?php _e("Select album", 'flag'); ?>:</label></td>
            <td><select id="album" name="album" style="width: 200px" size="8">
                    <option value="" selected="selected"><?php _e("choose album", 'flag'); ?></option>
				<?php
					$albumlist = $flagdb->find_all_albums('id', 'ASC');
					if(is_array($albumlist)) {
						foreach($albumlist as $album) {
							$name = $album->name;
							echo '<option value="' . $album->id . '" >' . $name . '</option>' . "\n";
						}
					}
				?>
            </select></td>
         </tr>
        </table>
		</div>
		<!-- /album panel -->
		<!-- skin panel -->
		<div id="custom_panel" class="panel cptab">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="skinname"><?php _e("Choose skin", 'flag'); ?>:</label></td>
            <td valign="middle"><select id="skinname" name="skinname" style="width: 200px">
                    <option value="" selected="selected"><?php _e("choose custom skin", 'flag'); ?></option>
<?php
	foreach ( (array)$all_skins as $skin_file => $skin_data) {
		echo '<option value="'.dirname($skin_file).'">'.$skin_data['Name'].'</option>'."\n";
	}
?>
            </select></td>
         </tr>
		 <tr>
			<td valign="top"><label><?php _e("Skin size", 'flag'); ?>:</label><br /><span style="font-size:9px">(<?php _e("blank for default", 'flag'); ?>)</span></td>
            <td valign="top"><?php _e("width", 'flag'); ?>: <input id="gallerywidth" type="text" name="gallerywidth" style="width: 50px" /> &nbsp; <?php _e("height", 'flag'); ?>: <input id="galleryheight" type="text" name="galleryheight" style="width: 50px" /></td>
		 </tr>
        </table>
		</div>
		<!-- /custom panel -->
		<!-- sort panel -->
		<div id="sort_panel" class="panel cptab">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="galorderby"><?php _e("Order by", 'flag'); ?>:</label></td>
            <td valign="middle"><select id="galorderby" name="galorderby" style="width: 200px">
                    <option value="" selected="selected"><?php _e("Gallery IDs (default)", 'flag'); ?></option>
                    <option value="title"><?php _e("Gallery Title", 'flag'); ?></option>
                    <!-- <option value="sortorder"><?php _e("User Defined", 'flag'); ?></option> -->
                    <option value="rand"><?php _e("Randomly", 'flag'); ?></option>
            </select></td>
         </tr>
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="galorder"><?php _e("Order", 'flag'); ?>:</label></td>
            <td valign="middle"><select id="galorder" name="galorder" style="width: 200px">
                    <option value="" selected="selected"><?php _e("DESC (default)", 'flag'); ?></option>
                    <option value="ASC"><?php _e("ASC", 'flag'); ?></option>
            </select></td>
         </tr>
         <tr>
            <td nowrap="nowrap" valign="middle"><label for="galexclude"><?php _e("Exclude Gallery", 'flag'); ?>:</label></td>
            <td valign="middle"><input id="galexclude" name="galexclude" type="text" style="width: 200px" /></td>
         </tr>
       </table>
		</div>
		<!-- /sort panel -->
		<!-- music panel -->
		<div id="music_panel" class="panel cptab">
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap" valign="top"><div style="display: block; width: 100px; white-space: normal;"><?php _e("Choose playlist for background music", 'flag'); ?>:</div></td>
            <td valign="middle" valign="top"><select id="playlist" name="playlist" style="width: 200px">
                    <option value="" selected="selected"><?php _e("choose playlist", 'flag'); ?></option>
				<?php 
					foreach((array)$all_playlists as $playlist_file => $playlist_data) {
						$playlist_name = basename($playlist_file, '.xml');
				?>
					<option value="<?php echo $playlist_name; ?>"><?php echo $playlist_data['title']; ?></option>
				<?php 
					}
				?>
            </select><p style="padding-top: 10px; margin: 0; font-size: 10px;"><?php _e('Read Skin specification for supporting this function.') ?></p></td>
         </tr>
        </table>
		</div>
		<!-- /music panel -->

<?php if($_REQUEST['riched'] == "false") { ?>
	</div>
	<div class="mceActionPanel">
		<div style="float: right">
			<input type="button" id="insert" name="insert" value="<?php _e("Insert", 'flag'); ?>" />
		</div>
	</div>
	<script type="text/javascript">	
		/* <![CDATA[ */
		var cptabs=new ddtabcontent("tabs");
		cptabs.setpersist(false);
		cptabs.setselectedClassTarget("linkparent");
		cptabs.init();
		/* ]]> */
	</script>
	<script type="text/javascript">
		/* <![CDATA[ */
		var win = window.dialogArguments || opener || parent || top;
		jQuery('#insert').click(function(){
			var tagtext;
			var galleryname = document.getElementById('galleryname').value;
			var gallerywidth = document.getElementById('gallerywidth').value;
			var galleryheight = document.getElementById('galleryheight').value;
			var galorderby = document.getElementById('galorderby').value;
			var galorder = document.getElementById('galorder').value;
			var galexclude = document.getElementById('galexclude').value;
			var skinname = document.getElementById('skinname').value;
			var playlist = document.getElementById('playlist').value;
			var gallery = document.getElementById('galleries');
			var album = jQuery('#album').val();
			var len = gallery.length;
			var galleryid="";
			if(!album){
				for(i=0;i<len;i++)
				{
					if(gallery.options[i].selected) {
						if(galleryid=="") {
							galleryid = " gid=" + galleryid + gallery.options[i].value;
						} else {
							galleryid = galleryid + "," + gallery.options[i].value;
						}
					}
				}
			} else {
				galleryname = jQuery('#album option:selected').text();
				album = ' album='+album;
			}
			if (gallerywidth && galleryheight)
				var gallerysize = " w=" + gallerywidth + " h=" + galleryheight;
			else
				var gallerysize="";
			
			if (galleryid == 'all') {
				if (galorderby) {
					var galorderby = " orderby=" + galorderby;
				} 
				if (galorder) {
					var galorder = " order=" + galorder;
				}
				if (galexclude) {
					var galexclude = " exclude=" + galexclude;
				} 
			} else {
				var galorderby = '';
				var galorder = '';
				var galexclude = '';
			}
			if (skinname) {
				var skinname = " skin=" + skinname;
			} else var skinname = '';
			if (playlist) {
				var skinname = " play=" + playlist;
			} else var playlist = '';

			if (galleryid || album ) {
				tagtext = '[flagallery' + galleryid + album + ' name="' + galleryname + '"' + gallerysize + galorderby + galorder + galexclude + skinname + playlist + ']';
				win.send_to_editor(tagtext);
				win.bind_resize();
			} else alert('Choose at least one gallery!');
		});
		jQuery(window).unload(function(){
			win.bind_resize();
		});
		/* ]]> */
	</script>
<?php } else { ?>
	</div>
	<div class="mceActionPanel">
		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'flag'); ?>" onclick="insertFLAGLink();" />
		</div>
	</div>
<?php } ?>
	<script type="text/javascript">
	/* <![CDATA[ */
	/*function setVisibility(){
	  jQuery('#custom_tab').css('display',(document.getElementById('gallerycustom').checked) ? 'block':'none');
	}*/
	jQuery('#galleries').change(function(){
		jQuery('#sort_tab').hide();
		if(jQuery('#galleries option[value=all]:selected')) {
			jQuery('#galleries option[value=all]:selected').siblings().removeAttr('selected');
		}
	});
	/* ]]> */
	</script>
</form>
</body>
</html>
