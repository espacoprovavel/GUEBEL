<?php
/**
 * Front page template.
 *
 * Renders the homepage. When built with Elementor, outputs the Elementor
 * content directly. Otherwise, loads the default theme section parts.
 *
 * @package Guebel
 * @since   1.0.0
 */

get_header();

if ( guebel_is_elementor_content() ) :
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
else :
	get_template_part( 'template-parts/hero', 'section' );
	get_template_part( 'template-parts/section', 'categories' );
	get_template_part( 'template-parts/section', 'featured-products' );
	get_template_part( 'template-parts/section', 'editorial' );
	get_template_part( 'template-parts/section', 'benefits' );
	get_template_part( 'template-parts/section', 'newsletter' );
endif;

get_footer();
