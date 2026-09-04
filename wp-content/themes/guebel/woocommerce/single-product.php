<?php
/**
 * The Template for displaying all single products.
 *
 * Override: guebel/woocommerce/single-product.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --- Elementor Pro: defer to its single template if one is set --- */
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	return;
}

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content
 *
 * @hooked woocommerce_output_content_wrapper - 10 (removed by theme)
 * @hooked woocommerce_breadcrumb            - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) :
	the_post();

	wc_get_template_part( 'content', 'single-product' );

endwhile;

/**
 * Hook: woocommerce_after_main_content
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (removed by theme)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
