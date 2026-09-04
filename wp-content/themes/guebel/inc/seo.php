<?php
/**
 * SEO foundations for the Guebel theme.
 *
 * Provides semantic HTML, schema.org JSON-LD, Open Graph meta, and
 * compatibility hooks for Rank Math / Yoast SEO / SEOPress.
 * When an SEO plugin is active, its meta tags take precedence and
 * the theme output is suppressed to avoid duplication.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if an SEO plugin handles meta output.
 *
 * @return bool
 */
function guebel_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' )                // Yoast SEO
		|| defined( 'RANK_MATH_VERSION' )            // Rank Math
		|| defined( 'SEOPRESS_VERSION' )             // SEOPress
		|| class_exists( 'All_in_One_SEO_Pack' );    // AIOSEO
}

/**
 * Output Open Graph and Twitter Card meta tags.
 * Only runs when no SEO plugin is active.
 */
function guebel_meta_tags() {
	if ( guebel_has_seo_plugin() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = get_bloginfo( 'description', 'display' );
	$url         = is_singular() ? get_permalink() : home_url( '/' );
	$image       = '';
	$type        = 'website';

	if ( is_singular() ) {
		$type = 'article';
		$post = get_post();

		if ( $post && $post->post_excerpt ) {
			$description = wp_strip_all_tags( $post->post_excerpt );
		} elseif ( $post ) {
			$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 25 );
		}

		if ( has_post_thumbnail() ) {
			$image = get_the_post_thumbnail_url( null, 'large' );
		}
	}

	$description = esc_attr( $description );
	?>
	<meta property="og:type" content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo $description; ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:locale" content="<?php echo esc_attr( get_locale() ); ?>">
	<?php if ( $image ) : ?>
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>">
	<?php endif; ?>
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo $description; ?>">
	<?php if ( $image ) : ?>
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'guebel_meta_tags', 5 );

/**
 * Output JSON-LD structured data.
 * Only runs when no SEO plugin is active.
 */
function guebel_schema_output() {
	if ( guebel_has_seo_plugin() ) {
		return;
	}

	$schema = array();

	// Organization schema (always present).
	$org = array(
		'@type' => 'Organization',
		'@id'   => home_url( '/#organization' ),
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
	);

	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $logo_url ) {
			$org['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $logo_url,
			);
		}
	}

	$social_links = guebel_get_social_links();
	if ( ! empty( $social_links ) ) {
		$org['sameAs'] = array_values( $social_links );
	}

	$schema[] = $org;

	// WebSite schema with SearchAction.
	$schema[] = array(
		'@type'           => 'WebSite',
		'@id'             => home_url( '/#website' ),
		'url'             => home_url( '/' ),
		'name'            => get_bloginfo( 'name' ),
		'description'     => get_bloginfo( 'description' ),
		'publisher'       => array( '@id' => home_url( '/#organization' ) ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'        => 'EntryPoint',
				'urlTemplate'  => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	// BreadcrumbList on inner pages.
	if ( ! is_front_page() ) {
		$breadcrumbs = guebel_get_breadcrumb_items();
		if ( ! empty( $breadcrumbs ) ) {
			$items = array();
			foreach ( $breadcrumbs as $i => $crumb ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'name'     => $crumb['name'],
					'item'     => $crumb['url'],
				);
			}
			$schema[] = array(
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $items,
			);
		}
	}

	// Product schema (WooCommerce single product).
	if ( guebel_has_woocommerce() && is_product() ) {
		global $product;
		if ( $product && is_a( $product, 'WC_Product' ) ) {
			$product_schema = array(
				'@type'       => 'Product',
				'name'        => $product->get_name(),
				'description' => wp_strip_all_tags( $product->get_short_description() ),
				'sku'         => $product->get_sku(),
				'offers'      => array(
					'@type'         => 'Offer',
					'price'         => $product->get_price(),
					'priceCurrency' => get_woocommerce_currency(),
					'availability'  => $product->is_in_stock()
						? 'https://schema.org/InStock'
						: 'https://schema.org/OutOfStock',
					'url'           => get_permalink(),
				),
			);

			$image = wp_get_attachment_image_url( $product->get_image_id(), 'large' );
			if ( $image ) {
				$product_schema['image'] = $image;
			}

			if ( $product->get_average_rating() > 0 ) {
				$product_schema['aggregateRating'] = array(
					'@type'       => 'AggregateRating',
					'ratingValue' => $product->get_average_rating(),
					'reviewCount' => $product->get_review_count(),
				);
			}

			$schema[] = $product_schema;
		}
	}

	if ( ! empty( $schema ) ) {
		$graph = array(
			'@context' => 'https://schema.org',
			'@graph'   => $schema,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'guebel_schema_output', 10 );

/**
 * Build breadcrumb items for schema.
 *
 * @return array Array of ['name' => ..., 'url' => ...].
 */
function guebel_get_breadcrumb_items() {
	$items = array();
	$items[] = array(
		'name' => __( 'Home', 'guebel' ),
		'url'  => home_url( '/' ),
	);

	if ( is_category() ) {
		$cat = get_queried_object();
		$items[] = array(
			'name' => $cat->name,
			'url'  => get_category_link( $cat ),
		);
	} elseif ( is_singular( 'post' ) ) {
		$cats = get_the_category();
		if ( ! empty( $cats ) ) {
			$items[] = array(
				'name' => $cats[0]->name,
				'url'  => get_category_link( $cats[0] ),
			);
		}
		$items[] = array(
			'name' => get_the_title(),
			'url'  => get_permalink(),
		);
	} elseif ( is_singular() ) {
		$items[] = array(
			'name' => get_the_title(),
			'url'  => get_permalink(),
		);
	} elseif ( guebel_has_woocommerce() && is_shop() ) {
		$items[] = array(
			'name' => wc_get_page_permalink( 'shop' ) ? __( 'Shop', 'guebel' ) : __( 'Products', 'guebel' ),
			'url'  => guebel_shop_url(),
		);
	} elseif ( guebel_has_woocommerce() && is_product_category() ) {
		$items[] = array(
			'name' => __( 'Shop', 'guebel' ),
			'url'  => guebel_shop_url(),
		);
		$cat = get_queried_object();
		$items[] = array(
			'name' => $cat->name,
			'url'  => get_term_link( $cat ),
		);
	} elseif ( guebel_has_woocommerce() && is_product() ) {
		$items[] = array(
			'name' => __( 'Shop', 'guebel' ),
			'url'  => guebel_shop_url(),
		);
		$items[] = array(
			'name' => get_the_title(),
			'url'  => get_permalink(),
		);
	} elseif ( is_search() ) {
		$items[] = array(
			'name' => __( 'Search Results', 'guebel' ),
			'url'  => get_search_link(),
		);
	}

	return $items;
}

/**
 * Add canonical URL if no SEO plugin handles it.
 */
function guebel_canonical_url() {
	if ( guebel_has_seo_plugin() ) {
		return;
	}

	if ( is_singular() ) {
		echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
	} elseif ( is_front_page() ) {
		echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'guebel_canonical_url', 3 );

/**
 * Add meta description if no SEO plugin handles it.
 */
function guebel_meta_description() {
	if ( guebel_has_seo_plugin() ) {
		return;
	}

	$description = '';

	if ( is_front_page() ) {
		$description = get_bloginfo( 'description', 'display' );
	} elseif ( is_singular() ) {
		$post = get_post();
		if ( $post && $post->post_excerpt ) {
			$description = wp_strip_all_tags( $post->post_excerpt );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$description = term_description();
		$description = wp_strip_all_tags( $description );
	}

	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( wp_trim_words( $description, 25 ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'guebel_meta_description', 4 );
