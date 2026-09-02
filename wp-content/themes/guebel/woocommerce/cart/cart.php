<?php
/**
 * Cart Page
 *
 * Override: guebel/woocommerce/cart/cart.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 7.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --- Elementor Pro: defer if it has a cart template --- */
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	return;
}

do_action( 'woocommerce_before_cart' );
?>

<div class="guebel-cart-page" data-animate="fade-up">

	<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

		<?php do_action( 'woocommerce_before_cart_table' ); ?>

		<div class="guebel-cart-layout">

			<div class="guebel-cart-items">

				<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
					<thead>
						<tr>
							<th class="product-thumbnail" scope="col">
								<span class="screen-reader-text"><?php esc_html_e( 'Product image', 'guebel' ); ?></span>
							</th>
							<th class="product-name" scope="col"><?php esc_html_e( 'Product', 'guebel' ); ?></th>
							<th class="product-price" scope="col"><?php esc_html_e( 'Price', 'guebel' ); ?></th>
							<th class="product-quantity" scope="col"><?php esc_html_e( 'Quantity', 'guebel' ); ?></th>
							<th class="product-subtotal" scope="col"><?php esc_html_e( 'Subtotal', 'guebel' ); ?></th>
							<th class="product-remove" scope="col">
								<span class="screen-reader-text"><?php esc_html_e( 'Remove', 'guebel' ); ?></span>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php do_action( 'woocommerce_before_cart_contents' ); ?>

						<?php
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
							/**
							 * Filter: woocommerce_cart_item_visible
							 */
							if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
								$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
								?>
								<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

									<td class="product-thumbnail" data-title="<?php esc_attr_e( 'Image', 'guebel' ); ?>">
										<?php
										$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
										if ( ! $product_permalink ) {
											echo wp_kses_post( $thumbnail );
										} else {
											printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
										}
										?>
									</td>

									<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'guebel' ); ?>">
										<?php
										if ( ! $product_permalink ) {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
										} else {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
										}

										do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

										// Meta data.
										echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

										// Backorder notification.
										if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'guebel' ) . '</p>', $product_id ) );
										}
										?>
									</td>

									<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'guebel' ); ?>">
										<?php
											echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
									</td>

									<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'guebel' ); ?>">
										<?php
										if ( $_product->is_sold_individually() ) {
											$min_quantity = 1;
											$max_quantity = 1;
										} else {
											$min_quantity = 0;
											$max_quantity = $_product->get_max_purchase_quantity();
										}

										$product_quantity = woocommerce_quantity_input(
											array(
												'input_name'   => "cart[{$cart_item_key}][qty]",
												'input_value'  => $cart_item['quantity'],
												'max_value'    => $max_quantity,
												'min_value'    => $min_quantity,
												'product_name' => $_product->get_name(),
											),
											$_product,
											false
										);

										echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
									</td>

									<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'guebel' ); ?>">
										<?php
											echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
									</td>

									<td class="product-remove">
										<?php
											echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												'woocommerce_cart_item_remove_link',
												sprintf(
													'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
													esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
													/* translators: %s: product name */
													esc_attr( sprintf( __( 'Remove %s from cart', 'guebel' ), wp_strip_all_tags( $_product->get_name() ) ) ),
													esc_attr( $product_id ),
													esc_attr( $_product->get_sku() )
												),
												$cart_item_key
											);
										?>
									</td>
								</tr>
								<?php
							}
						}
						?>

						<?php do_action( 'woocommerce_cart_contents' ); ?>

						<tr>
							<td colspan="6" class="actions">

								<?php if ( wc_coupons_enabled() ) { ?>
									<div class="coupon">
										<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'guebel' ); ?></label>
										<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'guebel' ); ?>" />
										<button type="submit" class="rule-btn" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'guebel' ); ?>">
											<?php esc_html_e( 'Apply coupon', 'guebel' ); ?>
										</button>
										<?php do_action( 'woocommerce_cart_coupon' ); ?>
									</div>
								<?php } ?>

								<button type="submit" class="rule-btn" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'guebel' ); ?>">
									<?php esc_html_e( 'Update cart', 'guebel' ); ?>
								</button>

								<?php do_action( 'woocommerce_cart_actions' ); ?>

								<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
							</td>
						</tr>

						<?php do_action( 'woocommerce_after_cart_contents' ); ?>
					</tbody>
				</table>
			</div>

			<?php // Cart Totals ?>
			<div class="guebel-cart-totals-wrap">
				<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

				<div class="cart-collaterals">
					<?php
					/**
					 * Hook: woocommerce_cart_collaterals
					 *
					 * @hooked woocommerce_cross_sell_display
					 * @hooked woocommerce_cart_totals - 10
					 */
					do_action( 'woocommerce_cart_collaterals' );
					?>
				</div>

				<div class="guebel-cart-continue">
					<a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="rule-btn">
						<?php esc_html_e( 'Continue Shopping', 'guebel' ); ?>
					</a>
				</div>
			</div>

		</div>

		<?php do_action( 'woocommerce_after_cart_table' ); ?>

	</form>

	<?php
	/**
	 * Hook: woocommerce_after_cart
	 */
	do_action( 'woocommerce_after_cart' );
	?>

</div>
