<?php
/**
 * AJAX Handlers.
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles AJAX requests.
 */
class Guebel_Ajax_Handlers {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Newsletter subscription (available to logged-in and guests).
		add_action( 'wp_ajax_guebel_newsletter_subscribe', array( $this, 'newsletter_subscribe' ) );
		add_action( 'wp_ajax_nopriv_guebel_newsletter_subscribe', array( $this, 'newsletter_subscribe' ) );

		// Quick view (available to all).
		add_action( 'wp_ajax_guebel_quick_view', array( $this, 'quick_view' ) );
		add_action( 'wp_ajax_nopriv_guebel_quick_view', array( $this, 'quick_view' ) );

		// Cart count (available to all).
		add_action( 'wp_ajax_guebel_cart_count', array( $this, 'cart_count' ) );
		add_action( 'wp_ajax_nopriv_guebel_cart_count', array( $this, 'cart_count' ) );

		// Enqueue scripts for front-end.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue front-end scripts with AJAX URL and nonce.
	 */
	public function enqueue_scripts() {
		wp_localize_script(
			'jquery',
			'guebel_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'guebel_ajax_nonce' ),
			)
		);
	}

	/**
	 * Handle newsletter subscription.
	 */
	public function newsletter_subscribe() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'guebel_ajax_nonce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Erro de segurança. Recarregue a página e tente novamente.', 'guebel-core' ) ),
				403
			);
		}

		// Validate email.
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		if ( empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Por favor, introduza um endereço de email válido.', 'guebel-core' ) )
			);
		}

		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

		global $wpdb;
		$table_name = $wpdb->prefix . 'guebel_newsletter';

		// Check if already subscribed.
		$existing = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT id FROM {$table_name} WHERE email = %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$email
			)
		);

		if ( $existing ) {
			wp_send_json_error(
				array( 'message' => __( 'Este email já está subscrito na nossa newsletter.', 'guebel-core' ) )
			);
		}

		// Insert subscriber.
		$result = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table_name,
			array(
				'email'      => $email,
				'name'       => $name,
				'status'     => 'subscribed',
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s' )
		);

		if ( false === $result ) {
			wp_send_json_error(
				array( 'message' => __( 'Ocorreu um erro. Por favor, tente novamente mais tarde.', 'guebel-core' ) )
			);
		}

		/**
		 * Fires after a successful newsletter subscription.
		 *
		 * @param string $email Subscriber email.
		 * @param string $name  Subscriber name.
		 */
		do_action( 'guebel_newsletter_subscribed', $email, $name );

		wp_send_json_success(
			array( 'message' => __( 'Subscrição realizada com sucesso! Obrigado.', 'guebel-core' ) )
		);
	}

	/**
	 * Handle quick view product data.
	 */
	public function quick_view() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'guebel_ajax_nonce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Erro de segurança.', 'guebel-core' ) ),
				403
			);
		}

		if ( ! Guebel_Core::is_woocommerce_active() ) {
			wp_send_json_error(
				array( 'message' => __( 'WooCommerce não está ativo.', 'guebel-core' ) )
			);
		}

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

		if ( ! $product_id ) {
			wp_send_json_error(
				array( 'message' => __( 'Produto não encontrado.', 'guebel-core' ) )
			);
		}

		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			wp_send_json_error(
				array( 'message' => __( 'Produto não encontrado.', 'guebel-core' ) )
			);
		}

		$image_id  = $product->get_image_id();
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium_large' ) : wc_placeholder_img_src( 'medium_large' );

		$data = array(
			'id'              => $product->get_id(),
			'name'            => $product->get_name(),
			'price_html'      => $product->get_price_html(),
			'short_description' => wp_kses_post( $product->get_short_description() ),
			'image'           => esc_url( $image_url ),
			'permalink'       => get_permalink( $product_id ),
			'add_to_cart_url' => $product->add_to_cart_url(),
			'is_in_stock'     => $product->is_in_stock(),
			'is_3d_printed'   => get_post_meta( $product_id, '_guebel_is_3d_printed', true ) === 'yes',
			'is_customizable' => get_post_meta( $product_id, '_guebel_customizable', true ) === 'yes',
			'production_time' => get_post_meta( $product_id, '_guebel_production_time', true ),
			'sku'             => $product->get_sku(),
		);

		wp_send_json_success( $data );
	}

	/**
	 * Handle cart count update.
	 */
	public function cart_count() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'guebel_ajax_nonce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Erro de segurança.', 'guebel-core' ) ),
				403
			);
		}

		if ( ! Guebel_Core::is_woocommerce_active() ) {
			wp_send_json_error(
				array( 'message' => __( 'WooCommerce não está ativo.', 'guebel-core' ) )
			);
		}

		$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
		$total = WC()->cart ? WC()->cart->get_cart_total() : '';

		wp_send_json_success(
			array(
				'count' => absint( $count ),
				'total' => wp_kses_post( $total ),
			)
		);
	}
}
