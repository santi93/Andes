<?php get_header(); ?>

<section id="middle">			
  <?php get_sidebar(); ?>
  
  <div id="content">
    <?php if ( have_posts() ) : ?>
  
    <h2 class="page-title">
      <?php printf( __( 'Search Results for: <br /> <span class="search-title">%s</span>' ), '<span>' . get_search_query() . '</span>' ); ?>
    </h2>
  
    <?php get_template_part( 'index', 'search' ); ?>
    <?php else : ?>
  
    <div id="post-0" class="post no-results not-found">
  
      <h2 class="page-title">
        <?php printf( __( 'Search Results for: <br ?> <span class="search-title">%s</span>' ), '<span>' . get_search_query() . '</span>' ); ?>
      </h2>
      
      <div class="entry-content">
        <p><?php _e( "The content your searching for couldn't be found or isn't visible to the public. <br /> <span class='actions'> Please do one of the following actions:</span> " ); ?></p>
        
        <ol class="options">
          <li> Try again with different keywords <span><?php get_template_part( 'searchform' , 'single'); ?></span>  </li>
          <li> Look through the different categories on the left </li>
          <li> Search in the archives, by dates </li
      </div>
      
    </div>
    <?php endif; ?>
  </div>
</section>			


<?php get_footer(); ?>