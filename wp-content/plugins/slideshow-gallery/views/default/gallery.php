<?php if (!empty($slides)) : ?>
	<ul id="slideshow" style="display:none;">
		<?php if ($frompost) : ?>
			<?php foreach ($slides as $slide) : ?>
				<li>
					<h3><?php echo $slide -> post_title; ?></h3>
					<?php $full_image_href = wp_get_attachment_image_src($slide -> ID, 'full', false); ?>
					<span><?php echo $full_image_href[0]; ?></span>
					<p><?php echo $slide -> post_content; ?></p>
					<?php $thumbnail_link = wp_get_attachment_image_src($slide -> ID, 'thumbnail', false); ?>
					<?php if ($this -> get_option('thumbnails') == "Y") : ?>
						<?php if (!empty($slide -> guid)) : ?>
							<a href="<?php echo $slide -> guid; ?>" title="<?php echo $slide -> post_title; ?>"><img style="height:75px;" src="<?php echo $thumbnail_link[0]; ?>" alt="<?php echo $this -> Html -> sanitize($slide -> post_title); ?>" /></a>
						<?php else : ?>
							<img style="height:75px;" src="<?php echo $thumbnail_link[0]; ?>" alt="<?php echo $this -> Html -> sanitize($slide -> post_title); ?>" />
						<?php endif; ?>
					<?php else : ?>
						<a href="<?php echo $slide -> guid; ?>" title="<?php echo $slide -> post_title; ?>"></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		<?php else : ?>
			<?php foreach ($slides as $slide) : ?>		
				<li>
					<h3><?php echo $slide -> title; ?></h3>
					<span><?php echo rtrim(get_bloginfo('wpurl'), '/'); ?>/wp-content/uploads/<?php echo $this -> plugin_name; ?>/<?php echo $slide -> image; ?></span>
					<p><?php echo $slide -> description; ?></p>
					<?php if ($this -> get_option('thumbnails') == "Y") : ?>
						<?php if ($slide -> uselink == "Y" && !empty($slide -> link)) : ?>
							<a href="<?php echo $slide -> link; ?>" title="<?php echo $slide -> title; ?>"><img style="height:75px;" src="<?php echo $this -> Html -> image_url($this -> Html -> thumbname($slide -> image)); ?>" alt="<?php echo $this -> Html -> sanitize($slide -> title); ?>" /></a>
						<?php else : ?>
							<img style="height:75px;" src="<?php echo $this -> Html -> image_url($this -> Html -> thumbname($slide -> image)); ?>" alt="<?php echo $this -> Html -> sanitize($slide -> title); ?>" />
						<?php endif; ?>
					<?php else : ?>
						<a href="<?php echo $slide -> link; ?>" title="<?php echo $slide -> title; ?>"></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	
	<div id="slideshow-wrapper">
		<?php if ($this -> get_option('thumbnails') == "Y" && $this -> get_option('thumbposition') == "top") : ?>
			<div id="thumbnails" class="thumbstop">
				<div id="slideleft" title="<?php _e('Slide Left', $this -> plugin_name); ?>"></div>
				<div id="slidearea">
					<div id="slider"></div>
				</div>
				<div id="slideright" title="<?php _e('Slide Right', $this -> plugin_name); ?>"></div>
				<br style="clear:both; visibility:hidden; height:1px;" />
			</div>
		<?php endif; ?>
	
		<div id="fullsize">
			<div id="imgprev" class="imgnav" title="Previous Image"></div>
			<div id="imglink"><!-- link --></div>
			<div id="imgnext" class="imgnav" title="Next Image"></div>
			<div id="image"></div>
			<?php if ($this -> get_option('information') == "Y") : ?>
				<div id="information">
					<h3></h3>
					<p></p>
				</div>
			<?php endif; ?>
		</div>
		
		<?php if ($this -> get_option('thumbnails') == "Y" && $this -> get_option('thumbposition') == "bottom") : ?>
			<div id="thumbnails" class="thumbsbot">
				<div id="slideleft" title="<?php _e('Slide Left', $this -> plugin_name); ?>"></div>
				<div id="slidearea">
					<div id="slider"></div>
				</div>
				<div id="slideright" title="<?php _e('Slide Right', $this -> plugin_name); ?>"></div>
				<br style="clear:both; visibility:hidden; height:1px;" />
			</div>
		<?php endif; ?>
	</div>
	
	<script type="text/javascript">
	jQuery.noConflict();
	tid('slideshow').style.display = "none";
	tid('slideshow-wrapper').style.display = 'block';
	tid('slideshow-wrapper').style.visibility = 'hidden';	


	/**
	 * issue #2: Bugfix for WebKit. Safari and similar browsers aren't capable to handle jQuery.ready() right. The problem
	 * here was, that sometimes the event was fired (if js is not available in browsers cache) too early, so that not all
	 * pictures were displayed in the thumbnail bar. I added a timeout to give the browser time to load the pictures.
	 * During that time I found it nice to display a spinner icon to give the visitor a hint that "somethings going on there".
	 * For this to display correctly I've added some lines to the css file too.
	 */

	// append the spinner
	jQuery("#fullsize").append('<div id="spinner"><img src="<?php echo $this -> url(); ?>/images/spinner.gif"></div>');
	tid('spinner').style.visibility = 'visible';

	var slideshow = new TINY.slideshow("slideshow");
	jQuery(document).ready(function() {
		// set a timeout before launching the slideshow
		window.setTimeout(function() {
			<?php if ($this -> get_option('autoslide')) : ?>slideshow.auto = true;<?php else : ?>slideshow.auto = false;<?php endif; ?>
			slideshow.speed = <?php echo $this -> get_option('autospeed'); ?>;
			slideshow.imgSpeed = <?php echo $this -> get_option('fadespeed'); ?>;
			slideshow.navOpacity = <?php echo $this -> get_option('navopacity'); ?>;
			slideshow.navHover = <?php echo $this -> get_option('navhover'); ?>;
			slideshow.letterbox = "#000000";
			slideshow.linkclass = "linkhover";
			slideshow.info = "<?php echo ($this -> get_option('information') == "Y") ? 'information' : ''; ?>";
			slideshow.infoSpeed = <?php echo $this -> get_option('infospeed'); ?>;
			slideshow.thumbs = "<?php echo ($this -> get_option('thumbnails') == "Y") ? 'slider' : ''; ?>";
			slideshow.thumbOpacity = <?php echo $this -> get_option('thumbopacity'); ?>;
			slideshow.left = "slideleft";
			slideshow.right = "slideright";
			slideshow.scrollSpeed = <?php echo $this -> get_option('thumbscrollspeed'); ?>;
			slideshow.spacing = <?php echo $this -> get_option('thumbspacing'); ?>;
			slideshow.active = "<?php echo $this -> get_option('thumbactive'); ?>";
			slideshow.imagesthickbox = "<?php echo ($this -> get_option('imagesthickbox') == "Y") ? 'true' : 'false'; ?>";
			jQuery("#spinner").remove();
			slideshow.init("slideshow","image","imgprev","imgnext","imglink");
			tid('slideshow-wrapper').style.visibility = 'visible';
		}, 1000);
	});
	</script>
<?php endif; ?>