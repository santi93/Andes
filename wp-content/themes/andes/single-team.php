<?php get_header(); ?>

     
	
<section id="middle">
          <div class="teammates-sidebar">
              <?php $loop = new WP_Query( array( 'post_type' => 'team' ) ); ?>

              <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

              <a href="<?php the_permalink(); ?>" class="entry-content-team">
              <?php if ( has_post_thumbnail() )the_post_thumbnail();
              	the_title( '<h2>', '</h2>');?>
              </a>
              <?php endwhile; ?>
            </div>
      
          <div id="content">
			      <?php if(have_posts()) : while(have_posts()) : the_post(); ?>
				
      			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
									
      				<h2 class="entry-title">	
      					<?php 
      					the_title('<a href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a>');
      					 ?>
      				</h2>
												
      				<div class="entry-content">
      					<?php 							
      					 the_content();
      					 wp_link_pages('before=<p class="pages">' . __('Pages:','example') . '&after=</p>')	
      					 ?>
      				</div>
				
      				<div id="personal-info">
            			<?php if ( ! dynamic_sidebar( 'team-content-widget-area' ) ) : ?>
            			<?php endif; ?>
            	</div>
						
      			</div>
      			<?php endwhile; ?>
							
      			<?php else : ?>
						
      			<p class="no-posts"><?php _e('Sorry, no posts matched your criteria', 'andes'); ?></p>
		
      			<?php endif; ?>
          </div>

</section>

<div id="posts_nav">
	<span class="previous-post-link"><?php previous_post_link(); ?> </span>			
	<span class="next-post-link"><?php next_post_link(); ?></span>	
</div>

<?php get_footer(); ?>