<?php
/**
 * WooCommerce integration.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if WooCommerce is active.
 *
 * @return bool
 */
function guebel_has_woocommerce() {
	return class_exists( 'WooCommerce' );
}

/**
 * Remove default WooCommerce styles selectively.
 *
 * Keeps the general stylesheet but removes layout and smallscreen styles
 * since the theme handles its own responsive layout.
 *
 * @param array $enqueue_styles WooCommerce stylesheets.
 * @return array Modified stylesheets.
 */
function guebel_wc_dequeue_styles( $enqueue_styles ) {
	unset( $enqueue_styles['woocommerce-layout'] );
	unset( $enqueue_styles['woocommerce-smallscreen'] );
	return $enqueue_styles;
}
add_filter( 'woocommerce_enqueue_styles', 'guebel_wc_dequeue_styles' );

/**
 * Content wrapper start — replace default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * Output theme content wrapper opening tag.
 */
function guebel_wc_wrapper_start() {
	echo '<section class="shop container woocommerce-page-wrap">';
}
add_action( 'woocommerce_before_main_content', 'guebel_wc_wrapper_start', 10 );

/**
 * Output theme content wrapper closing tag.
 */
function guebel_wc_wrapper_end() {
	echo '</section>';
}
add_action( 'woocommerce_after_main_content', 'guebel_wc_wrapper_end', 10 );

/**
 * Number of products per row on the shop page.
 *
 * @return int
 */
function guebel_wc_columns() {
	return 3;
}
add_filter( 'loop_shop_columns', 'guebel_wc_columns' );

/**
 * Number of products per page on the shop.
 *
 * @return int
 */
function guebel_wc_products_per_page() {
	$per_page = get_theme_mod( 'guebel_products_per_page', 12 );
	return absint( $per_page );
}
add_filter( 'loop_shop_per_page', 'guebel_wc_products_per_page' );

/**
 * Related products configuration.
 *
 * @param array $args Related products query args.
 * @return array Modified args.
 */
function guebel_wc_related_products_args( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'guebel_wc_related_products_args' );

/**
 * Cross-sell display configuration.
 *
 * @param int $columns Number of columns.
 * @return int
 */
function guebel_wc_cross_sells_columns( $columns ) {
	return 3;
}
add_filter( 'woocommerce_cross_sells_columns', 'guebel_wc_cross_sells_columns' );

/**
 * Cross-sell total count.
 *
 * @param int $total Total products.
 * @return int
 */
function guebel_wc_cross_sells_total( $total ) {
	return 3;
}
add_filter( 'woocommerce_cross_sells_total', 'guebel_wc_cross_sells_total' );

/**
 * Upsell display configuration.
 *
 * @param array $args Upsell arguments.
 * @return array
 */
function guebel_wc_upsells_args( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}
add_filter( 'woocommerce_upsell_display_args', 'guebel_wc_upsells_args' );

/**
 * Get cart item count.
 *
 * @return int
 */
function guebel_cart_count() {
	if ( ! guebel_has_woocommerce() || is_null( WC()->cart ) ) {
		return 0;
	}
	return (int) WC()->cart->get_cart_contents_count();
}

/**
 * Output cart count badge markup (used as AJAX fragment).
 */
function guebel_cart_count_markup() {
	?>
	<span class="cart-count" data-cart-count><?php echo esc_html( guebel_cart_count() ); ?></span>
	<?php
}

/**
 * Register cart count as AJAX fragment for live updates.
 *
 * @param array $fragments Cart fragments.
 * @return array Modified fragments.
 */
function guebel_cart_fragment( $fragments ) {
	ob_start();
	guebel_cart_count_markup();
	$fragments['span.cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'guebel_cart_fragment' );

/**
 * Get cart URL (falls back to shop anchor when WC is not active).
 *
 * @return string
 */
function guebel_cart_url() {
	return guebel_has_woocommerce() ? wc_get_cart_url() : '#shop';
}

/**
 * Get my-account URL.
 *
 * @return string
 */
function guebel_account_url() {
	return guebel_has_woocommerce() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
}

/**
 * Get shop page URL (falls back to homepage anchor).
 *
 * @return string
 */
function guebel_shop_url() {
	if ( guebel_has_woocommerce() ) {
		$shop = wc_get_page_permalink( 'shop' );
		if ( $shop ) {
			return $shop;
		}
	}
	return home_url( '/#shop' );
}

/**
 * Add WooCommerce body classes.
 *
 * @param array $classes Body classes.
 * @return array Modified classes.
 */
function guebel_wc_body_classes( $classes ) {
	if ( guebel_has_woocommerce() ) {
		$classes[] = 'woocommerce-active';

		if ( is_shop() || is_product_category() || is_product_tag() ) {
			$classes[] = 'guebel-shop-page';
		}

		if ( is_product() ) {
			$classes[] = 'guebel-product-page';
		}

		if ( is_cart() ) {
			$classes[] = 'guebel-cart-page';
		}

		if ( is_checkout() ) {
			$classes[] = 'guebel-checkout-page';
		}

		if ( is_account_page() ) {
			$classes[] = 'guebel-account-page';
		}
	}
	return $classes;
}
add_filter( 'body_class', 'guebel_wc_body_classes' );

/**
 * Customize add to cart button classes on product loops.
 *
 * @param array $args Button arguments.
 * @return array Modified arguments.
 */
function guebel_wc_add_to_cart_args( $args ) {
	if ( isset( $args['class'] ) ) {
		$args['class'] = trim( str_replace( 'button', '', $args['class'] ) . ' rule-btn' );
	}
	return $args;
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'guebel_wc_add_to_cart_args' );

/**
 * Customize WooCommerce breadcrumb defaults.
 *
 * @param array $defaults Breadcrumb defaults.
 * @return array Modified defaults.
 */
function guebel_wc_breadcrumb_defaults( $defaults ) {
	$defaults['delimiter']   = ' <span class="breadcrumb-sep">/</span> ';
	$defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'guebel' ) . '">';
	$defaults['wrap_after']  = '</nav>';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'guebel_wc_breadcrumb_defaults' );

/**
 * Custom product image sizes.
 */
function guebel_wc_image_dimensions() {
	if ( ! guebel_has_woocommerce() ) {
		return;
	}

	// Single product image.
	add_filter( 'woocommerce_get_image_size_single', function () {
		return array(
			'width'  => 800,
			'height' => 800,
			'crop'   => 1,
		);
	} );

	// Product thumbnails.
	add_filter( 'woocommerce_get_image_size_thumbnail', function () {
		return array(
			'width'  => 400,
			'height' => 400,
			'crop'   => 1,
		);
	} );

	// Gallery thumbnails.
	add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function () {
		return array(
			'width'  => 150,
			'height' => 150,
			'crop'   => 1,
		);
	} );
}
add_action( 'after_setup_theme', 'guebel_wc_image_dimensions', 12 );

/**
 * Display product badges (new, sale, featured).
 *
 * @param WC_Product|null $product Product object.
 */
function guebel_product_badges( $product = null ) {
	if ( ! guebel_has_woocommerce() ) {
		return;
	}

	if ( ! $product ) {
		global $product;
	}

	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return;
	}

	$new_badge_text      = get_theme_mod( 'guebel_badge_new_text', __( 'New', 'guebel' ) );
	$sale_badge_text     = get_theme_mod( 'guebel_badge_sale_text', __( 'Sale', 'guebel' ) );
	$featured_badge_text = get_theme_mod( 'guebel_badge_featured_text', __( 'Featured', 'guebel' ) );

	// Check if the product is new (published within last 30 days).
	$post_date = get_the_date( 'U', $product->get_id() );
	$is_new    = ( time() - $post_date ) < ( 30 * DAY_IN_SECONDS );

	if ( $product->is_on_sale() ) {
		echo '<span class="product-tag badge--sale">' . esc_html( $sale_badge_text ) . '</span>';
	} elseif ( $product->is_featured() ) {
		echo '<span class="product-tag badge--featured">' . esc_html( $featured_badge_text ) . '</span>';
	} elseif ( $is_new ) {
		echo '<span class="product-tag badge--new">' . esc_html( $new_badge_text ) . '</span>';
	}
}

/**
 * Remove default WooCommerce sale flash and replace with theme badge.
 */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'guebel_product_badges', 10 );

/**
 * Register product attributes relevant to 3D printed items.
 *
 * Provides helpers for common 3D printing product attributes.
 */
function guebel_get_product_attribute_options() {
	return array(
		'pa_color'    => array(
			'label' => __( 'Color', 'guebel' ),
		),
		'pa_size'     => array(
			'label' => __( 'Size', 'guebel' ),
		),
		'pa_material' => array(
			'label' => __( 'Material', 'guebel' ),
		),
		'pa_finish'   => array(
			'label' => __( 'Finish', 'guebel' ),
		),
	);
}

/**
 * Customize checkout fields.
 *
 * @param array $fields Checkout fields.
 * @return array Modified fields.
 */
function guebel_wc_checkout_fields( $fields ) {
	// Add phone placeholder.
	if ( isset( $fields['billing']['billing_phone'] ) ) {
		$fields['billing']['billing_phone']['placeholder'] = esc_attr__( 'Your phone number', 'guebel' );
	}

	// Add email placeholder.
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['placeholder'] = esc_attr__( 'Your email address', 'guebel' );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'guebel_wc_checkout_fields' );

/**
 * Support for mini cart widget.
 *
 * Ensures cart fragments are updated across the theme.
 */
function guebel_mini_cart_fragment( $fragments ) {
	ob_start();
	?>
	<div class="guebel-mini-cart-contents">
		<?php woocommerce_mini_cart(); ?>
	</div>
	<?php
	$fragments['div.guebel-mini-cart-contents'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'guebel_mini_cart_fragment' );

/**
 * Fallback menu for navigation when no menu is assigned.
 */
function guebel_fallback_menu() {
	?>
	<nav class="site-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'guebel' ); ?>">
		<a href="<?php echo esc_url( guebel_shop_url() ); ?>" class="wide-sm"><?php esc_html_e( 'Shop', 'guebel' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/#about' ) ); ?>" class="wide-sm"><?php esc_html_e( 'About', 'guebel' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/#club' ) ); ?>" class="wide-sm"><?php esc_html_e( 'Club', 'guebel' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>" class="wide-sm"><?php esc_html_e( 'Contact', 'guebel' ); ?></a>
	</nav>
	<?php
}

/**
 * Remove default WooCommerce sidebar.
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Single product gallery support — ensure flexslider/zoom/lightbox.
 */
function guebel_wc_single_product_gallery_support() {
	if ( guebel_has_woocommerce() && is_product() ) {
		wp_enqueue_script( 'flexslider' );
		wp_enqueue_script( 'zoom' );
		wp_enqueue_script( 'photoswipe-ui-default' );
		wp_enqueue_style( 'photoswipe-default-skin' );
	}
}
add_action( 'wp_enqueue_scripts', 'guebel_wc_single_product_gallery_support' );
