<!-- This is where the loop for the posts is -->

<?php get_header(); ?>



<section id="middle" class="blog">

  <div id="sidebar" class="blog">
    <?php if ( ! dynamic_sidebar( 'blog-sidebar-widget-area' ) ) : ?>
  	<?php endif; ?>
  </div>
  
	<div id="content">
	
	  
  	<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
  	
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		
		  	<h2 class="entry-title">	
					<?php the_title('<a href="' . get_permalink() . '" title="' . the_title_attribute('echo=0') . '" rel="bookmark">', '</a>'); ?>
				</h2>
				
			  <div class="entry-image"> 
  				<?php echo wp_get_attachment_image( 1 ); ?> 
				</div>
				
			  <p class="meta-info">
					<abbr class="published" title="<?php the_time('m d y'); ?>"><?php the_time(__('m d y', 'example')); ?> </abbr>
				</p>
				
			  <div class="entry-content">
					<?php 
					   the_content('read more');
					   wp_link_pages('before=<p class="pages">' . __('Pages:','example') . '&after=</p>')
					 ?>
				</div>
				
				<div class="comments-notice">					
					<?php	comments_popup_link( __( '', '1 Comment', '% Comments' ) );?>	
				</div>	
							
				<?php comments_template( $file ); ?>
				
			</div>
						
		<?php endwhile; ?>
		<?php else : ?>
		<p class="no-posts"><?php _e('Sorry, no posts matched your criteria', 'verone'); ?></p>
		<?php endif; ?>
	</div>
	
	  <div id="posts_nav">
    	<span class="previous-post-link"><?php next_posts_link(' Older Posts'); ?> </span>			
    	<span class="next-post-link"><?php previous_posts_link('More Recent Posts '); ?></span>	
    </div>
    
</section>	
