<?php
/**
 * The template for displaying product content within loops.
 *
 * Override: guebel/woocommerce/content-product.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

// Bail early if the product post is not valid.
if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
}

// Determine badges.
$is_on_sale  = $product->is_on_sale();
$is_featured = $product->is_featured();
$is_new      = ( ( time() - get_post_time( 'U', true ) ) < ( 30 * DAY_IN_SECONDS ) );

// Second image for hover effect.
$gallery_ids  = $product->get_gallery_image_ids();
$second_image = ! empty( $gallery_ids ) ? wp_get_attachment_image( $gallery_ids[0], 'woocommerce_thumbnail', false, array( 'class' => 'guebel-product-img-hover', 'aria-hidden' => 'true', 'loading' => 'lazy' ) ) : '';

// Product categories.
$categories = wc_get_product_category_list( $product->get_id(), ', ' );
?>

<li <?php wc_product_class( 'guebel-product-card', $product ); ?> data-animate="fade-up">

	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );
	?>

	<div class="guebel-product-media product-media">
		<a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'guebel' ), get_the_title() ) ); ?>">
			<?php
			/**
			 * Hook: woocommerce_before_shop_loop_item_title
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
			?>

			<?php if ( $second_image ) : ?>
				<?php echo wp_kses_post( $second_image ); ?>
			<?php endif; ?>
		</a>

		<?php // Badges ?>
		<div class="guebel-product-badges">
			<?php if ( $is_on_sale ) : ?>
				<span class="guebel-badge guebel-badge--sale product-tag">
					<?php esc_html_e( 'Sale', 'guebel' ); ?>
				</span>
			<?php endif; ?>
			<?php if ( $is_new && ! $is_on_sale ) : ?>
				<span class="guebel-badge guebel-badge--new product-tag">
					<?php esc_html_e( 'New', 'guebel' ); ?>
				</span>
			<?php endif; ?>
			<?php if ( $is_featured ) : ?>
				<span class="guebel-badge guebel-badge--featured product-tag">
					<?php esc_html_e( 'Featured', 'guebel' ); ?>
				</span>
			<?php endif; ?>
		</div>

		<?php // Quick view trigger ?>
		<button class="guebel-quick-view" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" data-quick-view aria-label="<?php echo esc_attr( sprintf( __( 'Quick view %s', 'guebel' ), get_the_title() ) ); ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
		</button>

		<?php // Wishlist placeholder (compatible with YITH) ?>
		<div class="guebel-wishlist-btn">
			<?php
			if ( shortcode_exists( 'yith_wcwl_add_to_wishlist' ) ) {
				echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
			}
			?>
		</div>
	</div>

	<div class="guebel-product-info">
		<?php if ( $categories ) : ?>
			<div class="guebel-product-category wide-sm">
				<?php echo wp_kses_post( $categories ); ?>
			</div>
		<?php endif; ?>

		<?php
		/**
		 * Hook: woocommerce_shop_loop_item_title
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		?>
		<h2 class="guebel-product-title product-name <?php echo esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ); ?>">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<?php
		/**
		 * Hook: woocommerce_after_shop_loop_item_title
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price  - 10
		 */
		?>

		<?php if ( $rating_html = wc_get_rating_html( $product->get_average_rating(), $product->get_review_count() ) ) : ?>
			<div class="guebel-product-rating">
				<?php echo wp_kses_post( $rating_html ); ?>
			</div>
		<?php endif; ?>

		<div class="guebel-product-price product-price">
			<?php echo wp_kses_post( $product->get_price_html() ); ?>
		</div>

		<?php
		/**
		 * Hook: woocommerce_after_shop_loop_item
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5
		 * @hooked woocommerce_template_loop_add_to_cart        - 10
		 */
		?>
		<div class="guebel-product-actions">
			<?php
			woocommerce_template_loop_add_to_cart( array(
				'class' => implode(
					' ',
					array_filter(
						array(
							'rule-btn',
							'guebel-add-to-cart',
							'product_type_' . $product->get_type(),
							$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
							$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
						)
					)
				),
			) );
			?>
		</div>
	</div>

	<?php
	/**
	 * Hook: woocommerce_after_shop_loop_item
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>

</li>
