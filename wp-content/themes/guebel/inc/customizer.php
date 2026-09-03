<?php
/**
 * WordPress Customizer settings for Guebel.
 *
 * Provides fallback settings that work alongside Elementor's Global Settings.
 * Primary customisation should happen through Elementor Site Settings;
 * these Customizer options exist for cases where Elementor Pro is not active.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function guebel_customize_register( $wp_customize ) {

	// --- Panel: Guebel Settings ---
	$wp_customize->add_panel( 'guebel_panel', array(
		'title'    => __( 'Guebel Settings', 'guebel' ),
		'priority' => 30,
	) );

	// --- Section: Brand Identity ---
	$wp_customize->add_section( 'guebel_brand', array(
		'title' => __( 'Brand Identity', 'guebel' ),
		'panel' => 'guebel_panel',
	) );

	$wp_customize->add_setting( 'guebel_logo_mobile', array(
		'default'           => '',
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'guebel_logo_mobile', array(
		'label'     => __( 'Mobile Logo', 'guebel' ),
		'section'   => 'guebel_brand',
		'mime_type' => 'image',
	) ) );

	$wp_customize->add_setting( 'guebel_store_description', array(
		'default'           => __( 'Curated contemporary decoration for refined interiors.', 'guebel' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'guebel_store_description', array(
		'label'   => __( 'Store Short Description', 'guebel' ),
		'section' => 'guebel_brand',
		'type'    => 'textarea',
	) );

	$wp_customize->add_setting( 'guebel_newsletter_text', array(
		'default'           => __( 'Subscreva e receba novidades, lançamentos e ofertas exclusivas.', 'guebel' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'guebel_newsletter_text', array(
		'label'   => __( 'Footer Newsletter Text', 'guebel' ),
		'section' => 'guebel_brand',
		'type'    => 'textarea',
	) );

	// --- Section: Contact Info ---
	$wp_customize->add_section( 'guebel_contact', array(
		'title' => __( 'Contact Information', 'guebel' ),
		'panel' => 'guebel_panel',
	) );

	$contact_fields = array(
		'guebel_email'     => array( 'label' => __( 'Email', 'guebel' ), 'default' => '' ),
		'guebel_phone'     => array( 'label' => __( 'Phone', 'guebel' ), 'default' => '' ),
		'guebel_whatsapp'  => array( 'label' => __( 'WhatsApp', 'guebel' ), 'default' => '' ),
		'guebel_address'   => array( 'label' => __( 'Address', 'guebel' ), 'default' => '' ),
		'guebel_hours'     => array( 'label' => __( 'Business Hours', 'guebel' ), 'default' => '' ),
	);

	foreach ( $contact_fields as $id => $field ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $field['default'],
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( $id, array(
			'label'   => $field['label'],
			'section' => 'guebel_contact',
			'type'    => 'text',
		) );
	}

	// --- Section: Social Media ---
	$wp_customize->add_section( 'guebel_social', array(
		'title' => __( 'Social Media', 'guebel' ),
		'panel' => 'guebel_panel',
	) );

	$social_networks = array(
		'guebel_instagram' => 'Instagram',
		'guebel_pinterest' => 'Pinterest',
		'guebel_facebook'  => 'Facebook',
		'guebel_tiktok'    => 'TikTok',
		'guebel_youtube'   => 'YouTube',
		'guebel_twitter'   => 'X (Twitter)',
	);

	foreach ( $social_networks as $id => $label ) {
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( $id, array(
			'label'   => $label . ' URL',
			'section' => 'guebel_social',
			'type'    => 'url',
		) );
	}

	// --- Section: Shop Settings ---
	$wp_customize->add_section( 'guebel_shop', array(
		'title' => __( 'Shop Settings', 'guebel' ),
		'panel' => 'guebel_panel',
	) );

	$wp_customize->add_setting( 'guebel_products_per_page', array(
		'default'           => 12,
		'sanitize_callback' => 'absint',
	) );

	$wp_customize->add_control( 'guebel_products_per_page', array(
		'label'       => __( 'Products Per Page', 'guebel' ),
		'section'     => 'guebel_shop',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 1, 'max' => 48 ),
	) );

	$wp_customize->add_setting( 'guebel_badge_new_text', array(
		'default'           => __( 'New', 'guebel' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'guebel_badge_new_text', array(
		'label'   => __( 'New Badge Text', 'guebel' ),
		'section' => 'guebel_shop',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'guebel_badge_sale_text', array(
		'default'           => __( 'Sale', 'guebel' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'guebel_badge_sale_text', array(
		'label'   => __( 'Sale Badge Text', 'guebel' ),
		'section' => 'guebel_shop',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'guebel_badge_featured_text', array(
		'default'           => __( 'Featured', 'guebel' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'guebel_badge_featured_text', array(
		'label'   => __( 'Featured Badge Text', 'guebel' ),
		'section' => 'guebel_shop',
		'type'    => 'text',
	) );

	// --- Section: Header Settings ---
	$wp_customize->add_section( 'guebel_header', array(
		'title' => __( 'Header Settings', 'guebel' ),
		'panel' => 'guebel_panel',
	) );

	$wp_customize->add_setting( 'guebel_header_transparent', array(
		'default'           => false,
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'guebel_header_transparent', array(
		'label'   => __( 'Transparent header on homepage hero', 'guebel' ),
		'section' => 'guebel_header',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'guebel_header_sticky', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );

	$wp_customize->add_control( 'guebel_header_sticky', array(
		'label'   => __( 'Sticky header on scroll', 'guebel' ),
		'section' => 'guebel_header',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'guebel_topbar_text', array(
		'default'           => '',
		'sanitize_callback' => 'wp_kses_post',
	) );

	$wp_customize->add_control( 'guebel_topbar_text', array(
		'label'   => __( 'Announcement Bar Text', 'guebel' ),
		'section' => 'guebel_header',
		'type'    => 'text',
	) );

	// --- Section: Footer Settings ---
	$wp_customize->add_section( 'guebel_footer', array(
		'title' => __( 'Footer Settings', 'guebel' ),
		'panel' => 'guebel_panel',
	) );

	$wp_customize->add_setting( 'guebel_footer_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'guebel_footer_text', array(
		'label'   => __( 'Footer Copyright Text', 'guebel' ),
		'section' => 'guebel_footer',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'guebel_payment_methods', array(
		'default'           => 'Visa · Mastercard · PayPal · MB Way',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'guebel_payment_methods', array(
		'label'   => __( 'Payment Methods Text', 'guebel' ),
		'section' => 'guebel_footer',
		'type'    => 'text',
	) );
}
add_action( 'customize_register', 'guebel_customize_register' );

/**
 * Output CSS variables from Customizer settings to complement Elementor globals.
 */
function guebel_customizer_css() {
	// Intentionally minimal — primary design tokens live in style.css
	// and are overridden by Elementor Global Colors/Fonts when active.
}
add_action( 'wp_head', 'guebel_customizer_css', 99 );

/**
 * Get social links array.
 *
 * @return array Associative array of network => url.
 */
function guebel_get_social_links() {
	$networks = array(
		'instagram' => get_theme_mod( 'guebel_instagram', '' ),
		'pinterest' => get_theme_mod( 'guebel_pinterest', '' ),
		'facebook'  => get_theme_mod( 'guebel_facebook', '' ),
		'tiktok'    => get_theme_mod( 'guebel_tiktok', '' ),
		'youtube'   => get_theme_mod( 'guebel_youtube', '' ),
		'twitter'   => get_theme_mod( 'guebel_twitter', '' ),
	);

	return array_filter( $networks );
}
