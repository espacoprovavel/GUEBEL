<?php
/**
 * My Account page
 *
 * Override: guebel/woocommerce/myaccount/my-account.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --- Elementor Pro: defer if it has a my-account template --- */
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	return;
}
?>

<div class="guebel-myaccount" data-animate="fade-up">

	<div class="guebel-myaccount-layout">

		<?php // Navigation ?>
		<nav class="guebel-myaccount-nav" aria-label="<?php esc_attr_e( 'Account navigation', 'guebel' ); ?>">
			<?php
			/**
			 * Hook: woocommerce_account_navigation
			 *
			 * @hooked woocommerce_account_navigation - 10
			 */
			do_action( 'woocommerce_account_navigation' );
			?>
		</nav>

		<?php // Content ?>
		<div class="guebel-myaccount-content woocommerce-MyAccount-content">
			<?php
			/**
			 * Hook: woocommerce_account_content
			 *
			 * @hooked woocommerce_account_content - 10
			 */
			do_action( 'woocommerce_account_content' );
			?>
		</div>

	</div>

</div>
