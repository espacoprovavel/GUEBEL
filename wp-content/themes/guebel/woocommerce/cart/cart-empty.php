<?php
/**
 * Empty cart page
 *
 * Override: guebel/woocommerce/cart/cart-empty.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --- Elementor Pro: defer if it has a template --- */
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	return;
}

/**
 * Hook: woocommerce_cart_is_empty
 *
 * @hooked wc_empty_cart_message - 10
 */
// We handle the message ourselves below; still fire the hook for plugins.
?>

<div class="guebel-empty-cart" data-animate="fade-up">

	<?php do_action( 'woocommerce_cart_is_empty' ); ?>

	<div class="guebel-empty-cart-inner">

		<div class="guebel-empty-cart-icon" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round">
				<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
				<path d="M3 6h18"/>
				<path d="M16 10a4 4 0 0 1-8 0"/>
			</svg>
		</div>

		<h2 class="guebel-empty-cart-title section-title">
			<?php esc_html_e( 'Your cart is empty', 'guebel' ); ?>
		</h2>

		<p class="guebel-empty-cart-text section-text">
			<?php esc_html_e( 'Looks like you have not added anything to your cart yet. Browse our collection and find something you love.', 'guebel' ); ?>
		</p>

		<p class="guebel-empty-cart-action">
			<a class="rule-btn" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Continue Shopping', 'guebel' ); ?>
			</a>
		</p>
	</div>

	<?php
	// Featured products suggestions.
	$featured = wc_get_products(
		array(
			'limit'    => 4,
			'featured' => true,
			'status'   => 'publish',
			'orderby'  => 'rand',
		)
	);

	if ( ! empty( $featured ) ) :
		?>
		<div class="guebel-empty-cart-suggestions" data-animate="fade-up">
			<h3 class="shop-title"><?php esc_html_e( 'You might like', 'guebel' ); ?></h3>

			<ul class="guebel-product-suggestions product-grid">
				<?php
				foreach ( $featured as $featured_product ) :
					$post_object = get_post( $featured_product->get_id() );
					if ( ! $post_object ) {
						continue;
					}
					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					wc_get_template_part( 'content', 'product' );
				endforeach;
				wp_reset_postdata();
				?>
			</ul>
		</div>
	<?php endif; ?>

</div>
