<?php /* Header for Andes */?>
<DOCTYPE html>

<html <?php language_attributes(); ?>>
  <head>
    <meta  charset="<?php bloginfo( 'charset' ); ?>" />

    <title>
      <?php
       
          global $page, $paged;


          wp_title( ':' ,true, 'right' );

          // Add the blog name.
          bloginfo( 'name' );

          // Add the blog description for the home/front page.
          $site_description = get_bloginfo( 'description', 'display' );
          if ( $site_description && ( is_home() || is_front_page() ) )
          echo " : $site_description";
        ?>
    </title>
    <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_directory' ); ?>/hashgrid.css" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />

    <?php wp_head(); ?>
   </head>
 
   <body <?php body_class(); ?>>
   
     <section id="header">
       <?php $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div'; ?>
       <div id="logo">
       		<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"> 
       		  <img src="<?php bloginfo('template_url'); ?>/Andes Interactive Logo.jpg" alt="" " /> 
       		</a>
       </div>        
       </<?php echo $heading_tag; ?>>
     
   
       <menu id="navigation" role="navigation">
         <?php 
           $menu_args = array(             
                     'container' => false,           
                     'theme_location' => 'header-menu'
                     );    
           wp_nav_menu( $menu_args );
         ?>
         <li id="search" class="widget_search">
           <?php get_search_form(); ?>       
         </li>
       </menu>
   
     </section>