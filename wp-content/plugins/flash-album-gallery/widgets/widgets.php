<?php
/*
* GRAND FlAGallery Widget
*/

/**
 * flagSlideshowWidget - The slideshow widget control for GRAND FlAGallery ( require WP2.8 or higher)
 *
 * @package GRAND FlAGallery
 * @access public
 */
class flagSlideshowWidget extends WP_Widget {

	function flagSlideshowWidget() {
		$widget_ops = array('classname' => 'widget_slideshow', 'description' => __( 'Show a GRAND FlAGallery Slideshow', 'flag') );
		$this->WP_Widget('flag-slideshow', __('FLAGallery Slideshow', 'flag'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __('Slideshow', 'flag') : $instance['title'], $instance, $this->id_base);

		$out = $this->render_slideshow($instance['galleryid'] , $instance['width'] , $instance['height'] , $instance['skin']);

		if ( !empty( $out ) ) {
			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;
		?>
		<div class="flag_slideshow widget">
			<?php echo $out; ?>
		</div>
		<?php
			echo $after_widget;
		}

	}

	function render_slideshow($gid, $w = '100%', $h = '200', $skin = 'default') {
        $out = do_shortcode('[flagallery gid='.$gid.' name=\' \' w='.$w.' h='.$h.' skin='.$skin.']');
		return $out;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['galleryid'] = (int) $new_instance['galleryid'];
		$instance['height'] = (int) $new_instance['height'];
		$instance['width'] = $new_instance['width'];
		$instance['skin'] = $new_instance['skin'];

		return $instance;
	}

	function form( $instance ) {

		global $wpdb;

		require_once (dirname( dirname(__FILE__) ) . '/admin/get_skin.php');

		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Slideshow', 'galleryid' => '0', 'height' => '200', 'width' => '100%', 'skin' => 'default') );
		$title  = esc_attr( $instance['title'] );
		$height = esc_attr( $instance['height'] );
		$width  = esc_attr( $instance['width'] );
		$skin  = esc_attr( $instance['skin'] );
		$tables = $wpdb->get_results("SELECT * FROM $wpdb->flaggallery ORDER BY 'name' ASC ");
		$all_skins = get_skins();
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('galleryid'); ?>"><?php _e('Select Gallery:', 'flag'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('galleryid'); ?>" id="<?php echo $this->get_field_id('galleryid'); ?>" class="widefat">
<?php
				if($tables) {
					foreach($tables as $table) {
					echo '<option value="'.$table->gid.'" ';
					if ($table->gid == $instance['galleryid']) echo "selected='selected' ";
					echo '>'.$table->gid.' - '.$table->name.'</option>'."\n\t";
					}
				}
?>
				</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'flag'); ?></label> <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $height; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'flag'); ?></label> <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $width; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('skin'); ?>"><?php _e('Select Skin:', 'flag'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('skin'); ?>" id="<?php echo $this->get_field_id('skin'); ?>" class="widefat">
					<option value="" <?php if (0 == $instance['skin']) echo "selected='selected' "; ?> ><?php _e('Choose Skin', 'flag'); ?></option>
<?php
				if($all_skins) {
					foreach ( (array)$all_skins as $skin_file => $skin_data) {
						echo '<option value="'.dirname($skin_file).'"';
						if (dirname($skin_file) == $instance['skin']) echo ' selected="selected"';
						echo '>'.$skin_data['Name'].'</option>'."\n";
					}
				}
?>
				</select>
		</p>
<?php
	}

}

// register it
//add_action('widgets_init', create_function('', 'return register_widget("flagSlideshowWidget");'));


class flagBannerWidget extends WP_Widget {

	function flagBannerWidget() {
		$widget_ops = array('classname' => 'widget_banner', 'description' => __( 'Show a GRAND FlAGallery Banner', 'flag') );
		$this->WP_Widget('flag-banner', __('FLAGallery Banner', 'flag'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __('Banner', 'flag') : $instance['title'], $instance, $this->id_base);

		$out = $this->render_slideshow($instance['xml'] , $instance['width'] , $instance['height'] , $instance['skin']);

		if ( !empty( $out ) ) {
			echo $before_widget;
			if ( $title)
				echo $before_title . $title . $after_title;
		?>
		<div class="flag_banner widget">
			<?php echo $out; ?>
		</div>
		<?php
			echo $after_widget;
		}

	}

	function render_slideshow($xml, $w = '100%', $h = '200', $skin = '') {
        $out = do_shortcode('[grandbannerwidget xml='.$xml.' w='.$w.' h='.$h.' skin='.$skin.']');
		return $out;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['xml'] = $new_instance['xml'];
		$instance['height'] = (int) $new_instance['height'];
		$instance['width'] = $new_instance['width'];
		$instance['skin'] = $new_instance['skin'];

		return $instance;
	}

	function form( $instance ) {

		global $wpdb;

		require_once (dirname( dirname(__FILE__) ) . '/admin/get_skin.php');
		require_once (dirname( dirname(__FILE__) ) . '/admin/banner.functions.php');

		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Banner', 'xml' => '', 'width' => '100%', 'height' => '200', 'skin' => 'banner_widget_default') );
		$title  = esc_attr( $instance['title'] );
		$width  = esc_attr( $instance['width'] );
		$height = esc_attr( $instance['height'] );
		$skin  = esc_attr( $instance['skin'] );
		$all_playlists = get_b_playlists();
		$all_skins = get_skins(false,'w');
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('xml'); ?>"><?php _e('Select playlist:', 'flag'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('xml'); ?>" id="<?php echo $this->get_field_id('xml'); ?>" class="widefat">
<?php
	foreach((array)$all_playlists as $playlist_file => $playlist_data) {
		$playlist_name = basename($playlist_file, '.xml');
?>
					<option <?php selected($playlist_name , $instance['xml']); ?> value="<?php echo $playlist_name; ?>"><?php echo $playlist_data['title']; ?></option>
<?php
	}
?>
				</select>
		</p>
		<p><label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'flag'); ?></label> <input id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $height; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'flag'); ?></label> <input id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" style="padding: 3px; width: 45px;" value="<?php echo $width; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('skin'); ?>"><?php _e('Select Skin:', 'flag'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('skin'); ?>" id="<?php echo $this->get_field_id('skin'); ?>" class="widefat">
<?php
				if($all_skins) {
					foreach ( (array)$all_skins as $skin_file => $skin_data) {
						echo '<option value="'.dirname($skin_file).'"';
						if (dirname($skin_file) == $instance['skin']) echo ' selected="selected"';
						echo '>'.$skin_data['Name'].'</option>'."\n";
					}
				}
?>
				</select>
		</p>
<?php
	}

}

// register it
add_action('widgets_init', create_function('', 'return register_widget("flagBannerWidget");'));


/**
 * flagWidget - The widget control for GRAND FlAGallery
 *
 * @package GRAND FlAGallery
 * @access public
 */
class flagWidget extends WP_Widget {
    
   	function flagWidget() {
		$widget_ops = array('classname' => 'flag_images', 'description' => __( 'Add recent or random images from the galleries', 'flag') );
		$this->WP_Widget('flag-images', __('FLAGallery Widget', 'flag'), $widget_ops);
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title']	= strip_tags($new_instance['title']);
		$instance['type']	= $new_instance['type'];
		$instance['width']	= (int) $new_instance['width'];
		$instance['height']	= (int) $new_instance['height'];
		$instance['fwidth']	= (int) $new_instance['fwidth'];
		$instance['fheight']	= (int) $new_instance['fheight'];
		$instance['album']	= (int) $new_instance['album'];
		$instance['skin']	= $new_instance['skin'];

		return $instance;
	}

	function form( $instance ) {
		global $wpdb, $flagdb;

		require_once (dirname( dirname(__FILE__) ) . '/admin/get_skin.php');

		$all_skins = get_skins();

		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 
            'title' => 'Galleries',
            'type'  => 'random',
            'width' => '75',
            'height'=> '65',
            'fwidth' => '640',
            'fheight'=> '480',
            'album' =>  '',
			'skin'	=> '' ) );
		$title  = esc_attr( $instance['title'] );
		$width  = esc_attr( $instance['width'] );
        $height = esc_attr( $instance['height'] );
		$fwidth  = esc_attr( $instance['fwidth'] );
        $fheight = esc_attr( $instance['fheight'] );

		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :','flag'); ?>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title');?>" type="text" class="widefat" value="<?php echo $title; ?>" />
			</label>
		</p>
			
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>_random">
			<input id="<?php echo $this->get_field_id('type'); ?>_random" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="random" <?php checked("random" , $instance['type']); ?> /> <?php _e('random','flag'); ?>
			</label>
            <label for="<?php echo $this->get_field_id('type'); ?>_first">
            <input id="<?php echo $this->get_field_id('type'); ?>_first" name="<?php echo $this->get_field_name('type'); ?>" type="radio" value="recent" <?php checked("recent" , $instance['type']); ?> /> <?php _e('first in album','flag'); ?>
			</label>
		</p>

		<p>
			<?php _e('Width x Height of thumbs:','flag'); ?><br />
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" /> x
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" /> (px)
		</p>

		<p>
			<?php _e('Width x Height of popup:','flag'); ?><br />
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id('fwidth'); ?>" name="<?php echo $this->get_field_name('fwidth'); ?>" type="text" value="<?php echo $fwidth; ?>" /> x
			<input style="width: 50px; padding:3px;" id="<?php echo $this->get_field_id('fheight'); ?>" name="<?php echo $this->get_field_name('fheight'); ?>" type="text" value="<?php echo $fheight; ?>" /> (px)
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('album'); ?>"><?php _e('Select Album:','flag'); ?>
			<select id="<?php echo $this->get_field_id('album'); ?>" name="<?php echo $this->get_field_name('album'); ?>" class="widefat">
				<option value="" ><?php _e('Choose album','flag'); ?></option>
			<?php
				$albumlist = $flagdb->find_all_albums();
				if(is_array($albumlist)) {
					foreach($albumlist as $album) { ?>
						<option <?php selected( $album->id , $instance['album']); ?> value="<?php echo $album->id; ?>"><?php echo $album->name; ?></option>
					<?php }
				}
			?>
			</select>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('skin'); ?>"><?php _e('Select Skin:', 'flag'); ?></label>
				<select size="1" name="<?php echo $this->get_field_name('skin'); ?>" id="<?php echo $this->get_field_id('skin'); ?>" class="widefat">
<?php
				if($all_skins) {
					foreach ( (array)$all_skins as $skin_file => $skin_data) {
						echo '<option value="'.dirname($skin_file).'"';
						if (dirname($skin_file) == $instance['skin']) echo ' selected="selected"';
						echo '>'.$skin_data['Name'].'</option>'."\n";
					}
				}
?>
				</select>
		</p>

	<?php

	}

	function widget( $args, $instance ) {
		global $wpdb, $flagdb;

		extract( $args );

        $title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title'], $instance, $this->id_base);

		$album = $instance['album'];

       	$gallerylist = $flagdb->get_album($album);
        $ids = explode( ',', $gallerylist );
		$gids = str_replace(',','_',$gallerylist);
   		foreach ($ids as $id) {
			if ( $instance['type'] == 'random' )
				$imageList[$id] = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE tt.exclude != 1 AND t.gid = {$id} ORDER by rand() LIMIT 1");
			else
				$imageList[$id] = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->flaggallery AS t INNER JOIN $wpdb->flagpictures AS tt ON t.gid = tt.galleryid WHERE tt.exclude != 1 AND t.gid = {$id} ORDER by tt.sortorder ASC LIMIT 1");
   		}
		echo $before_widget . $before_title . $title . $after_title;
		echo "\n" . '<div class="flag-widget">'. "\n";

		if (is_array($imageList)){
			foreach($imageList as $key => $image) {
				// get the URL constructor
				$image = new flagImage($image[0]);

				// get the effect code
				$thumbcode = 'class="flag_fancybox"';
				
				// enable i18n support for alttext and description
				$alttext      =  htmlspecialchars( stripslashes( flagGallery::i18n($image->alttext, 'pic_' . $image->pid . '_alttext') ));
				$description  =  htmlspecialchars( stripslashes( flagGallery::i18n($image->description, 'pic_' . $image->pid . '_description') ));

				//TODO:For mixed portrait/landscape it's better to use only the height setting, if widht is 0 or vice versa
				$out = '<a href="'.home_url().'/wp-content/plugins/flash-album-gallery/facebook.php?i='.$image->galleryid.'&amp;f='.$instance['skin'].'&amp;h='.$instance['fheight'].'" title="' . $image->title . '" ' . $thumbcode .'>';
				$out .= '<img src="'.$image->thumbURL.'" width="'.$instance['width'].'" height="'.$instance['height'].'" title="'.$alttext.'" alt="'.$alttext.'" />';
				echo $out . '</a>'."\n";

			}
		}

		echo '</div>'."\n";
		echo '<style type="text/css">.flag_fancybox img { border: 1px solid #A9A9A9; margin: 0 2px 2px 0; padding: 1px; }</style>'."\n";
		echo '<script type="text/javascript">var fbVar = "'.FLAG_URLPATH.'"; var fbW = '.$instance['fwidth'].', fbH = '.$instance['fheight'].'; waitJQ(fbVar,fbW,fbH);</script>'."\n";
		echo $after_widget;
		
	}

}// end widget class

// register it
add_action('widgets_init', create_function('', 'return register_widget("flagWidget");'));

/**
 * flagSlideshowWidget($galleryID, $width, $height)
 * Function for templates without widget support
 * 
 * @param integer $galleryID 
 * @param string $width
 * @param string $height
 * @return echo the widget content
 */
function flagSlideshowWidget($gid, $w = '100%', $h = '200', $skin = 'default') {

	echo flagSlideshowWidget::render_slideshow($gid, $w, $h, $skin);

}

function flagBannerWidget($xml, $w = '100%', $h = '200', $skin = 'default') {

	echo flagBannerWidget::render_slideshow($xml, $w, $h, $skin);

}

/**
 * flagDisplayRandomImages($number,$width,$height,$exclude,$list,$show)
 * Function for templates without widget support
 *
 * @return echo the widget content
 */
function flagDisplayRandomImages($number, $width = '75', $height = '65', $exclude = 'all', $list = '', $show = 'thumbnail') {
	
	$options = array(   'title'    => false, 
						'items'    => $number,
						'show'     => $show ,
						'type'     => 'random',
						'width'    => $width, 
						'height'   => $height, 
						'exclude'  => $exclude,
						'list'     => $list,
                        'webslice' => false );
                        
	$flag_widget = new flagWidget();
	$flag_widget->widget($args = array( 'widget_id'=> 'sidebar_1' ), $options);
}

/**
 * flagDisplayRecentImages($number,$width,$height,$exclude,$list,$show)
 * Function for templates without widget support
 *
 * @return echo the widget content
 */
function flagDisplayRecentImages($number, $width = '75', $height = '50', $exclude = 'all', $list = '', $show = 'thumbnail') {

	$options = array(   'title'    => false, 
						'items'    => $number,
						'show'     => $show ,
						'type'     => 'recent',
						'width'    => $width, 
						'height'   => $height, 
						'exclude'  => $exclude,
						'list'     => $list,
                        'webslice' => false );
                        
	$flag_widget = new flagWidget();
	$flag_widget->widget($args = array( 'widget_id'=> 'sidebar_1' ), $options);
}

?>