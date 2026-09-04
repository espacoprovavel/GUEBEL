<?php
/**
 * Shortcodes.
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders plugin shortcodes.
 */
class Guebel_Shortcodes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_shortcodes' ) );
	}

	/**
	 * Register all shortcodes.
	 */
	public function register_shortcodes() {
		add_shortcode( 'guebel_year', array( $this, 'shortcode_year' ) );
		add_shortcode( 'guebel_store_name', array( $this, 'shortcode_store_name' ) );
		add_shortcode( 'guebel_phone', array( $this, 'shortcode_phone' ) );
		add_shortcode( 'guebel_whatsapp', array( $this, 'shortcode_whatsapp' ) );
		add_shortcode( 'guebel_email', array( $this, 'shortcode_email' ) );
		add_shortcode( 'guebel_address', array( $this, 'shortcode_address' ) );
		add_shortcode( 'guebel_social_links', array( $this, 'shortcode_social_links' ) );
		add_shortcode( 'guebel_featured_products', array( $this, 'shortcode_featured_products' ) );
		add_shortcode( 'guebel_new_products', array( $this, 'shortcode_new_products' ) );
		add_shortcode( 'guebel_sale_products', array( $this, 'shortcode_sale_products' ) );
		add_shortcode( 'guebel_collections', array( $this, 'shortcode_collections' ) );
	}

	/**
	 * Current year shortcode.
	 *
	 * @return string
	 */
	public function shortcode_year() {
		return esc_html( gmdate( 'Y' ) );
	}

	/**
	 * Store name shortcode.
	 *
	 * @return string
	 */
	public function shortcode_store_name() {
		$name = Guebel_Core::get_option( 'store_name', get_bloginfo( 'name' ) );
		return esc_html( $name );
	}

	/**
	 * Phone shortcode.
	 *
	 * @return string
	 */
	public function shortcode_phone() {
		$phone = Guebel_Core::get_option( 'phone' );
		if ( empty( $phone ) ) {
			return '';
		}

		$phone_clean = preg_replace( '/[^0-9+]/', '', $phone );

		return sprintf(
			'<a href="tel:%s" class="guebel-phone-link">%s</a>',
			esc_attr( $phone_clean ),
			esc_html( $phone )
		);
	}

	/**
	 * WhatsApp shortcode.
	 *
	 * @return string
	 */
	public function shortcode_whatsapp() {
		$whatsapp = Guebel_Core::get_option( 'whatsapp' );
		if ( empty( $whatsapp ) ) {
			return '';
		}

		$number_clean = preg_replace( '/[^0-9]/', '', $whatsapp );

		return sprintf(
			'<a href="https://wa.me/%s" target="_blank" rel="noopener noreferrer" class="guebel-whatsapp-link">%s</a>',
			esc_attr( $number_clean ),
			esc_html( $whatsapp )
		);
	}

	/**
	 * Email shortcode.
	 *
	 * @return string
	 */
	public function shortcode_email() {
		$email = Guebel_Core::get_option( 'email' );
		if ( empty( $email ) ) {
			return '';
		}

		return sprintf(
			'<a href="mailto:%s" class="guebel-email-link">%s</a>',
			esc_attr( antispambot( $email ) ),
			esc_html( antispambot( $email ) )
		);
	}

	/**
	 * Address shortcode.
	 *
	 * @return string
	 */
	public function shortcode_address() {
		$parts = array();

		$address = Guebel_Core::get_option( 'address' );
		if ( ! empty( $address ) ) {
			$parts[] = $address;
		}

		$city = Guebel_Core::get_option( 'city' );
		if ( ! empty( $city ) ) {
			$postal_code = Guebel_Core::get_option( 'postal_code' );
			if ( ! empty( $postal_code ) ) {
				$parts[] = $postal_code . ' ' . $city;
			} else {
				$parts[] = $city;
			}
		}

		$country = Guebel_Core::get_option( 'country' );
		if ( ! empty( $country ) ) {
			$parts[] = $country;
		}

		if ( empty( $parts ) ) {
			return '';
		}

		return '<span class="guebel-address">' . esc_html( implode( ', ', $parts ) ) . '</span>';
	}

	/**
	 * Social links shortcode.
	 *
	 * @return string
	 */
	public function shortcode_social_links() {
		$networks = array(
			'instagram_url' => array(
				'label' => __( 'Instagram', 'guebel-core' ),
				'icon'  => 'dashicons-instagram',
			),
			'pinterest_url' => array(
				'label' => __( 'Pinterest', 'guebel-core' ),
				'icon'  => 'dashicons-pinterest',
			),
			'facebook_url'  => array(
				'label' => __( 'Facebook', 'guebel-core' ),
				'icon'  => 'dashicons-facebook-alt',
			),
			'tiktok_url'    => array(
				'label' => __( 'TikTok', 'guebel-core' ),
				'icon'  => 'dashicons-video-alt3',
			),
			'youtube_url'   => array(
				'label' => __( 'YouTube', 'guebel-core' ),
				'icon'  => 'dashicons-youtube',
			),
		);

		$output = '<ul class="guebel-social-links" style="list-style: none; padding: 0; margin: 0; display: flex; gap: 10px;">';
		$has_links = false;

		foreach ( $networks as $key => $network ) {
			$url = Guebel_Core::get_option( $key );
			if ( ! empty( $url ) ) {
				$has_links = true;
				$output   .= sprintf(
					'<li><a href="%s" target="_blank" rel="noopener noreferrer" title="%s" class="guebel-social-link guebel-social--%s"><span class="dashicons %s"></span><span class="screen-reader-text">%s</span></a></li>',
					esc_url( $url ),
					esc_attr( $network['label'] ),
					esc_attr( strtolower( $network['label'] ) ),
					esc_attr( $network['icon'] ),
					esc_html( $network['label'] )
				);
			}
		}

		$output .= '</ul>';

		return $has_links ? $output : '';
	}

	/**
	 * Featured products shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function shortcode_featured_products( $atts ) {
		if ( ! Guebel_Core::is_woocommerce_active() ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'count'   => 4,
				'columns' => 4,
			),
			$atts,
			'guebel_featured_products'
		);

		$count   = absint( $atts['count'] );
		$columns = absint( $atts['columns'] );

		if ( $count < 1 ) {
			$count = 4;
		}
		if ( $columns < 1 ) {
			$columns = 4;
		}

		return do_shortcode( sprintf(
			'[products limit="%d" columns="%d" visibility="featured"]',
			$count,
			$columns
		) );
	}

	/**
	 * New products shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function shortcode_new_products( $atts ) {
		if ( ! Guebel_Core::is_woocommerce_active() ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'count'   => 4,
				'columns' => 4,
			),
			$atts,
			'guebel_new_products'
		);

		$count   = absint( $atts['count'] );
		$columns = absint( $atts['columns'] );

		if ( $count < 1 ) {
			$count = 4;
		}
		if ( $columns < 1 ) {
			$columns = 4;
		}

		return do_shortcode( sprintf(
			'[products limit="%d" columns="%d" orderby="date" order="DESC"]',
			$count,
			$columns
		) );
	}

	/**
	 * Sale products shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function shortcode_sale_products( $atts ) {
		if ( ! Guebel_Core::is_woocommerce_active() ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'count'   => 4,
				'columns' => 4,
			),
			$atts,
			'guebel_sale_products'
		);

		$count   = absint( $atts['count'] );
		$columns = absint( $atts['columns'] );

		if ( $count < 1 ) {
			$count = 4;
		}
		if ( $columns < 1 ) {
			$columns = 4;
		}

		return do_shortcode( sprintf(
			'[products limit="%d" columns="%d" on_sale="true"]',
			$count,
			$columns
		) );
	}

	/**
	 * Collections showcase shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function shortcode_collections( $atts ) {
		$atts = shortcode_atts(
			array(
				'count' => 3,
			),
			$atts,
			'guebel_collections'
		);

		$count = absint( $atts['count'] );
		if ( $count < 1 ) {
			$count = 3;
		}

		$collections = get_posts(
			array(
				'post_type'      => 'guebel_collection',
				'posts_per_page' => $count,
				'post_status'    => 'publish',
				'orderby'        => 'menu_order date',
				'order'          => 'ASC',
			)
		);

		if ( empty( $collections ) ) {
			return '';
		}

		$output = '<div class="guebel-collections" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">';

		foreach ( $collections as $collection ) {
			$thumbnail = '';
			if ( has_post_thumbnail( $collection->ID ) ) {
				$thumbnail = get_the_post_thumbnail(
					$collection->ID,
					'medium_large',
					array( 'style' => 'width: 100%; height: 200px; object-fit: cover;' )
				);
			}

			$output .= '<div class="guebel-collection-card" style="border: 1px solid #e0e0e0; border-radius: 4px; overflow: hidden;">';

			if ( ! empty( $thumbnail ) ) {
				$output .= '<div class="guebel-collection-image">' . $thumbnail . '</div>';
			}

			$output .= '<div class="guebel-collection-content" style="padding: 15px;">';
			$output .= '<h3 style="margin: 0 0 10px;">' . esc_html( get_the_title( $collection->ID ) ) . '</h3>';

			if ( ! empty( $collection->post_excerpt ) ) {
				$output .= '<p style="color: #666; margin: 0 0 10px;">' . esc_html( $collection->post_excerpt ) . '</p>';
			}

			$output .= sprintf(
				'<a href="%s" class="guebel-collection-link" style="color: #2c3e50; text-decoration: none; font-weight: bold;">%s &rarr;</a>',
				esc_url( get_permalink( $collection->ID ) ),
				esc_html__( 'Ver Coleção', 'guebel-core' )
			);

			$output .= '</div>';
			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}
}
