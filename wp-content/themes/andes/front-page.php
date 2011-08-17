<? get_header(); ?>

<section id="middle">
	
	<div id="content">
		
		<div id="home_columns">
			<?php if ( ! dynamic_sidebar( 'home-content-widget-area' ) ) : ?>
			<?php endif; ?>
		</div>
		
		
		<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
		
			<div id="post_<?php the_ID(); ?>" <?php post_class(); ?>>
			
				<div class="entry-image"> <?php echo wp_get_attachment_image( 1 ); ?> </div>
				
			</div>
		
		<?php endwhile; ?>
		
		<?php else : ?>
		
			<p class="no-posts"><?php _e('Sorry, no posts matched your criteria', 'example'); ?></p>
			
		
		<?php endif; ?>
	
		
	</div>
</section>

<?php get_footer(); ?>
