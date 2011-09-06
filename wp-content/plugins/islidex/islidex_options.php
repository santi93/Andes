<?php
$islidex_options = get_option('islidex');
// Debug
//$usecaption = $islidex_options['usecaption'];
//$theme = $islidex_options['theme'];
//$wtheme = $islidex_options['widget_theme'];
//$numpost = $islidex_options['num_post'];
//$piece = $islidex_options['piece_shadow'];
//$usew = $islidex_options['usewidget'];
//echo '. t - '.$theme;
//echo '. wt - '.$wtheme;
//echo '. c - '.$usecaption;
//echo '. s - '.$numpost;
//echo '. p - '.$piece;
//echo '. wu - '.$usew;
//
?>

<script type="text/javascript">
  jQuery(document).ready(function() {
	jQuery('div.credits').hide();
	jQuery('h3.ciccio').toggleClass('open');
	<?php
	/*if (($islidex_options['usewidget']) == 1)	{
	echo "jQuery('div.widget').show();
    jQuery('h3.widg').toggleClass('open');";
	} else {*/
	echo "jQuery('div.widget').hide();
    jQuery('h3.widg').toggleClass('open, Remove');";
	//}
	if (($islidex_options['theme']) == 'Nivo')	{
	echo "jQuery('table#piecemaker').hide();
	jQuery('table#greek').hide();
	jQuery('table#nivo').show();";
	} elseif (($islidex_options['theme']) == 'Piecemaker') {
	echo "jQuery('table#nivo').hide();
	jQuery('table#greek').hide();
	jQuery('table#piecemaker').show();";
	} elseif (($islidex_options['theme']) == 'Greek') {
	echo "jQuery('table#nivo').hide();
	jQuery('table#piecemaker').hide();
	jQuery('table#greek').show();";
	} else {
	echo "jQuery('table#piecemaker').hide();
	jQuery('table#nivo').hide();
	jQuery('table#greek').hide();
	";
	}
	if (($islidex_options['widget_theme']) == 'Nivo') {
	echo "jQuery('table#wnivo').show();";
	} else {
	echo "jQuery('table#wnivo').hide();";
	}
	?>
	
	jQuery('option#Apple_toggle').click(function() {
	jQuery('table#piecemaker').slideUp('fast');
	jQuery('table#nivo').slideUp('fast');
	jQuery('table#greek').slideUp('fast');
      return false;
    });

	jQuery('option#Timeline_toggle').click(function() {
	jQuery('table#piecemaker').slideUp('fast');
	jQuery('table#nivo').slideUp('fast');
	jQuery('table#greek').slideUp('fast');
      return false;
    });
	
	jQuery('option#Nivo_toggle').click(function() {
	jQuery('table#piecemaker').slideUp('fast');
	jQuery('table#nivo').toggleClass("open");
    jQuery("table#nivo").slideDown('slow');
	jQuery('table#greek').slideUp('fast');
      return false;
    });

	jQuery('option#Piecemaker_toggle').click(function() {
	jQuery('table#nivo').slideUp('fast');
	jQuery('table#piecemaker').toggleClass("open");
    jQuery("table#piecemaker").slideDown('slow');
	jQuery('table#greek').slideUp('fast');
      return false;
    });

	jQuery('option#Greek_toggle').click(function() {
	jQuery('table#nivo').slideUp('fast');
	jQuery('table#greek').toggleClass("open");
    jQuery("table#greek").slideDown('slow');
	jQuery('table#piecemaker').slideUp('fast');
      return false;
    });

	jQuery('option#wNivo_toggle').click(function() {
	jQuery('table#wnivo').toggleClass("open");
    jQuery("table#wnivo").slideDown('slow');
      return false;
    });

	jQuery('option#wApple_toggle').click(function() {
	jQuery('table#wnivo').slideUp('fast');
      return false;
    });

	jQuery('option#wTimeline_toggle').click(function() {
	jQuery('table#wnivo').slideUp('fast');
      return false;
    });

    jQuery('h3').click(function() {
      jQuery(this).toggleClass("open");
      jQuery(this).next("div").slideToggle('1000');
      return false;
    });

    jQuery("#picker1").farbtastic("#colorpickerField1");
    jQuery("#picker2").farbtastic("#colorpickerField2");
  });
</script>

<div id="islidex_options" class="islidex_options">

	<h2>iSlidex Settings</h2>
	
<form method="post" action="options.php">
<?php settings_fields('islidex_options');
$islidex_options = get_option('islidex'); ?>

<div class="islidex_options-body">

  <table class="form-table">

    <tr valign="top">
      <th scope="row">Select Category</th>
      <td><?php wp_dropdown_categories(array('show_option_none' => 'Select Category', 'name' => 'islidex[category_id]', 'selected' => $islidex_options['category_id'])); ?>
        <p>This is where iSlidex will look for the posts (and their images) to include in the slider</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Number of Slides to show</th>
      <td><input type="text" name="islidex[num_post]" value="<?php echo ($islidex_options['num_post'])?$islidex_options['num_post']:'5'; ?>"/>
      </td>
    </tr>

	<tr valign="top">
      <th scope="row">Width</th>
      <td><input type="text" name="islidex[slide_size_w]" value="<?php echo ($islidex_options['slide_size_w'])?$islidex_options['slide_size_w']:'560'; ?>"/>
        <p>Add width of slideshow in px (don't type px at the end, just the number).</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Height</th>
      <td><input type="text" name="islidex[slide_size_h]" value="<?php echo ($islidex_options['slide_size_h'])?$islidex_options['slide_size_h']:'374'; ?>"/>
        <p>Add height of slide show in px (don't type px at the end, just the number).</p>
      </td>
    </tr>

	<tr valign="top">
      <th scope="row">Captions</th>
		<td><input name="islidex[usecaption]" value="1" <?php echo ($islidex_options['usecaption']) ? 'checked="checked"' : ''; ?> type="checkbox"/>
        <p>If checked, will display post title below the slide. Please mind that: this doesn't work with Piecemaker and it will also affect the widget.</p>
		</td>
      </td>
    </tr>

	<tr valign="top">
      <th scope="row">Linked Slides</th>
		<td><input name="islidex[linked]" value="1" <?php echo ($islidex_options['linked']) ? 'checked="checked"' : ''; ?> type="checkbox"/>
        <p>If checked, each slide will link to its post.</p>
		</td>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Theme <?php //echo '-'($islidex_options['theme']); //debug?></th>
      <td>
		<?php $themename = array('Apple', 'Nivo', 'Piecemaker', 'Timeline', 'Greek');
		echo '<select class="inputbox" name="islidex[theme]">';
		foreach ($themename as $tn) {
			if ($islidex_options['theme'])
			$checked = ($islidex_options['theme'] == $tn) ? 'selected="selected"' : '';
			else
			$checked = ('Apple' == $tn) ? 'selected="selected"' : '';
			echo '<option value="' . $tn . '" ' . $checked . ' id="'.$tn.'_toggle">' . $tn . '</option>';
		  }
		echo '</select>'; ?>
		</td>
    </tr>

	<table id="greek" class="form-table">
		
		<tr valign="top">
			<th scope="row"></th>
			<td><h4>Greek custom settings</h4></td>
	    </tr>

		<tr valign="top">
			<th scope="row"><label for="islidex[greekslidew]">Slide Width</label></th>
			<td>
			<input type="text" name="islidex[greekslidew]" value="<?php echo ($islidex_options['greekslidew'])?$islidex_options['greekslidew']:'250'; ?>"/>
			<p>The individual size of each post slide in px (don't type px at the end, just the number).</p>
			</td>
		</tr>
	
	</table>

	<table id="nivo" class="form-table">
		
		<tr valign="top">
			<th scope="row"></th>
			<td><h4>Nivo custom settings</h4></td>
	    </tr>

		<tr valign="top">
			<th scope="row"><label for="islidex[nivoeffect]">Effect for Nivo theme</label></th>
			<td>
			<?php $nivoeffects = array('sliceDown', 'sliceDownLeft', 'sliceUp', 'sliceUpLeft', 'sliceUpDown', 'sliceUpDownLeft', 'fold', 'fade', 'random');
			echo '<select class="inputbox" name="islidex[nivoeffect]">';
			foreach ($nivoeffects as $ne) {
				if ($islidex_options['nivoeffect'])
				$checked = ($islidex_options['nivoeffect'] == $ne) ? 'selected="selected"' : '';
				else
				$checked = ('slideDown' == $ne) ? 'selected="selected"' : '';
				echo '<option value="' . $ne . '" ' . $checked . '>' . $ne . '</option>';
			}
			echo '</select>'; ?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Slide Transition Speed</th>
			<td>
				<input type="text" name="islidex[nivo_transpeed]" value="<?php echo ($islidex_options['nivo_transpeed']) ? $islidex_options['nivo_transpeed'] : '500'; ?>"/>
				<p>How fast the slides... slide, in seconds.</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Slide Permanence</th>
			<td>
				<input type="text" name="islidex[nivo_pausetime]" value="<?php echo ($islidex_options['nivo_pausetime']) ? $islidex_options['nivo_pausetime'] : '3000'; ?>"/>
				<p>How long each slide is showed for, in seconds.</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">Disable Autoplay</th>
			<td>
				<input name="islidex[nivo_auto]" value="1" <?php echo ($islidex_options['nivo_auto']) ? 'checked="checked"' : ''; ?> type="checkbox"/>
				<p>Check to disable slides autoplay</p>
			</td>
		</tr>
	
	</table>

	<table id="piecemaker" class="form-table">
		
		<tr valign="top">
			<th scope="row"></th>
			<td><h4>Piecemaker custom settings</h4></td>
	    </tr>

		<tr valign="top">
			<th scope="row">AutoPlay</th>
			<td>
				<input type="text" name="islidex[piece_autoplay]" value="<?php echo ($islidex_options['piece_autoplay']) ? $islidex_options['piece_autoplay'] : '3'; ?>"/>
				<p>Number of seconds to the next slide. Type 0 to disable autoplay</p>
			</td>
		</tr>

		<tr valign="top">
		  <th scope="row">Rotation effect for Piecemaker theme</th>
		  <td>
		  <?php $tweenTypes = array('linear', 'easeInQuad', 'easeOutQuad', 'easeInOutQuad', 'easeInCubic', 'easeOutCubic', 'easeInOutCubic', 'easeInQuart', 'easeOutQuart', 'easeInOutQuart', 'easeInQuint', 'easeOutQuint', 'easeInOutQuint', 'easeInSine', 'easeOutSine', 'easeInOutSine', 'easeInExpo', 'easeOutExpo', 'easeInOutExpo', 'easeInCirc', 'easeOutCirc', 'easeInOutCirc', 'easeInElastic', 'easeOutElastic', 'easeInOutElastic', 'easeInBack', 'easeOutBack', 'easeInOutBack', 'easeInBounce', 'easeOutBounce', 'easeInOutBounce');
		  echo '<select class="inputbox" name="islidex[piece_tweentype]">';
		  foreach ($tweenTypes as $tt) {
			if ($islidex_options['piece_tweentype'])
			  $checked = ($islidex_options['piece_tweentype'] == $tt) ? 'selected="selected"' : '';
			else
			  $checked = ('easeInOutBack' == $tt) ? 'selected="selected"' : '';
			echo '<option value="' . $tt . '" ' . $checked . ' >' . $tt . '</option>';
		  }
		  echo '</select>';
		  ?>
			<p>Type of transition (Tween Type).</p>
		  </td>
		</tr>
		
		<tr valign="top">
      <th scope="row">Segments</th>
      <td><input type="text" name="islidex[piece_segments]" value="<?php echo ($islidex_options['piece_segments']) ? $islidex_options['piece_segments'] : '7'; ?>"/>
	      <p>Number of segments in which the images will be sliced.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Tween Time</th>
      <td><input type="text" name="islidex[piece_tweentime]" value="<?php echo ($islidex_options['piece_tweentime']) ? $islidex_options['piece_tweentime'] : '1.2'; ?>"/>
        <p>Number of seconds for each element to be turned.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Tween Delay</th>
      <td><input type="text" name="islidex[piece_tweendelay]" value="<?php echo ($islidex_options['piece_tweendelay']) ? $islidex_options['piece_tweendelay'] : '0.1'; ?>"/>
        <p>Number of seconds between each segment rotation.</p>
      </td>
    </tr>

		<!-- <tr valign="top">
			<th scope="row">Shadow</th>
			<td>
			<input name="islidex[piece_shadow]" value="1" <?php //echo ($islidex_options['piece_shadow']) ? 'checked="checked"' : ''; ?> type="checkbox"/>
		    <p>If checked, Piecemaker will have a shadow underneath (please allow a larger space to display this)</p>
			</td>
		</tr> -->

			<tr valign="top">
      <th scope="row">Z Distance</th>
      <td><input type="text" name="islidex[piece_zdistance]" value="<?php echo ($islidex_options['piece_zdistance']) ? $islidex_options['piece_zdistance'] : '500'; ?>"/>
        <p>The extent the segments move on the z-axis when being rotating. Negative values bring the slices closer to the PC, positive values take it further away. A good range is roughly between -200 and 700.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Expand</th>
      <td><input type="text" name="islidex[piece_expand]" value="<?php echo ($islidex_options['piece_expand']) ? $islidex_options['piece_expand'] : '20'; ?>"/>
        <p>To which extent the segments move away from each other when rotating.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Inner Color</th>
      <td><input id="colorpickerField1" type="text" name="islidex[piece_innercolor]" value="<?php echo ($islidex_options['piece_innercolor']) ? $islidex_options['piece_innercolor'] : '#111111'; ?>"/><br/>
        <div style="position:relative;" id="picker1"></div>
        <p>Select color of the inner sides of the segments.</p>
      </td>
    </tr>
  
	<tr valign="top">
      <th scope="row">Text Background Color</th>
      <td><input id="colorpickerField2" type="text" name="islidex[piece_textbackground]" value="<?php echo ($islidex_options['piece_textbackground']) ? $islidex_options['piece_textbackground'] : '#0064C8'; ?>"/><br/>
        <div style="position:relative;" id="picker2">&nbsp;</div>
        <p>Select color of the text box background.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Shadow Darkness</th>
      <td><input type="text" name="islidex[piece_shadowdarkness]" value="<?php echo ($islidex_options['piece_shadowdarkness']) ? $islidex_options['piece_shadowdarkness'] : '0'; ?>"/>
        <p>Value of shadowing when the segments are rotating and moving towards the background. 100 is black, 0 is no shadow.</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Text Distance</th>
      <td><input type="text" name="islidex[piece_textdistance]" value="<?php echo ($islidex_options['piece_textdistance']) ? $islidex_options['piece_textdistance'] : '25'; ?>"/>
        <p>Distance of the info text to the borders of the box</p>
      </td>
    </tr>

	</table> <!-- fine opzioni piecemaker -->	
   

  
	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
</p>



<!-- Widget -->


<h3 class="widg">Widget Settings</h3>

<div class="islidex_options-body widget">

  <table class="form-table">

		<!--	<tr valign="top">
				<th scope="row">Do you want to use the widget?</th>
				<td>
					<input name="islidex[usewidget]" value="1" <?php //echo ($islidex_options['usewidget']) ? 'checked="checked"' : ''; ?> type="checkbox"/>
					<p>If checked, it will show in the available widgets list.</p>
				</td>
			</tr>		-->

     <tr valign="top">
      <th scope="row">Select Category</th>
      <td><?php wp_dropdown_categories(array('show_option_none' => 'Select Category', 'name' => 'islidex[widget_cat]', 'selected' => $islidex_options['widget_cat'])); ?>
        <p>This is where iSlidex will look for the posts (and their images) to include in the slider</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Number of Slides to show</th>
      <td><input type="text" name="islidex[widget_num_post]" value="<?php echo ($islidex_options['widget_num_post'])?$islidex_options['widget_num_post']:'5'; ?>"/>
      </td>
    </tr>

	<tr valign="top">
      <th scope="row">Width</th>
      <td><input type="text" name="islidex[widget_size_w]" value="<?php echo ($islidex_options['widget_size_w'])?$islidex_options['widget_size_w']:'560'; ?>"/>
        <p>Add width of slideshow in px (don't type px at the end, just the number).</p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Height</th>
      <td><input type="text" name="islidex[widget_size_h]" value="<?php echo ($islidex_options['widget_size_h'])?$islidex_options['widget_size_h']:'374'; ?>"/>
        <p>Add height of slide show in px (don't type px at the end, just the number).</p>
      </td>
    </tr>
	
	<tr valign="top">
      <th scope="row">Linked Slides</th>
		<td><input name="islidex[wlinked]" value="1" <?php echo ($islidex_options['wlinked']) ? 'checked="checked"' : ''; ?> type="checkbox"/>
        <p>If checked, each slide will link to its post.</p>
		</td>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row">Theme</th>
      <td>
		<?php $wthemename = array('Apple', 'Nivo');
		echo '<select class="inputbox" name="islidex[widget_theme]">';
		foreach ($wthemename as $wtn) {
			if ($islidex_options['widget_theme'])
			$wchecked = ($islidex_options['widget_theme'] == $wtn) ? 'selected="selected"' : '';
			else
			$wchecked = ('Apple' == $wtn) ? 'selected="selected"' : '';
			echo '<option value="' . $wtn . '" ' . $wchecked . ' id="w'.$wtn.'_toggle">' . $wtn . '</option>';
		  }
		echo '</select>'; ?>
		<p>Piecemaker is not available for widgets as there wouldn't be enough space to display it.</p>
	</td>
    </tr>
	
	<table id="wnivo" class="form-table">
		
		<tr valign="top">
			<th scope="row"></th>
			<td><h4>Nivo custom settings</h4></td>
	    </tr>

		<tr valign="top">
			<th scope="row"><label for="islidex_wnivoeffect">Effect for Nivo widget theme</label></th>
			<td>
			<?php $wnivoeffects = array('sliceDown', 'sliceDownLeft', 'sliceUp', 'sliceUpLeft', 'sliceUpDown', 'sliceUpDownLeft', 'fold', 'fade', 'random');
			echo '<select class="inputbox" name="islidex[wnivoeffect]">';
			foreach ($wnivoeffects as $wne) {
				if ($islidex_options['wnivoeffect'])
				$wchecked = ($islidex_options['wnivoeffect'] == $wne) ? 'selected="selected"' : '';
				else
				$wchecked = ('slideDown' == $wne) ? 'selected="selected"' : '';
				echo '<option value="' . $wne . '" ' . $wchecked . '>' . $wne . '</option>';
			}
			echo '</select>'; ?>
			</td>
			</tr>
	</table>

 </table>
  
 <p class="submit">
 <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
</p>
</div>
</form>


<!-- Other -->

	<h3 class="ciccio">Usage</h3>
	<div class="islidex_options-body support usage admin-info">
		<p>Copy and paste <code>[islidex]</code> in your posts or pages, where you want iSlidex to be.
		<br />
		If you want to use it directly in the template code, use <code>&lt;?php show_islidex()?&gt;</code>.</p>
		<h4>Individual Slides/Thumbs Customization</h4>
		<p>To customize exactly what slide/thumb to show for each post, add inside each individual post you want to customize, these custom fields: <code>islidex_slide</code> (to cutomize the slide) and/or <code>islidex_thumb</code> (to customize the thumb) as field names, and as value insert the direct link to the custom image. Images MUST exist inside your domain. Thumbs will only be shown in the Apple theme.
		<br />
		If you don't add these fields, iSlidex will take a random image automatically from each post, so only use the custom fields if you want to cutomize a post with a specific slide and/or thumb.
		<br />
		If, for some reason, inside a post there's no image whatsoever, iSlidex will use a cute WP logo to replace both the slide and the thumb.</p>      
		</p>
		<h4>Custom iSlidex</h4>
		<p>This function lets you add as many iSlidex as you want across your site, all with different settings, indipendent from this page settings or other iSlidex in your site.
		<br />
		Please remember that you can have max 3 iSlidex in the same page, but they can't be of the same type (eg. you can have 1 Nivo widget + 1 Nivo standard + 1 custom Nivo, but you can't have 2 custom Nivos or 2 standard Apple or 2 widgets with same style).
		<br />
		<br />
		Copy and paste <code>[islidex_custom cat=5 num=3 w=450 h=200 theme=1]</code> in your posts or pages. Remember to change the numbers depending on the values you prefer.
		If you want to use it directly in the template code, use <code>&lt;?php show_customislidex(5,3,450,200,1, true); ?&gt;</code>.
		<br />
		The numbers '5, 3, 450, 200, 1, true' are only an example here, you must change them depending on the values you prefer.
		<br />These numbers represent, in order: <strong>cat</strong>egory (where to get the posts from), max <strong>num</strong>ber of posts/slides (to show in iSlidex), <strong>w</strong>idth (of the slider), <strong>h</strong>eight (of the slider), <strong>theme</strong> (1 is Apple, 2 is Nivo, 3 is Piecemaker), <strong>auto_adv</strong> (true or false if you want slides auto advance, true is default).. Please replace the numbers with the settings you prefer, each time you want to add a new slider in a different location.
		The parameter <code>cat=</code> can take also multiple categories, separated by comma. Just type for example <code>cat=3,5</code>.
		</p>
		<h4>Themes</h4>
		<p>To adjust the spaces around the Piecemaker theme better for both the standard iSlidex and the custom one, please style the respective classes <em>mypiecemaker</em> and <em>mycustompiecemaker</em>. Save the style to your own style.css in your site's template folder (not in iSlidex folder). In case you see no difference, try adding also <em>!important</em> to your css statements.</p>
	</div>

	<h2>Support</h2>
	<div class="admin-info support blu">
		<p>If you can't make it work, BEFORE you report it as broken, take some time and read again the Usage and the plugin FAQ and Forum.
		<br />You can leave a comment about the plugin, ask questions, say thank you or add your piece of code at <strong><a href="http://www.shambix.com/en/news/wordpress-plugin-islidex/" target="_blank">iSlidex official blog post</a></strong>.
		<br />We don't just specialize in Wordpress, we also develop Web &amp; Mobile Apps. Contact us at <a href="mailto:info@shambix.com">info@shambix.com</a> for a quote and include the code "ISLIDEX20" in your email, for a <strong>20% off your first project with us</strong>!</p>

		<?php
		define ('ISLIDEX_PLUGIN_BASENAME', 	plugin_basename(dirname(__FILE__)));
		define ('ISLIDEX_PLUGIN_URL', 		WP_PLUGIN_URL		."/".ISLIDEX_PLUGIN_BASENAME);
		define ('ISLIDEX_PLUGIN_IMAGES', 	ISLIDEX_PLUGIN_URL	."/img");
		$page_path 		= ISLIDEX_PLUGIN_IMAGES . '/islidex_page.png';
		$logo_path 			= ISLIDEX_PLUGIN_IMAGES . '/poweredbyshambix.png';
		$wpcons_path 		= ISLIDEX_PLUGIN_IMAGES . '/wp_consultant.png';
		$instructions_path 	= ISLIDEX_PLUGIN_IMAGES . '/islidex_instructions.png';
		$faq_path 			= ISLIDEX_PLUGIN_IMAGES . '/islidex_faq.png';
		$support_path 		= ISLIDEX_PLUGIN_IMAGES . '/islidex_support.png';
		$donate_path 		= ISLIDEX_PLUGIN_IMAGES . '/donate_buy_coffee.jpg';
		$cheat_path 		= ISLIDEX_PLUGIN_IMAGES . '/wp_cheatsheet.png';
		?>

		<div style="background:none repeat scroll 0 0 #FFFFFF;border:3px dotted #AAAAAA;bottom:0;padding:10px;text-align:center;">
			<div style="background: none repeat scroll 0 0 #F0F0F0;">
				<a href="http://www.shambix.com/en/news/wordpress-plugin-islidex/" title="Official iSlidex page on Shambix" target="_blank"><img src="<?php echo $page_path; ?>" alt="Official iSlidex page on Shambix"></a>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="http://wordpress.org/extend/plugins/islidex/installation/" title="Check the updated usage instructions here" target="_blank"><img src="<?php echo $instructions_path; ?>" alt="Look for Shambix | Design&amp;Marketing Consulting"></a>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="http://wordpress.org/extend/plugins/islidex/faq/" title="Got a question? Maybe we already have an answer here!" target="_blank"><img src="<?php echo $faq_path; ?>" alt="Look for Shambix | Design&amp;Marketing Consulting"></a>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="http://wordpress.org/tags/islidex?forum_id=10" title="Got any issues? Need help? Check the forums or post a new topic!" target="_blank"><img src="<?php echo $support_path; ?>" alt="Look for Shambix | Design&amp;Marketing Consulting"></a>
			</div>
			<br />
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="3UR3E5PL3TW3E">
			<input type="image" src="<?php echo $donate_path; ?>" border="0" name="submit" alt="PayPal - Safe and Fast!">
			<img alt="" border="0" src="https://www.paypal.com/it_IT/i/scr/pixel.gif" width="1" height="1">
			</form>
			<br />
			<br />
			<a href="http://www.shambix.com/en/news/wordpress-cheat-sheet-pack/" title="Free Download: Wordpress 3+ CheatSheet made by Shambix" target="_blank"><img src="<?php echo $cheat_path; ?>" alt="Free Download: Wordpress 3+ CheatSheet made by Shambix"></a>
			<br />
			<br />
			<a href="http://www.shambix.com" title="Shambix | Design&amp;Marketing Consulting" target="_blank"><img src="<?php echo $logo_path; ?>" alt="Shambix | Design&amp;Marketing Consulting"></a>
			<br />
			<img src="<?php echo $wpcons_path; ?>" alt="Shambix @ CodePoet - Official Wordpress Consultants" title="Shambix @ CodePoet - Official Wordpress Consultants">
		</div>

	</div>

	<h3>Credits</h3>
	<div class="islidex_options-body credits">
		<p>
		- Apple slider (Credits to <a href="http://tutorialzine.com/2009/11/beautiful-apple-gallery-slideshow/" target="_blank">TutorialZine</a>)
		<br />
		- Nivo slider (Credits to <a href="http://nivo.dev7studios.com/" target="_blank">Dev7Studios</a>)
		<br />
		- Piecemaker slider (Credits to <a href="http://www.modularweb.net/#/en/piecemaker" target="_blank">ModularWeb</a>)
		<br />
		- Captify (Credits to <a href="http://thirdroute.com/projects/captify/" target="_blank">Brian Reavis</a>)
		<br />
		- TimThumb (Credits to <a href="http://code.google.com/p/timthumb/" target="_blank">Tim McDaniels</a>)
		</p>
	</div>

</div>