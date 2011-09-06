<?php global $wpdb;
$flag_options = get_option ('flag_options'); 
$siteurl = get_option ('siteurl');
$isCrawler = flagGetUserNow($_SERVER['HTTP_USER_AGENT']); // check if is a crowler
extract($altColors);
?>
<?php $bg = ($wmode == 'window')? '#'.$Background : 'transparent'; ?>
<style type="text/css">
.flashalbum { clear: both; }
.flag_alternate .flagcatlinks { padding: 7px 3px; margin:0 0 3px; background-color: #292929; }
.flag_alternate .flagcatlinks a.flagcat { padding: 4px 10px; margin: 0; border: none; border-left: 1px dotted #ffffff; font: 14px Tahoma; text-decoration: none; background: none; color: #ffffff; background-color: #292929; }
.flag_alternate .flagcatlinks a.flagcat:hover { text-decoration: none; background: none; border: none; border-left: 1px dotted #ffffff; }
.flag_alternate .flagcatlinks a.active, .flag_alternate .flagcatlinks a.flagcat:hover { color: #ffffff; background-color: #737373; outline: none; }
.flag_alternate .flagcatlinks a.flagcat:first-child { border: none; }
.flag_alternate .flagcategory { width: 100%; height: auto; position: relative; text-align: center; display: none; font-size: 0; line-height: 0; padding-bottom: 4px; }
.flag_alternate .flagcategory a { display: inline-block; margin: 1px 0 0 1px; padding: 0; height: 100px; width: 115px; line-height: 96px; position:relative; overflow: hidden; text-align: center; z-index:99; cursor:pointer; background-color: #ffffff; border: 2px solid #ffffff; text-decoration: none; background-image: url(<?php echo FLAG_URLPATH; ?>admin/images/loadingAnimation.gif); background-repeat: no-repeat; background-position: 50% 50%; font-size: 8px; color: #ffffff; }
.flag_alternate .flagcategory a:hover { background-color: #ffffff; border: 2px solid #4a4a4a; color: #4a4a4a; text-decoration: none; }
.flag_alternate .flagcategory a.current, .flag_alternate .flagcategory a.last { border-color: #4a4a4a; }
.flag_alternate .flagcategory a img { vertical-align: middle; display:inline-block; position: static; margin: 0 auto; padding: 0; border: none; height: 100px !important; width: 115px !important; }
.flag_alternate { background-color: <?php echo $bg; ?>; margin: 7px 0; display: none; }
</style>
<?php if($BarsBG) { 
$bgBar = ($wmode == 'window')? '#'.$BarsBG : 'transparent'; ?>
<style type="text/css">
.flag_alternate .flagcatlinks { background-color: #<?php echo $BarsBG; ?>; }
.flag_alternate .flagcatlinks a.flagcat { border-color: #<?php echo $CatColor; ?>; color: #<?php echo $CatColor; ?>; background-color: #<?php echo $CatBGColor; ?>; }
.flag_alternate .flagcatlinks a.flagcat:hover { border-color: #<?php echo $CatColor; ?>; }
.flag_alternate .flagcatlinks a.active, .flag_alternate .flagcatlinks a.flagcat:hover { color: #<?php echo $CatColorOver; ?>; background-color: #<?php echo $CatBGColorOver; ?>; }
.flag_alternate .flagcategory a { background-color: #<?php echo $ThumbBG; ?>; border: 2px solid #<?php echo $ThumbBG; ?>; color: #<?php echo $ThumbBG; ?>; }
.flag_alternate .flagcategory a:hover { background-color: #<?php echo $ThumbBG; ?>; border: 2px solid #<?php echo $ThumbLoaderColor; ?>; color: #<?php echo $ThumbLoaderColor; ?>; }
.flag_alternate .flagcategory a.current, .flag_alternate .flagcategory a.last { border-color: #<?php echo $ThumbLoaderColor; ?>; }
#fancybox-title-over .title { color: #<?php echo $TitleColor; ?>; }
#fancybox-title-over .descr { color: #<?php echo $DescrColor; ?>; }
</style>
<?php }; ?>
<script type="text/javascript">var ExtendVar='<?php echo FLAG_URLPATH; ?>';</script>
<div id="<?php echo $skinID; ?>_jq" class="flag_alternate">
		<div class="flagcatlinks"></div>
<?php
$gID = explode( '_', $galleryID ); // get the gallery id
if ( is_user_logged_in() ) $exclude_clause = '';
else $exclude_clause = ' AND exclude<>1 ';
foreach ( $gID as $galID ) {
	$galID = (int) $galID;
	if ( $galID == 0) {
		$thepictures = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE 1=1 {$exclude_clause} ORDER BY tt.{$flag_options['galSort']} {$flag_options['galSortDir']} ");
	} else {
		$thepictures = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = '{$galID}' {$exclude_clause} ORDER BY tt.{$flag_options['galSort']} {$flag_options['galSortDir']} ");
	}
	$captions = '';
?>
	<?php if (is_array ($thepictures) && count($thepictures)){ ?>
		<div class="flagCatMeta">
			<h4><?php echo esc_attr(stripslashes($thepictures[0]->title));?></h4>
			<p><?php echo esc_attr(stripslashes($thepictures[0]->galdesc));?></p>
		</div>
		<div class="flagcategory" id="gid_<?php echo $galID.'_'.$skinID; ?>">
			<?php $n = count($thepictures);
				$var = floor($n/5);
				if($var==0 || $var > 4) $var=4;
				$split = ceil($n/$var);
				$j=0;
		if ($isCrawler){
			foreach ($thepictures as $picture) { ?><a class="i<?php echo $j++; ?>" href="<?php echo $siteurl.'/'.$picture->path.'/'.$picture->filename; ?>" rel="gid_<?php echo $galID.'_'.$skinID; ?>"><img title="<?php echo esc_attr(stripslashes($picture->alttext)); ?>" alt="<?php echo esc_attr(stripslashes($picture->description)); ?>" src="<?php echo $siteurl.'/'.$picture->path.'/thumbs/thumbs_'.$picture->filename; ?>" width="115" height="100" /></a><?php 
			}
		} else {
			foreach ($thepictures as $picture) { ?><a class="i<?php echo $j++; ?>" href="<?php echo $siteurl.'/'.$picture->path.'/'.$picture->filename; ?>" rel="gid_<?php echo $galID.'_'.$skinID; ?>">[img title="<?php echo esc_attr(stripslashes($picture->alttext)); ?>" alt="<?php echo esc_attr(stripslashes($picture->description)); ?>" src="<?php echo $siteurl.'/'.$picture->path.'/thumbs/thumbs_'.$picture->filename; ?>"]</a><?php 
			}
		} ?>
		</div>
	<?php } ?>
<?php } ?>
</div>
