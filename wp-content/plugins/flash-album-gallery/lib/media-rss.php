<?php
/**
* Class to produce Media RSS nodes
* 
* @author 		Vincent Prat
* @copyright 	Copyright 2008
*/

require_once( dirname(__FILE__) . '/../flag-config.php');

class flagMediaRss {
	
	/**
	 * Function called by the wp_head action to output the RSS link for medias
	 */
	function add_mrss_alternate_link() {
		echo "<link id='MediaRSS' rel='alternate' type='application/rss+xml' title='GRAND FlAGallery RSS Feed' href='" . flagMediaRss::get_mrss_url() . "' />\n";		
	}
	
	/**
	 * Get the URL of the general media RSS
	 */
	function get_mrss_url() {	
		return FLAG_URLPATH . 'xml/media-rss.php';
	}
	
	/**
	 * Get the URL of a gallery media RSS
	 */
	function get_gallery_mrss_url($gid, $prev_next = false) {		
		return flagMediaRss::get_mrss_url() . '?' . ('gid=' . $gid . ($prev_next ? '&prev_next=true' : '') . '&mode=gallery');
	}
	
	/**
	 * Get the URL of the media RSS for last pictures
	 */
	function get_last_pictures_mrss_url($page = 0, $show = 30) {		
		return flagMediaRss::get_mrss_url() . '?' . ('show=' . $show . '&page=' . $page . '&mode=last_pictures');
	}
	
	/**
	 * Get the XML <rss> node corresponding to the last pictures registered
	 *
	 * @param page The current page (defaults to 0)
	 * @param show The number of pictures to include in one field (default 30) 
	 */
	function get_last_pictures_mrss($page = 0, $show = 30) {
		$images = flagdb::find_last_images($page, $show);
		
		$title = stripslashes(get_option('blogname'));
		$description = stripslashes(get_option('blogdescription'));
		$link = get_option('siteurl');
		$prev_link = ($page > 0) ? flagMediaRss::get_last_pictures_mrss_url($page-1, $show) : '';
		$next_link = count($images)!=0 ? flagMediaRss::get_last_pictures_mrss_url($page+1, $show) : '';
		
		return flagMediaRss::get_mrss_root_node($title, $description, $link, $prev_link, $next_link, $images);
	}
	
	/**
	 * Get the XML <rss> node corresponding to a gallery
	 *
	 * @param $gallery (object) The gallery to include in RSS
	 * @param $prev_gallery (object) The previous gallery to link in RSS (null if none)
	 * @param $next_gallery (object) The next gallery to link in RSS (null if none)
	 */
	function get_gallery_mrss($gallery, $prev_gallery = null, $next_gallery = null) {
		global $flag;
		
		$flag_options = get_option('flag_options');
		//Set sort order value, if not used (upgrade issue)
		$flag_options['galSort'] = ($flag_options['galSort']) ? $flag_options['galSort'] : 'pid';
		$flag_options['galSortDir'] = ($flag_options['galSortDir'] == 'DESC') ? 'DESC' : 'ASC';
	
		$title = stripslashes(flagGallery::i18n($gallery->title));
		$description = stripslashes(flagGallery::i18n($gallery->galdesc));
		$link = get_option('siteurl');
		$prev_link = ( $prev_gallery != null) ? flagMediaRss::get_gallery_mrss_url($prev_gallery->gid, true) : '';
		$next_link = ( $next_gallery != null) ? flagMediaRss::get_gallery_mrss_url($next_gallery->gid, true) : '';
		$images = flagdb::get_gallery($gallery->gid, $flag_options['galSort'], $flag_options['galSortDir']);

		return flagMediaRss::get_mrss_root_node($title, $description, $link, $prev_link, $next_link, $images);
	}
	
	/**
	 * Get the XML <rss> node
	 */
	function get_mrss_root_node($title, $description, $link, $prev_link, $next_link, $images) {	
		
		if ($prev_link != '' || $next_link != '')
			$out = "<rss version='2.0' xmlns:media='http://search.yahoo.com/mrss' xmlns:atom='http://www.w3.org/2005/Atom'>\n" ;
		else
			$out = "<rss version='2.0' xmlns:media='http://search.yahoo.com/mrss'>\n";
		
		$out .= "\t<channel>\n";
		
		$out .= flagMediaRss::get_generator_mrss_node();
		$out .= flagMediaRss::get_title_mrss_node($title);
		$out .= flagMediaRss::get_description_mrss_node($description);
		$out .= flagMediaRss::get_link_mrss_node($link);
				
		if ($prev_link!='') {
			$out .= flagMediaRss::get_previous_link_mrss_node($prev_link);
		}
		if ($next_link!='') { 
			$out .= flagMediaRss::get_next_link_mrss_node($next_link);
		} 
		
		foreach ($images as $image) {
			$out .= flagMediaRss::get_image_mrss_node($image);
		}
		
		$out .= "\t</channel>\n";
		$out .= "</rss>\n";
		
		return $out;
	}	
	
	/**
	 * Get the XML <generator> node
	 */
	function get_generator_mrss_node($indent = "\t\t") {	
		return $indent . "<generator><![CDATA[GRAND Flash Album Gallery [http://codeasily.com]]]></generator>\n";
	}	
	
	/**
	 * Get the XML <title> node
	 */
	function get_title_mrss_node($title, $indent = "\t\t") {	
		return $indent . "<title>" . $title . "</title>\n";
	}	
	
	/**
	 * Get the XML <description> node
	 */
	function get_description_mrss_node($description, $indent = "\t\t") {	
		return $indent . "<description>" . $description . "</description>\n";
	}	
	
	/**
	 * Get the XML <link> node
	 */
	function get_link_mrss_node($link, $indent = "\t\t") {	
		return $indent . "<link><![CDATA[" . htmlspecialchars($link) . "]]></link>\n";
	}	
	
	/**
	 * Get the XML <atom:link previous> node
	 */
	function get_previous_link_mrss_node($link, $indent = "\t\t") {	
		return $indent . "<atom:link rel='previous' href='" . htmlspecialchars($link) . "' />\n";
	}	
	
	/**
	 * Get the XML <atom:link next> node
	 */
	function get_next_link_mrss_node($link, $indent = "\t\t") {	
		return $indent . "<atom:link rel='next' href='" . htmlspecialchars($link) . "' />\n";
	}	
	
	/**
	 * Get the XML <item> node corresponding to one single image
	 *
	 * @param $image The image object
	 */
	function get_image_mrss_node($image, $indent = "\t\t" ) {
		global $flag;
		$flag_options = get_option('flag_options');
		
		$title = html_entity_decode(stripslashes($image->alttext));
		$desc = html_entity_decode(stripslashes($image->description));
		
		$thumbwidth = $flag_options['thumbWidth'];
		$thumbheight = ($flag_options['thumbFix'] ? $flag_options['thumbHeight'] : $thumbwidth); 	
		
		$out  = $indent . "<item>\n";
		$out .= $indent . "\t<title><![CDATA[" . flagGallery::i18n($title) . "]]></title>\n";
		$out .= $indent . "\t<description><![CDATA[" . flagGallery::i18n($desc) . "]]></description>\n";
		$out .= $indent . "\t<media:content url='" . $image->imageURL . "' medium='image' />\n";
		$out .= $indent . "\t<media:title><![CDATA[" . flagGallery::i18n($title) . "]]></media:title>\n";
		$out .= $indent . "\t<media:description><![CDATA[" . flagGallery::i18n($desc) . "]]></media:description>\n";
		$out .= $indent . "\t<media:thumbnail url='" . $image->thumbURL . "' width='" . $thumbwidth . "' height='" . $thumbheight . "' />\n";
		$out .= $indent . "\t<media:copyright><![CDATA[Copyright (c) " . get_option("blogname") . " (" . get_option("siteurl") . ")]]></media:copyright>\n";
		$out .= $indent . "</item>\n";

		return $out;
	}
	
}

?>