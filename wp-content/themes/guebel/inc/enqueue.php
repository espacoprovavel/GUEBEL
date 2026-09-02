<?php
/**
 * Enqueue scripts and styles.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preload critical fonts.
 */
function guebel_preload_fonts() {
	?>
	<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php
}
add_action( 'wp_head', 'guebel_preload_fonts', 1 );

/**
 * Enqueue front-end styles.
 */
function guebel_enqueue_styles() {
	// Google Fonts: Cormorant Garamond (display) + Inter (body).
	wp_enqueue_style(
		'guebel-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500&family=Inter:wght@300;400;500;600;700&display=swap',
		array(),
		null
	);

	// Main theme stylesheet.
	wp_enqueue_style(
		'guebel-style',
		get_stylesheet_uri(),
		array( 'guebel-fonts' ),
		GUEBEL_VERSION
	);

	// WooCommerce specific styles (only on WC pages).
	if ( guebel_has_woocommerce() ) {
		wp_enqueue_style(
			'guebel-woocommerce',
			get_template_directory_uri() . '/assets/css/woocommerce.css',
			array( 'guebel-style' ),
			GUEBEL_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'guebel_enqueue_styles' );

/**
 * Enqueue front-end scripts.
 */
function guebel_enqueue_scripts() {
	// Main theme script.
	wp_enqueue_script(
		'guebel-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		GUEBEL_VERSION,
		true
	);

	// Pass data to main script.
	wp_localize_script(
		'guebel-main',
		'guebelData',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'homeUrl'  => home_url( '/' ),
			'themeUrl' => get_template_directory_uri(),
			'i18n'     => array(
				'menuOpen'  => esc_html__( 'Open menu', 'guebel' ),
				'menuClose' => esc_html__( 'Close menu', 'guebel' ),
				'loading'   => esc_html__( 'Loading...', 'guebel' ),
				'error'     => esc_html__( 'An error occurred. Please try again.', 'guebel' ),
				'emailRequired' => esc_html__( 'Please enter a valid email address.', 'guebel' ),
				'subscribed'    => esc_html__( 'Thank you for subscribing!', 'guebel' ),
			),
		)
	);

	// Comment reply script on single posts.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'guebel_enqueue_scripts' );

/**
 * Add defer attribute to non-critical scripts.
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string Modified script tag.
 */
function guebel_defer_scripts( $tag, $handle ) {
	$defer_handles = array( 'guebel-main' );

	if ( in_array( $handle, $defer_handles, true ) ) {
		return str_replace( ' src', ' defer src', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'guebel_defer_scripts', 10, 2 );

/**
 * Add preload for Google Fonts stylesheet.
 *
 * @param string $tag    The link tag.
 * @param string $handle The stylesheet handle.
 * @return string Modified link tag.
 */
function guebel_preload_font_stylesheet( $tag, $handle ) {
	if ( 'guebel-fonts' === $handle ) {
		$tag = str_replace(
			"rel='stylesheet'",
			"rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"",
			$tag
		);
		$tag .= '<noscript>' . str_replace( "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", "rel='stylesheet'", $tag ) . '</noscript>';
	}

	return $tag;
}
add_filter( 'style_loader_tag', 'guebel_preload_font_stylesheet', 10, 2 );
