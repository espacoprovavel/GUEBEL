<?php
/**
 * Guebel theme functions and definitions.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme version constant.
 */
define( 'GUEBEL_VERSION', '1.0.0' );

/**
 * Theme setup.
 *
 * Sets up theme defaults and registers support for various WordPress features.
 */
function guebel_setup() {
	// Make the theme available for translation.
	load_theme_textdomain( 'guebel', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Add custom image sizes.
	add_image_size( 'guebel-hero', 1920, 1080, true );
	add_image_size( 'guebel-card', 600, 600, true );
	add_image_size( 'guebel-blog', 800, 500, true );

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary'    => esc_html__( 'Primary Menu', 'guebel' ),
			'footer'     => esc_html__( 'Footer Menu', 'guebel' ),
			'mobile'     => esc_html__( 'Mobile Menu', 'guebel' ),
			'categories' => esc_html__( 'Shop Categories Menu', 'guebel' ),
			'footer-shop' => esc_html__( 'Footer Shop Links', 'guebel' ),
			'footer-info' => esc_html__( 'Footer Info Links', 'guebel' ),
			'footer-legal' => esc_html__( 'Footer Legal Links', 'guebel' ),
		)
	);

	// Switch default core markup to output valid HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	// Add support for custom logo.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 250,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Add support for custom header.
	add_theme_support(
		'custom-header',
		array(
			'default-image' => '',
			'width'         => 1920,
			'height'        => 1080,
			'flex-height'   => true,
			'flex-width'    => true,
			'header-text'   => false,
		)
	);

	// Add support for custom background.
	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'f5f0eb',
		)
	);

	// Add support for selective refresh in the Customizer.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for wide and full-width blocks.
	add_theme_support( 'align-wide' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Add support for WooCommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Set content width.
	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 1200;
	}
}
add_action( 'after_setup_theme', 'guebel_setup' );

/**
 * Register widget areas.
 */
function guebel_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Blog Sidebar', 'guebel' ),
			'id'            => 'sidebar-blog',
			'description'   => esc_html__( 'Widgets displayed on blog pages.', 'guebel' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Shop Sidebar', 'guebel' ),
			'id'            => 'sidebar-shop',
			'description'   => esc_html__( 'Widgets displayed on shop pages.', 'guebel' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Widgets', 'guebel' ),
			'id'            => 'footer-widgets',
			'description'   => esc_html__( 'Widgets displayed in the footer area.', 'guebel' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'guebel_widgets_init' );

/**
 * Check if WooCommerce is active.
 */
function guebel_has_woocommerce() {
	return class_exists( 'WooCommerce' );
}

/**
 * Get the shop page URL.
 */
function guebel_shop_url() {
	if ( guebel_has_woocommerce() && function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( 'shop' );
	}
	return home_url( '/shop/' );
}

/**
 * Estimate reading time for the current post.
 */
function guebel_reading_time() {
	$content    = get_post_field( 'post_content', get_the_ID() );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	$minutes    = max( 1, (int) ceil( $word_count / 200 ) );

	return sprintf(
		/* translators: %d: number of minutes */
		_n( '%d min read', '%d min read', $minutes, 'guebel' ),
		$minutes
	);
}

/**
 * Load theme modules.
 */
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/elementor.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/seo.php';
require get_template_directory() . '/inc/performance.php';
require get_template_directory() . '/inc/accessibility.php';

if ( guebel_has_woocommerce() ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Fallback functions when WooCommerce is not active.
 */
if ( ! function_exists( 'guebel_cart_url' ) ) {
	function guebel_cart_url() {
		return home_url( '/cart/' );
	}
}

if ( ! function_exists( 'guebel_account_url' ) ) {
	function guebel_account_url() {
		return wp_login_url();
	}
}

if ( ! function_exists( 'guebel_cart_count_markup' ) ) {
	function guebel_cart_count_markup() {
		echo '<span class="cart-count" data-cart-count>0</span>';
	}
}

if ( ! function_exists( 'guebel_fallback_menu' ) ) {
	function guebel_fallback_menu() {
		echo '<ul>';
		echo '<li><a href="' . esc_url( guebel_shop_url() ) . '">' . esc_html__( 'Loja', 'guebel' ) . '</a></li>';
		echo '<li><a href="' . esc_url( home_url( '/sobre/' ) ) . '">' . esc_html__( 'Sobre', 'guebel' ) . '</a></li>';
		echo '<li><a href="' . esc_url( home_url( '/clube/' ) ) . '">' . esc_html__( 'Clube', 'guebel' ) . '</a></li>';
		echo '<li><a href="' . esc_url( home_url( '/contacto/' ) ) . '">' . esc_html__( 'Contacto', 'guebel' ) . '</a></li>';
		echo '</ul>';
	}
}

if ( ! function_exists( 'guebel_product_badges' ) ) {
	function guebel_product_badges( $product = null ) {
		return;
	}
}
