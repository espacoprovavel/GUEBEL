<?php
/**
 * Template Name: Canvas (Blank)
 * Template Post Type: page
 *
 * Full-width blank canvas for Elementor page building.
 * No header, no footer — only Elementor content.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
