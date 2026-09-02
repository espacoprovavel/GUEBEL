<?php
/**
 * Accessibility enhancements for the Guebel theme.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add aria-current to current menu items.
 *
 * @param array    $atts   Menu item link attributes.
 * @param WP_Post  $item   Menu item data.
 * @param stdClass $args   Menu arguments.
 * @param int      $depth  Depth.
 * @return array
 */
function guebel_nav_aria_current( $atts, $item, $args, $depth ) {
	if ( in_array( 'current-menu-item', (array) $item->classes, true ) ) {
		$atts['aria-current'] = 'page';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'guebel_nav_aria_current', 10, 4 );

/**
 * Add role="navigation" and aria-label to nav menus.
 *
 * @param string   $nav_menu HTML.
 * @param stdClass $args     Menu arguments.
 * @return string
 */
function guebel_nav_aria_label( $nav_menu, $args ) {
	if ( ! empty( $args->theme_location ) ) {
		$labels = array(
			'primary'     => __( 'Primary Navigation', 'guebel' ),
			'footer'      => __( 'Footer Navigation', 'guebel' ),
			'mobile'      => __( 'Mobile Navigation', 'guebel' ),
			'categories'  => __( 'Shop Categories', 'guebel' ),
			'footer-shop' => __( 'Footer Shop Links', 'guebel' ),
			'footer-info' => __( 'Footer Information', 'guebel' ),
			'footer-legal' => __( 'Legal Links', 'guebel' ),
		);

		if ( isset( $labels[ $args->theme_location ] ) ) {
			$label    = esc_attr( $labels[ $args->theme_location ] );
			$nav_menu = str_replace(
				'<ul',
				'<ul role="menubar" aria-label="' . $label . '"',
				$nav_menu
			);
		}
	}
	return $nav_menu;
}
add_filter( 'wp_nav_menu', 'guebel_nav_aria_label', 10, 2 );

/**
 * Add read more link with screen reader text for accessibility.
 *
 * @param string $more_link Read more link HTML.
 * @return string
 */
function guebel_read_more_link( $more_link ) {
	return str_replace(
		'(more&hellip;)',
		sprintf(
			'<span class="screen-reader-text">%s </span>%s',
			get_the_title(),
			__( 'Read more', 'guebel' )
		),
		$more_link
	);
}
add_filter( 'the_content_more_link', 'guebel_read_more_link' );

/**
 * Add descriptive title attributes to paginated links.
 *
 * @param string $link Pagination link HTML.
 * @return string
 */
function guebel_paginate_links_attr( $link ) {
	return $link;
}
add_filter( 'paginate_links_output', 'guebel_paginate_links_attr' );

/**
 * Ensure all images in content have alt attributes.
 *
 * @param array $attr        Image attributes.
 * @param WP_Post $attachment Attachment post.
 * @return array
 */
function guebel_ensure_image_alt( $attr, $attachment ) {
	if ( empty( $attr['alt'] ) ) {
		$attr['alt'] = get_the_title( $attachment->ID );
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'guebel_ensure_image_alt', 10, 2 );
