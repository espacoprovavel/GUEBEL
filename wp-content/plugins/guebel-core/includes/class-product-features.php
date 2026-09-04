<?php
/**
 * WooCommerce Product Features.
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds custom meta fields and product enhancements for WooCommerce.
 */
class Guebel_Product_Features {

	/**
	 * Meta fields configuration.
	 *
	 * @var array
	 */
	private $meta_fields = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->meta_fields = array(
			'_guebel_is_3d_printed'      => array(
				'label' => __( 'Produto impresso em 3D', 'guebel-core' ),
				'type'  => 'checkbox',
			),
			'_guebel_production_time'    => array(
				'label'       => __( 'Tempo de produção', 'guebel-core' ),
				'type'        => 'text',
				'placeholder' => __( 'Ex: 3-5 dias úteis', 'guebel-core' ),
			),
			'_guebel_sustainability_info' => array(
				'label' => __( 'Informação de sustentabilidade', 'guebel-core' ),
				'type'  => 'textarea',
			),
			'_guebel_customizable'       => array(
				'label' => __( 'Produto personalizável', 'guebel-core' ),
				'type'  => 'checkbox',
			),
			'_guebel_dimensions_detail'  => array(
				'label' => __( 'Dimensões detalhadas', 'guebel-core' ),
				'type'  => 'textarea',
			),
			'_guebel_care_instructions'  => array(
				'label' => __( 'Instruções de cuidado', 'guebel-core' ),
				'type'  => 'textarea',
			),
		);

		if ( ! Guebel_Core::is_woocommerce_active() ) {
			return;
		}

		// Admin metabox.
		add_action( 'add_meta_boxes', array( $this, 'add_product_metabox' ) );
		add_action( 'save_post_product', array( $this, 'save_product_meta' ), 10, 2 );

		// Front-end display.
		add_action( 'woocommerce_product_meta_end', array( $this, 'display_product_badges' ) );
		add_action( 'woocommerce_single_product_summary', array( $this, 'display_production_time' ), 25 );
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_tabs' ) );
	}

	/**
	 * Add product metabox.
	 */
	public function add_product_metabox() {
		add_meta_box(
			'guebel_product_details',
			__( 'Guebel - Detalhes do Produto', 'guebel-core' ),
			array( $this, 'render_metabox' ),
			'product',
			'normal',
			'high'
		);
	}

	/**
	 * Render the product metabox.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_metabox( $post ) {
		wp_nonce_field( 'guebel_save_product_meta', 'guebel_product_meta_nonce' );

		echo '<div class="guebel-metabox-wrap" style="padding: 10px 0;">';

		foreach ( $this->meta_fields as $key => $field ) {
			$value = get_post_meta( $post->ID, $key, true );

			echo '<div class="guebel-field" style="margin-bottom: 15px;">';

			switch ( $field['type'] ) {
				case 'checkbox':
					printf(
						'<label><input type="checkbox" name="%s" value="yes" %s /> %s</label>',
						esc_attr( $key ),
						checked( $value, 'yes', false ),
						esc_html( $field['label'] )
					);
					break;

				case 'text':
					printf(
						'<label for="%1$s"><strong>%2$s</strong></label><br>',
						esc_attr( $key ),
						esc_html( $field['label'] )
					);
					printf(
						'<input type="text" id="%1$s" name="%1$s" value="%2$s" placeholder="%3$s" class="regular-text" style="width: 100%%; max-width: 400px; margin-top: 5px;" />',
						esc_attr( $key ),
						esc_attr( $value ),
						esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' )
					);
					break;

				case 'textarea':
					printf(
						'<label for="%1$s"><strong>%2$s</strong></label><br>',
						esc_attr( $key ),
						esc_html( $field['label'] )
					);
					printf(
						'<textarea id="%1$s" name="%1$s" rows="3" style="width: 100%%; max-width: 600px; margin-top: 5px;">%2$s</textarea>',
						esc_attr( $key ),
						esc_textarea( $value )
					);
					break;
			}

			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Save product meta fields.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save_product_meta( $post_id, $post ) {
		// Verify nonce.
		if ( ! isset( $_POST['guebel_product_meta_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['guebel_product_meta_nonce'] ) ), 'guebel_save_product_meta' ) ) {
			return;
		}

		// Check autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save each field.
		foreach ( $this->meta_fields as $key => $field ) {
			switch ( $field['type'] ) {
				case 'checkbox':
					$value = isset( $_POST[ $key ] ) ? 'yes' : 'no';
					update_post_meta( $post_id, $key, $value );
					break;

				case 'text':
					if ( isset( $_POST[ $key ] ) ) {
						$value = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
						update_post_meta( $post_id, $key, $value );
					}
					break;

				case 'textarea':
					if ( isset( $_POST[ $key ] ) ) {
						$value = sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) );
						update_post_meta( $post_id, $key, $value );
					}
					break;
			}
		}
	}

	/**
	 * Display product badges on the product page.
	 */
	public function display_product_badges() {
		global $product;

		if ( ! $product ) {
			return;
		}

		$is_3d_printed  = get_post_meta( $product->get_id(), '_guebel_is_3d_printed', true );
		$is_customizable = get_post_meta( $product->get_id(), '_guebel_customizable', true );

		if ( 'yes' === $is_3d_printed || 'yes' === $is_customizable ) {
			echo '<div class="guebel-product-badges" style="margin-top: 10px;">';

			if ( 'yes' === $is_3d_printed ) {
				printf(
					'<span class="guebel-badge guebel-badge--3d" style="display: inline-block; background: #2c3e50; color: #fff; padding: 4px 12px; border-radius: 3px; font-size: 12px; margin-right: 5px;">%s</span>',
					esc_html__( 'Impresso em 3D', 'guebel-core' )
				);
			}

			if ( 'yes' === $is_customizable ) {
				printf(
					'<span class="guebel-badge guebel-badge--custom" style="display: inline-block; background: #27ae60; color: #fff; padding: 4px 12px; border-radius: 3px; font-size: 12px;">%s</span>',
					esc_html__( 'Personalizável', 'guebel-core' )
				);
			}

			echo '</div>';
		}
	}

	/**
	 * Display production time on the product page.
	 */
	public function display_production_time() {
		global $product;

		if ( ! $product ) {
			return;
		}

		$production_time = get_post_meta( $product->get_id(), '_guebel_production_time', true );

		if ( ! empty( $production_time ) ) {
			printf(
				'<div class="guebel-production-time" style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-left: 3px solid #2c3e50; font-size: 14px;"><strong>%s:</strong> %s</div>',
				esc_html__( 'Tempo de produção', 'guebel-core' ),
				esc_html( $production_time )
			);
		}
	}

	/**
	 * Add custom product tabs.
	 *
	 * @param array $tabs Existing tabs.
	 * @return array
	 */
	public function add_product_tabs( $tabs ) {
		global $product;

		if ( ! $product ) {
			return $tabs;
		}

		$sustainability = get_post_meta( $product->get_id(), '_guebel_sustainability_info', true );
		$dimensions     = get_post_meta( $product->get_id(), '_guebel_dimensions_detail', true );
		$care           = get_post_meta( $product->get_id(), '_guebel_care_instructions', true );

		if ( ! empty( $sustainability ) ) {
			$tabs['guebel_sustainability'] = array(
				'title'    => __( 'Sustentabilidade', 'guebel-core' ),
				'priority' => 30,
				'callback' => array( $this, 'render_sustainability_tab' ),
			);
		}

		if ( ! empty( $dimensions ) ) {
			$tabs['guebel_dimensions'] = array(
				'title'    => __( 'Dimensões', 'guebel-core' ),
				'priority' => 25,
				'callback' => array( $this, 'render_dimensions_tab' ),
			);
		}

		if ( ! empty( $care ) ) {
			$tabs['guebel_care'] = array(
				'title'    => __( 'Cuidados', 'guebel-core' ),
				'priority' => 35,
				'callback' => array( $this, 'render_care_tab' ),
			);
		}

		return $tabs;
	}

	/**
	 * Render sustainability tab content.
	 */
	public function render_sustainability_tab() {
		global $product;
		$info = get_post_meta( $product->get_id(), '_guebel_sustainability_info', true );
		if ( ! empty( $info ) ) {
			printf( '<h2>%s</h2>', esc_html__( 'Sustentabilidade', 'guebel-core' ) );
			echo wp_kses_post( wpautop( $info ) );
		}
	}

	/**
	 * Render dimensions tab content.
	 */
	public function render_dimensions_tab() {
		global $product;
		$info = get_post_meta( $product->get_id(), '_guebel_dimensions_detail', true );
		if ( ! empty( $info ) ) {
			printf( '<h2>%s</h2>', esc_html__( 'Dimensões Detalhadas', 'guebel-core' ) );
			echo wp_kses_post( wpautop( $info ) );
		}
	}

	/**
	 * Render care instructions tab content.
	 */
	public function render_care_tab() {
		global $product;
		$info = get_post_meta( $product->get_id(), '_guebel_care_instructions', true );
		if ( ! empty( $info ) ) {
			printf( '<h2>%s</h2>', esc_html__( 'Instruções de Cuidado', 'guebel-core' ) );
			echo wp_kses_post( wpautop( $info ) );
		}
	}
}
