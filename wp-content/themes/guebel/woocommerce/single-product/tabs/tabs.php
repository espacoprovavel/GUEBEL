<?php
/**
 * Single Product tabs
 *
 * Override: guebel/woocommerce/single-product/tabs/tabs.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter: woocommerce_product_tabs
 */
$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( empty( $product_tabs ) ) {
	return;
}

// Add custom tabs.
global $product;

// Care Instructions tab.
$care_instructions = $product ? get_post_meta( $product->get_id(), '_guebel_care_instructions', true ) : '';
if ( $care_instructions ) {
	$product_tabs['care_instructions'] = array(
		'title'    => __( 'Care Instructions', 'guebel' ),
		'priority' => 25,
		'callback' => 'guebel_care_instructions_tab',
	);
}

// Sustainability tab.
$sustainability = $product ? get_post_meta( $product->get_id(), '_guebel_sustainability', true ) : '';
if ( $sustainability ) {
	$product_tabs['sustainability'] = array(
		'title'    => __( 'Sustainability', 'guebel' ),
		'priority' => 26,
		'callback' => 'guebel_sustainability_tab',
	);
}

// Shipping & Returns tab (always present).
$product_tabs['shipping_returns'] = array(
	'title'    => __( 'Shipping & Returns', 'guebel' ),
	'priority' => 30,
	'callback' => 'guebel_shipping_returns_tab',
);

// Sort by priority.
uasort( $product_tabs, function( $a, $b ) {
	$a_priority = isset( $a['priority'] ) ? $a['priority'] : 10;
	$b_priority = isset( $b['priority'] ) ? $b['priority'] : 10;
	return $a_priority - $b_priority;
} );

$tab_keys = array_keys( $product_tabs );
?>

<div class="woocommerce-tabs wc-tabs-wrapper guebel-product-tabs" data-animate="fade-up">

	<?php // Desktop: Tab navigation ?>
	<ul class="tabs wc-tabs guebel-tabs-nav" role="tablist">
		<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
			<li class="<?php echo esc_attr( $key ); ?>_tab <?php echo ( $key === $tab_keys[0] ) ? 'active' : ''; ?>" id="tab-title-<?php echo esc_attr( $key ); ?>" role="presentation">
				<a href="#tab-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>" aria-selected="<?php echo ( $key === $tab_keys[0] ) ? 'true' : 'false'; ?>">
					<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php // Tab panels ?>
	<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
		<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab guebel-tab-panel" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>" <?php echo ( $key !== $tab_keys[0] ) ? 'hidden' : ''; ?>>

			<?php // Mobile: Accordion heading ?>
			<h3 class="guebel-accordion-heading" role="button" tabindex="0" aria-expanded="<?php echo ( $key === $tab_keys[0] ) ? 'true' : 'false'; ?>" aria-controls="tab-<?php echo esc_attr( $key ); ?>-content" data-accordion-toggle="<?php echo esc_attr( $key ); ?>">
				<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
				<svg class="guebel-accordion-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
			</h3>

			<div class="guebel-tab-content" id="tab-<?php echo esc_attr( $key ); ?>-content">
				<?php
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<?php
/**
 * Custom tab callbacks.
 * Defined inline to keep them bundled with the template.
 */
if ( ! function_exists( 'guebel_care_instructions_tab' ) ) {
	/**
	 * Care Instructions tab content.
	 *
	 * @param string $key Tab key.
	 * @param array  $tab Tab data.
	 */
	function guebel_care_instructions_tab( $key, $tab ) {
		global $product;
		$content = get_post_meta( $product->get_id(), '_guebel_care_instructions', true );
		if ( $content ) {
			echo '<h2>' . esc_html__( 'Care Instructions', 'guebel' ) . '</h2>';
			echo wp_kses_post( wpautop( $content ) );
		}
	}
}

if ( ! function_exists( 'guebel_sustainability_tab' ) ) {
	/**
	 * Sustainability tab content.
	 *
	 * @param string $key Tab key.
	 * @param array  $tab Tab data.
	 */
	function guebel_sustainability_tab( $key, $tab ) {
		global $product;
		$content = get_post_meta( $product->get_id(), '_guebel_sustainability', true );
		if ( $content ) {
			echo '<h2>' . esc_html__( 'Sustainability', 'guebel' ) . '</h2>';
			echo wp_kses_post( wpautop( $content ) );
		}
	}
}

if ( ! function_exists( 'guebel_shipping_returns_tab' ) ) {
	/**
	 * Shipping & Returns tab content.
	 *
	 * @param string $key Tab key.
	 * @param array  $tab Tab data.
	 */
	function guebel_shipping_returns_tab( $key, $tab ) {
		?>
		<h2><?php esc_html_e( 'Shipping & Returns', 'guebel' ); ?></h2>

		<div class="guebel-shipping-info">
			<h3><?php esc_html_e( 'Shipping', 'guebel' ); ?></h3>
			<p>
				<?php esc_html_e( 'We ship to most countries worldwide. Delivery times vary by location. Orders are typically processed within 1-3 business days.', 'guebel' ); ?>
			</p>
			<?php
			/**
			 * Hook: guebel_shipping_tab_content
			 *
			 * Allows plugins or custom code to add shipping info.
			 */
			do_action( 'guebel_shipping_tab_content' );
			?>
		</div>

		<div class="guebel-returns-info">
			<h3><?php esc_html_e( 'Returns', 'guebel' ); ?></h3>
			<p>
				<?php esc_html_e( 'We accept returns within 30 days of delivery. Items must be unused and in their original packaging. Please contact our support team to initiate a return.', 'guebel' ); ?>
			</p>
			<?php
			/**
			 * Hook: guebel_returns_tab_content
			 *
			 * Allows plugins or custom code to add returns info.
			 */
			do_action( 'guebel_returns_tab_content' );
			?>
		</div>
		<?php
	}
}
