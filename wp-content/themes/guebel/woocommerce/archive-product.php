<?php
/**
 * The Template for displaying product archives, including the main shop page.
 *
 * Override: guebel/woocommerce/archive-product.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --- Elementor Pro: defer to its archive template if one is set --- */
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
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
?>

<header class="guebel-archive-header" data-animate="fade-up">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="guebel-archive-title section-title">
			<?php woocommerce_page_title(); ?>
		</h1>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_archive_description
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description  - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</header>

<?php
if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count       - 20
	 * @hooked woocommerce_catalog_ordering   - 30
	 */
	?>
	<div class="guebel-shop-controls" data-animate="fade-up">
		<?php do_action( 'woocommerce_before_shop_loop' ); ?>
	</div>
	<?php

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );

} else {
	/**
	 * Hook: woocommerce_no_products_found
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

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
