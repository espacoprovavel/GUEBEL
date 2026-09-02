<?php
/**
 * My Account Dashboard
 *
 * Override: guebel/woocommerce/myaccount/dashboard.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* --- Elementor Pro: defer if it has a template --- */
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	return;
}

$current_user = wp_get_current_user();
?>

<div class="guebel-dashboard" data-animate="fade-up">

	<?php // Welcome message ?>
	<div class="guebel-dashboard-welcome">
		<h2 class="section-title">
			<?php
			printf(
				/* translators: %s: user display name */
				esc_html__( 'Welcome back, %s', 'guebel' ),
				'<strong>' . esc_html( $current_user->display_name ) . '</strong>'
			);
			?>
		</h2>
		<p class="section-text">
			<?php
			printf(
				/* translators: 1: user orders link, 2: user addresses link, 3: user edit account link, 4: logout link */
				wp_kses_post( __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'guebel' ) ),
				esc_url( wc_get_endpoint_url( 'orders' ) ),
				esc_url( wc_get_endpoint_url( 'edit-address' ) ),
				esc_url( wc_get_endpoint_url( 'edit-account' ) )
			);
			?>
		</p>
	</div>

	<?php // Quick links ?>
	<div class="guebel-dashboard-grid">

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="guebel-dashboard-card">
			<div class="guebel-dashboard-card-icon" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
			</div>
			<h3 class="guebel-dashboard-card-title"><?php esc_html_e( 'Orders', 'guebel' ); ?></h3>
			<p class="guebel-dashboard-card-text"><?php esc_html_e( 'View your order history and track deliveries.', 'guebel' ); ?></p>
		</a>

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'downloads' ) ); ?>" class="guebel-dashboard-card">
			<div class="guebel-dashboard-card-icon" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
			</div>
			<h3 class="guebel-dashboard-card-title"><?php esc_html_e( 'Downloads', 'guebel' ); ?></h3>
			<p class="guebel-dashboard-card-text"><?php esc_html_e( 'Access your downloadable products.', 'guebel' ); ?></p>
		</a>

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="guebel-dashboard-card">
			<div class="guebel-dashboard-card-icon" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
			</div>
			<h3 class="guebel-dashboard-card-title"><?php esc_html_e( 'Addresses', 'guebel' ); ?></h3>
			<p class="guebel-dashboard-card-text"><?php esc_html_e( 'Manage your billing and shipping addresses.', 'guebel' ); ?></p>
		</a>

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="guebel-dashboard-card">
			<div class="guebel-dashboard-card-icon" aria-hidden="true">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
			</div>
			<h3 class="guebel-dashboard-card-title"><?php esc_html_e( 'Account Details', 'guebel' ); ?></h3>
			<p class="guebel-dashboard-card-text"><?php esc_html_e( 'Edit your name, email, and password.', 'guebel' ); ?></p>
		</a>

	</div>

	<?php // Recent orders summary ?>
	<?php
	$customer_orders = wc_get_orders(
		array(
			'customer' => $current_user->ID,
			'limit'    => 3,
			'orderby'  => 'date',
			'order'    => 'DESC',
			'status'   => array_keys( wc_get_order_statuses() ),
		)
	);

	if ( ! empty( $customer_orders ) ) :
		?>
		<div class="guebel-dashboard-orders" data-animate="fade-up">
			<h3 class="guebel-dashboard-section-title">
				<?php esc_html_e( 'Recent Orders', 'guebel' ); ?>
			</h3>

			<table class="guebel-dashboard-orders-table shop_table">
				<thead>
					<tr>
						<th scope="col" class="wide-sm"><?php esc_html_e( 'Order', 'guebel' ); ?></th>
						<th scope="col" class="wide-sm"><?php esc_html_e( 'Date', 'guebel' ); ?></th>
						<th scope="col" class="wide-sm"><?php esc_html_e( 'Status', 'guebel' ); ?></th>
						<th scope="col" class="wide-sm"><?php esc_html_e( 'Total', 'guebel' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $customer_orders as $order ) : ?>
						<tr>
							<td>
								<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
									#<?php echo esc_html( $order->get_order_number() ); ?>
								</a>
							</td>
							<td><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></td>
							<td><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></td>
							<td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<p>
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="rule-btn">
					<?php esc_html_e( 'View All Orders', 'guebel' ); ?>
				</a>
			</p>
		</div>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_account_dashboard
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );
	?>

</div>
