<?php
/**
 * Elementor Pro integration.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor theme support setup.
 */
function guebel_elementor_setup() {
	add_theme_support( 'elementor' );
	add_theme_support( 'elementor-pro' );
}
add_action( 'after_setup_theme', 'guebel_elementor_setup', 11 );

/**
 * Register all core Elementor Pro theme locations.
 *
 * This allows Elementor Pro Theme Builder templates to override
 * the theme's default header, footer, single, archive, etc.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 */
function guebel_elementor_locations( $manager ) {
	$manager->register_all_core_location();

	// Register additional custom locations.
	$manager->register_location(
		'product',
		array(
			'label'           => esc_html__( 'Single Product', 'guebel' ),
			'multiple'        => false,
			'edit_in_content' => true,
		)
	);

	$manager->register_location(
		'product-archive',
		array(
			'label'           => esc_html__( 'Product Archive', 'guebel' ),
			'multiple'        => false,
			'edit_in_content' => true,
		)
	);
}
add_action( 'elementor/theme/register_locations', 'guebel_elementor_locations' );

/**
 * Check if Elementor plugin is active.
 *
 * @return bool
 */
function guebel_has_elementor() {
	return did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
}

/**
 * Check if Elementor Pro is active.
 *
 * @return bool
 */
function guebel_has_elementor_pro() {
	return class_exists( '\ElementorPro\Plugin' );
}

/**
 * Check if the current post/page content was built with Elementor.
 *
 * @param int|null $post_id Post ID. Defaults to current post.
 * @return bool
 */
function guebel_is_elementor_content( $post_id = null ) {
	if ( ! guebel_has_elementor() ) {
		return false;
	}

	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id ) {
		return false;
	}

	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * Render the theme header, deferring to Elementor Pro Theme Builder if available.
 *
 * If Elementor Pro has a header template assigned, it will render that.
 * Otherwise, the default template-parts/header-default.php is loaded.
 */
function guebel_do_header() {
	if ( guebel_has_elementor_pro() && class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
		$locations_manager = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager();
		if ( $locations_manager->do_location( 'header' ) ) {
			return;
		}
	}
	get_template_part( 'template-parts/header', 'default' );
}

/**
 * Render the theme footer, deferring to Elementor Pro Theme Builder if available.
 *
 * If Elementor Pro has a footer template assigned, it will render that.
 * Otherwise, the default template-parts/footer-default.php is loaded.
 */
function guebel_do_footer() {
	if ( guebel_has_elementor_pro() && class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
		$locations_manager = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager();
		if ( $locations_manager->do_location( 'footer' ) ) {
			return;
		}
	}
	get_template_part( 'template-parts/footer', 'default' );
}

/**
 * Set Elementor's default content width to match the theme.
 *
 * @param int $content_width Content width.
 * @return int
 */
function guebel_elementor_content_width( $content_width ) {
	return 1200;
}
add_filter( 'elementor/editor/content_width', 'guebel_elementor_content_width' );

/**
 * Add custom breakpoints support for Elementor.
 *
 * @param array $config Elementor kit config.
 * @return array Modified config.
 */
function guebel_elementor_breakpoints( $config ) {
	return $config;
}
add_filter( 'elementor/kit/config', 'guebel_elementor_breakpoints' );

/**
 * Add body class when Elementor is editing.
 *
 * @param array $classes Body classes.
 * @return array Modified classes.
 */
function guebel_elementor_body_class( $classes ) {
	if ( guebel_has_elementor() ) {
		$classes[] = 'guebel-elementor';

		if ( guebel_is_elementor_content() ) {
			$classes[] = 'guebel-elementor-content';
		}
	}

	if ( guebel_has_elementor_pro() ) {
		$classes[] = 'guebel-elementor-pro';
	}

	return $classes;
}
add_filter( 'body_class', 'guebel_elementor_body_class' );

/**
 * Set Elementor default colors to match the theme palette.
 *
 * @param array $config Editor configuration.
 * @return array Modified configuration.
 */
function guebel_elementor_editor_colors( $config ) {
	$config['default_scheme_color'] = array(
		'1' => '#3d6b50', // Primary.
		'2' => '#c27c5e', // Secondary.
		'3' => '#b89b5e', // Accent.
		'4' => '#2d3b32', // Text.
	);
	return $config;
}
add_filter( 'elementor/editor/localize_settings', 'guebel_elementor_editor_colors' );
