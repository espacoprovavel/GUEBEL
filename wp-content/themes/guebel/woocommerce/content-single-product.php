<?php
/**
 * The template for displaying product content in the single-product.php template.
 *
 * Override: guebel/woocommerce/content-single-product.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

/**
 * Hook: woocommerce_before_single_product
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

// Calculate sale percentage.
$sale_percentage = '';
if ( $product->is_on_sale() && $product->get_regular_price() && $product->get_sale_price() ) {
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	if ( $regular > 0 ) {
		$sale_percentage = round( ( ( $regular - $sale ) / $regular ) * 100 );
	}
}

// Care instructions from meta.
$care_instructions = get_post_meta( $product->get_id(), '_guebel_care_instructions', true );

// Sustainability info from meta.
$sustainability = get_post_meta( $product->get_id(), '_guebel_sustainability', true );
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'guebel-single-product', $product ); ?>>

	<div class="guebel-product-layout">

		<?php // --- Product Gallery --- ?>
		<div class="guebel-product-gallery" data-animate="fade-up">
			<?php
			/**
			 * Hook: woocommerce_before_single_product_summary
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images     - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div>

		<?php // --- Product Summary --- ?>
		<div class="guebel-product-summary" data-animate="fade-up">

			<?php
			// Category breadcrumb.
			$categories = wc_get_product_category_list( $product->get_id(), ' / ' );
			if ( $categories ) :
				?>
				<div class="guebel-single-category wide-sm">
					<?php echo wp_kses_post( $categories ); ?>
				</div>
			<?php endif; ?>

			<div class="summary entry-summary">
				<?php
				/**
				 * Hook: woocommerce_single_product_summary
				 *
				 * @hooked woocommerce_template_single_title       - 5
				 * @hooked woocommerce_template_single_rating      - 10
				 * @hooked woocommerce_template_single_price       - 10
				 * @hooked woocommerce_template_single_excerpt     - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta        - 40
				 * @hooked woocommerce_template_single_sharing     - 50
				 */
				do_action( 'woocommerce_single_product_summary' );
				?>

				<?php // Sale percentage badge. ?>
				<?php if ( $sale_percentage ) : ?>
					<div class="guebel-sale-badge">
						<?php
						/* translators: %s: sale percentage */
						printf( esc_html__( '%s%% off', 'guebel' ), esc_html( $sale_percentage ) );
						?>
					</div>
				<?php endif; ?>

				<?php // Stock status. ?>
				<div class="guebel-stock-status">
					<?php
					$availability = $product->get_availability();
					if ( ! empty( $availability['availability'] ) ) {
						$stock_class = $product->is_in_stock() ? 'guebel-in-stock' : 'guebel-out-of-stock';
						printf(
							'<span class="%s">%s</span>',
							esc_attr( $stock_class ),
							esc_html( $availability['availability'] )
						);
					}
					?>
				</div>

				<?php // Wishlist button. ?>
				<div class="guebel-single-wishlist">
					<?php
					if ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
						echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
					}
					?>
				</div>

				<?php // Share buttons placeholder. ?>
				<div class="guebel-share-buttons">
					<?php
					/**
					 * Hook: guebel_product_share
					 *
					 * Plugins can hook here to add social sharing buttons.
					 */
					do_action( 'guebel_product_share' );
					?>
				</div>

				<?php // Trust badges. ?>
				<div class="guebel-trust-badges" data-animate="fade-up">
					<div class="guebel-trust-badge">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
						<span><?php esc_html_e( 'Secure Payment', 'guebel' ); ?></span>
					</div>
					<div class="guebel-trust-badge">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
						<span><?php esc_html_e( 'Fast Shipping', 'guebel' ); ?></span>
					</div>
					<div class="guebel-trust-badge">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
						<span><?php esc_html_e( 'Easy Returns', 'guebel' ); ?></span>
					</div>
				</div>

			</div>
		</div>

	</div>

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display           - 15
	 * @hooked woocommerce_output_related_products  - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>

	<?php // Recently viewed products placeholder. ?>
	<div class="guebel-recently-viewed">
		<?php
		/**
		 * Hook: guebel_recently_viewed_products
		 *
		 * Plugins or custom code can hook here to display recently viewed products.
		 */
		do_action( 'guebel_recently_viewed_products' );
		?>
	</div>

</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
