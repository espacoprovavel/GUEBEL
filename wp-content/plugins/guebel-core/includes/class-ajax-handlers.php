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

		// Contact form (available to all).
		add_action( 'wp_ajax_guebel_contact_submit', array( $this, 'contact_submit' ) );
		add_action( 'wp_ajax_nopriv_guebel_contact_submit', array( $this, 'contact_submit' ) );

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

		// RGPD: explicit marketing consent is required and recorded.
		$consent = isset( $_POST['consent'] ) && in_array( wp_unslash( $_POST['consent'] ), array( '1', 'yes', 'true', 'on' ), true );
		if ( ! $consent ) {
			wp_send_json_error(
				array( 'message' => __( 'Precisa de autorizar a receção de comunicações para subscrever.', 'guebel-core' ) )
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

		// Insert subscriber with RGPD consent proof (date + IP).
		$result = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table_name,
			array(
				'email'        => $email,
				'name'         => $name,
				'status'       => 'subscribed',
				'consent'      => 1,
				'consent_date' => current_time( 'mysql' ),
				'consent_ip'   => $this->get_client_ip(),
				'created_at'   => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
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
	 * Handle contact form submission.
	 *
	 * Sends the enquiry to the store email. RGPD: requires the visitor to
	 * accept the privacy policy before the message is processed.
	 */
	public function contact_submit() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'guebel_ajax_nonce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Erro de segurança. Recarregue a página e tente novamente.', 'guebel-core' ) ),
				403
			);
		}

		$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

		if ( empty( $name ) ) {
			wp_send_json_error( array( 'message' => __( 'Por favor, indique o seu nome.', 'guebel-core' ) ) );
		}
		if ( empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Por favor, introduza um endereço de email válido.', 'guebel-core' ) ) );
		}
		if ( empty( $message ) ) {
			wp_send_json_error( array( 'message' => __( 'Por favor, escreva a sua mensagem.', 'guebel-core' ) ) );
		}

		// RGPD: privacy policy acceptance is required.
		$consent = isset( $_POST['consent'] ) && in_array( wp_unslash( $_POST['consent'] ), array( '1', 'yes', 'true', 'on' ), true );
		if ( ! $consent ) {
			wp_send_json_error( array( 'message' => __( 'Precisa de aceitar a Política de Privacidade para enviar a mensagem.', 'guebel-core' ) ) );
		}

		// Optional marketing opt-in from the same form.
		$marketing = isset( $_POST['marketing'] ) && in_array( wp_unslash( $_POST['marketing'] ), array( '1', 'yes', 'true', 'on' ), true );
		if ( $marketing ) {
			$this->store_newsletter_optin( $email, $name );
		}

		// Recipient: plugin setting, then Customizer email, then admin email.
		$to = Guebel_Core::get_option( 'email' );
		if ( empty( $to ) || ! is_email( $to ) ) {
			$to = get_theme_mod( 'guebel_email', '' );
		}
		if ( empty( $to ) || ! is_email( $to ) ) {
			$to = get_option( 'admin_email' );
		}

		$site_name = get_bloginfo( 'name' );
		/* translators: %s: visitor name */
		$subject = sprintf( __( 'Novo contacto do site — %s', 'guebel-core' ), $name );

		$body  = __( 'Nova mensagem do formulário de contacto:', 'guebel-core' ) . "\n\n";
		$body .= __( 'Nome', 'guebel-core' ) . ': ' . $name . "\n";
		$body .= __( 'Email', 'guebel-core' ) . ': ' . $email . "\n";
		if ( $phone ) {
			$body .= __( 'Telefone', 'guebel-core' ) . ': ' . $phone . "\n";
		}
		$body .= __( 'Mensagem', 'guebel-core' ) . ":\n" . $message . "\n\n";
		$body .= __( 'Marketing autorizado', 'guebel-core' ) . ': ' . ( $marketing ? __( 'Sim', 'guebel-core' ) : __( 'Não', 'guebel-core' ) ) . "\n";
		$body .= 'IP: ' . $this->get_client_ip() . "\n";

		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'Reply-To: ' . $name . ' <' . $email . '>',
		);

		$sent = wp_mail( $to, $subject, $body, $headers );

		/**
		 * Fires after a contact form submission (regardless of email result).
		 *
		 * @param array $data Submitted, sanitized data.
		 */
		do_action(
			'guebel_contact_submitted',
			array(
				'name'      => $name,
				'email'     => $email,
				'phone'     => $phone,
				'message'   => $message,
				'marketing' => $marketing,
			)
		);

		if ( ! $sent ) {
			wp_send_json_error(
				array( 'message' => __( 'Não foi possível enviar a mensagem. Por favor, tente mais tarde ou contacte-nos por email.', 'guebel-core' ) )
			);
		}

		wp_send_json_success(
			array( 'message' => __( 'Mensagem enviada com sucesso! Entraremos em contacto em breve.', 'guebel-core' ) )
		);
	}

	/**
	 * Store a newsletter opt-in (used by the contact form marketing checkbox).
	 *
	 * @param string $email Email address.
	 * @param string $name  Name.
	 */
	private function store_newsletter_optin( $email, $name ) {
		if ( empty( $email ) || ! is_email( $email ) ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'guebel_newsletter';

		$existing = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT id FROM {$table_name} WHERE email = %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$email
			)
		);

		if ( $existing ) {
			return;
		}

		$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table_name,
			array(
				'email'        => $email,
				'name'         => $name,
				'status'       => 'subscribed',
				'consent'      => 1,
				'consent_date' => current_time( 'mysql' ),
				'consent_ip'   => $this->get_client_ip(),
				'created_at'   => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
		);
	}

	/**
	 * Get the client IP address (best effort, for consent proof).
	 *
	 * @return string
	 */
	private function get_client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return $ip ? $ip : '0.0.0.0';
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
