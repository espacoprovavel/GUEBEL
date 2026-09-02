<?php
/**
 * Checkout Form
 *
 * Override: guebel/woocommerce/checkout/form-checkout.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --- Elementor Pro: defer if it has a checkout template --- */
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	return;
}

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'guebel' ) ) );
	return;
}
?>

<div class="guebel-checkout-page" data-animate="fade-up">

	<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>

	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

		<div class="guebel-checkout-layout">

			<?php // --- Billing & Shipping --- ?>
			<div class="guebel-checkout-fields">

				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div id="customer_details" class="guebel-customer-details">
						<div class="guebel-checkout-section">
							<h3 class="guebel-checkout-heading section-title"><?php esc_html_e( 'Billing Details', 'guebel' ); ?></h3>
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</div>

						<div class="guebel-checkout-section">
							<h3 class="guebel-checkout-heading section-title"><?php esc_html_e( 'Shipping Details', 'guebel' ); ?></h3>
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						</div>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>

				<?php // Order notes. ?>
				<div class="guebel-checkout-section guebel-checkout-notes">
					<h3 class="guebel-checkout-heading section-title"><?php esc_html_e( 'Additional Information', 'guebel' ); ?></h3>
					<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

					<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>
						<div class="woocommerce-additional-fields__field-wrapper">
							<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
								<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
				</div>

			</div>

			<?php // --- Order Review & Payment --- ?>
			<div class="guebel-checkout-sidebar">

				<div class="guebel-order-review-wrap">
					<h3 class="guebel-checkout-heading section-title" id="order_review_heading"><?php esc_html_e( 'Your Order', 'guebel' ); ?></h3>

					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>

					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
				</div>

				<?php // Trust signals. ?>
				<div class="guebel-checkout-trust" data-animate="fade-up">
					<div class="guebel-trust-badge">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
						<span><?php esc_html_e( 'Secure Payment', 'guebel' ); ?></span>
					</div>
					<div class="guebel-trust-badge">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
						<span><?php esc_html_e( 'SSL Encrypted', 'guebel' ); ?></span>
					</div>
					<div class="guebel-trust-badge">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
						<span><?php esc_html_e( 'Easy Returns', 'guebel' ); ?></span>
					</div>
				</div>

			</div>

		</div>

	</form>

	<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</div>
