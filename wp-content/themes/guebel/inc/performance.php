<?php
/**
 * Performance optimisations for the Guebel theme.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remove unnecessary items from wp_head.
 */
function guebel_cleanup_head() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'guebel_cleanup_head' );

/**
 * Disable emoji DNS prefetch.
 *
 * @param array  $urls          Prefetch URLs.
 * @param string $relation_type Relation type.
 * @return array Filtered URLs.
 */
function guebel_disable_emoji_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		$urls = array_filter( $urls, function( $url ) {
			return ! str_contains( $url, 'wp.org/images/core/emoji' )
				&& ! str_contains( $url, 's.w.org/images/core/emoji' );
		} );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'guebel_disable_emoji_dns_prefetch', 10, 2 );

/**
 * Remove jQuery Migrate on the front end for WordPress 6.0+.
 *
 * @param WP_Scripts $scripts Script registry.
 */
function guebel_remove_jquery_migrate( $scripts ) {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
		}
	}
}
add_action( 'wp_default_scripts', 'guebel_remove_jquery_migrate' );

/**
 * Add loading="lazy" and decoding="async" to content images
 * for browsers that support it.
 *
 * @param string $content Post content.
 * @return string Modified content.
 */
function guebel_lazy_content_images( $content ) {
	if ( is_admin() || is_feed() || wp_doing_ajax() ) {
		return $content;
	}

	return $content;
}
add_filter( 'the_content', 'guebel_lazy_content_images', 99 );

/**
 * Add resource hints for fonts and critical resources.
 *
 * @param array  $urls          Resource URLs.
 * @param string $relation_type Relation type.
 * @return array Modified URLs.
 */
function guebel_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href'        => 'https://fonts.googleapis.com',
			'crossorigin' => 'anonymous',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'guebel_resource_hints', 10, 2 );

/**
 * Disable self-pingbacks.
 *
 * @param array $links Pingback links.
 */
function guebel_disable_self_pingbacks( &$links ) {
	$home = home_url();
	foreach ( $links as $l => $link ) {
		if ( str_starts_with( $link, $home ) ) {
			unset( $links[ $l ] );
		}
	}
}
add_action( 'pre_ping', 'guebel_disable_self_pingbacks' );

/**
 * Limit post revisions to keep the database clean.
 *
 * @param int $num    Number of revisions.
 * @param WP_Post $post Post object.
 * @return int
 */
function guebel_limit_revisions( $num, $post ) {
	return 5;
}
add_filter( 'wp_revisions_to_keep', 'guebel_limit_revisions', 10, 2 );

/**
 * Add fetchpriority="high" to the hero/LCP image.
 *
 * WordPress 6.3+ handles this via wp_get_loading_optimization_attributes,
 * but this provides a fallback for earlier versions.
 *
 * @param array $attr Image attributes.
 * @param WP_Post $attachment Attachment post.
 * @param string|array $size Image size.
 * @return array
 */
function guebel_hero_fetch_priority( $attr, $attachment, $size ) {
	if ( is_front_page() && in_the_loop() && 0 === (int) did_action( 'guebel_hero_image_rendered' ) ) {
		$attr['fetchpriority'] = 'high';
		$attr['loading']       = 'eager';
		do_action( 'guebel_hero_image_rendered' );
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'guebel_hero_fetch_priority', 10, 3 );
