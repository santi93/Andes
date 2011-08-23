<?php

	function register_andes_menus() {
		register_nav_menus( array(
			'header-menu'  => __( 'Header Menu' ),
			'sidebar-menu' => __( 'Sidebar Menu' ),
			'blog-sidebar' => __( 'Blog Sidebar')
								  )
								);
							  }

	function andes_init() {

	register_andes_menus();

		if (!is_admin()) {
  		wp_deregister_script('jquery');
  		wp_deregister_script('jquery-color');
  		wp_deregister_script('jquery-form');
  		wp_deregister_script('jquery-ui-core');
  		wp_deregister_script('jquery-ui-tabs');
  		wp_deregister_script('jquery-ui-sortable');
  		wp_deregister_script('jquery-ui-draggable');
  		wp_deregister_script('jquery-ui-droppable');
  		wp_deregister_script('jquery-ui-selectable');
  		wp_deregister_script('jquery-ui-resizable');
  		wp_deregister_script('jquery-ui-dialog');

  		wp_register_script('jquery', get_bloginfo('template_directory').'/js/jquery-1.6.2.min.js', false, '1.6.2');
  		wp_register_script('jquery-ui', get_bloginfo('template_directory').'/js/jquery-ui-1.8.16.custom.min.js', 'jquery', '1.8.16');
  		wp_register_script('hashgrid', get_bloginfo('template_directory').'/js/hashgrid.js', 'jquery', '7');
  		wp_register_script('andes', get_bloginfo('template_directory').'/js/andes.js', 'jquery', '1');

  		wp_enqueue_script('jquery');
  		wp_enqueue_script('jquery-ui');
  		wp_enqueue_script('hashgrid');
  		wp_enqueue_script('andes');
  	}
  }

	add_action( 'init', 'andes_init' );


	function andes_nav_unlister( $menu ){
	return preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );
	}
	add_filter( 'wp_nav_menu', 'andes_nav_unlister' );


	function andes_widgets_init() {
	if( function_exists('register_sidebar') ) {
	// Area 0 in the sidebar. Empty by default.
	register_sidebar( array(
	'name' => __( 'Home Content', 'andes' ),
	'id' => 'home-content-widget-area',
	'description' => __( 'This is where the home content goes, Keep it short and simple', 'andes' ),
	'before_widget' => '<div id="%1$s" class="column %2$s">',
	'after_widget' => '</div>',
	'before_title' => '<h2 class="widget-title">',
	'after_title' => '</h2>',
	) );

	// Area 1 in the sidebar. Empty by default.
	register_sidebar( array(
	'name' => __( 'About Content', 'andes' ),
	'id' => 'about-content-widget-area',
	'description' => __( 'This is where the about content goes', 'andes' ),
	'before_widget' => '<div id="%1$s" class="column %2$s">',
	'after_widget' => '</div>',
	'before_title' => '<h2 class="widget-title">',
	'after_title' => '</h2>',
	) );
	
	// Area 2 in the sidebar. Empty by default.
	register_sidebar( array(
	'name' => __( 'Sidebar', 'andes' ),
	'id' => 'sidebar-widget-area',
	'description' => __( 'This is where the sidebar widgets go', 'andes' ),
	'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
	'after_widget' => '</li>',
	'before_title' => '<h3 class="widget-title">',
	'after_title' => '</h3>',
	) );
	
	// Area 3. located below the secondary widget area in the sidebar.
		register_sidebar( array(
			'name' => __( 'Blog Sidebar', 'andes' ),
			'id' => 'blog-sidebar-widget-area',
			'description' => __( 'This is where the sidebar widgets for the blog go', 'verone' ),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget' => '</li>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
	}
}






  function andes_comment($comment, $args, $depth) {
     $GLOBALS['comment'] = $comment;
  	switch ( $comment->comment_type ) :
  		case '' :   
      	?>
  	   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
  	     <div id="comment-<?php comment_ID(); ?>">

  		      <?php if ($comment->comment_approved == '0') : ?>
  	         <em><?php _e('Your comment is awaiting moderation.') ?></em>
  	         <br />
  		      <?php endif; ?>

  		      <div class="comment-meta commentmetadata">
  			      <span class="comment-author vcard">
  		         <?php echo get_avatar($comment,$size='32',$default='<path_to_url>' ); ?>
  		     	   </span>
  		     	   <?php printf(__('<cite class="comment_author"> %s:</cite>'), get_comment_author_link()) ?>
  			      <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
  				      <?php printf(__('%1$s '), get_comment_date('M d y'),  get_comment_time()) ?>
  			      </a> 


  		      </div>

  		      <?php comment_text() ?>

  	     </div>
  	   </li>
  		<?php
  		break;
  	endswitch;
  }


	add_action( 'widgets_init', 'andes_widgets_init' );


  if ( function_exists( 'add_theme_support' ) ) { 
    add_theme_support( 'post-thumbnails' ); 
  }





  add_action( 'init', 'create_post_type' );
  function create_post_type() {
  	register_post_type( 'project',
  		array(
  			'labels' => array(
  				'name'				 => __( 'Projects' ),
  				'singular_name'		 => __( 'Project' ),
  				'add new'			 =>	__('Add New'),
  				'add_new_item'		 => __('Add new Project'),
  				'edit'				 => __('Edit'),
  				'edit_item'			 => __('Edit This Project'),
  				'new_item'			 => __('New Project'),
  				'view'				 => __('View Project'),
  				'view_item'			 => __('View Project'),
  				'search_items'		 => __('Search Projects'),
  				'not_found'			 => __('No Projects Found'),
  				'not_found_in_trash' => __('No Projects found in Trash'),
  			),
  			'public' 		 		=> true,
  			'has_archive'		    => true,
  			'exclude_from_search'	=> false,
  			'hierarchical'			=> true,
  			'query_var' 			=> true, 
  			'supports'				=> array( 'title', 'editor', 'comments', 'trackbacks', 'excerpt', 'thumbnail', 'custom-fields' ),
  			'taxonomies'			=> array( 'post_tag', 'category'),
  			'can_export'			=> true,

  		)
  	);
  	register_post_type( 'team',
  		array(
  			'labels' => array(
  				'name'				 => __( 'Team' ),
  				'add new'			 =>	__('Add New'),
  				'add_new_item'		 => __('Add new Teammate'),
  				'edit'				 => __('Edit'),
  				'edit_item'			 => __('Edit This Teammate'),
  				'new_item'			 => __('New Teammate'),
  				'view'				 => __('View Teammate'),
  				'view_item'			 => __('View Teammate'),
  				'search_items'		 => __('Search Teammates'),
  				'not_found'			 => __('No Teammates Found'),
  				'not_found_in_trash' => __('No Teammates found in Trash'),
  			),
  			'public' 		 		=> true,
  			'has_archive'		    => true,
  			'exclude_from_search'	=> false,
  			'hierarchical'			=> true,
  			'query_var' 			=> true, 
  			'supports'				=> array( 'title', 'editor', 'comments', 'trackbacks', 'excerpt', 'thumbnail' ),
  			'taxonomies'			=> array( 'post_tag', 'category'),
  			'can_export'			=> true,

  		)
  	);
  }

?>