<?php
/*
Template Name: Portfolio
*/
?>
<?php get_header(); ?>


<section id="middle" class="portfolio">
	
	<div id="sidebar" class="portfolio">
    <?php if ( ! dynamic_sidebar( 'blog-sidebar-widget-area' ) ) : ?>
  	<?php endif; ?>
  </div>

	<?php $loop = new WP_Query( array( 'post_type' => 'project' ) ); ?>
	
	<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
	
	
		<a href="<?php the_permalink(); ?>" class="entry-content-portfolio">
			<?php
				if ( has_post_thumbnail() )
					the_post_thumbnail();
				the_title( '<h2>', '</h2>');
			?>
		</a>
	<?php endwhile; ?>

</section>


<?php get_footer(); ?>