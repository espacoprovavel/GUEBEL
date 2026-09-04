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

		$ip = $this->get_client_ip();

		// 1) Always store the submission so nothing is ever lost, even if
		// the mail server fails. This is what powers the admin inbox + export.
		global $wpdb;
		$stored = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prefix . 'guebel_contacts',
			array(
				'name'       => $name,
				'email'      => $email,
				'phone'      => $phone,
				'message'    => $message,
				'marketing'  => $marketing ? 1 : 0,
				'consent'    => 1,
				'consent_ip' => $ip,
				'status'     => 'new',
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s' )
		);

		// 2) Notify the store (best effort — a mail failure does not lose data).
		$to = $this->contact_recipient();

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'Reply-To: ' . $name . ' <' . $email . '>',
		);

		/* translators: %s: visitor name */
		$subject = sprintf( __( 'Novo contacto do site — %s', 'guebel-core' ), $name );
		wp_mail( $to, $subject, $this->contact_email_admin( $name, $email, $phone, $message, $marketing, $ip ), $headers );

		// 3) Send a branded confirmation to the customer.
		$cust_headers = array( 'Content-Type: text/html; charset=UTF-8' );
		wp_mail(
			$email,
			/* translators: %s: store name */
			sprintf( __( 'Recebemos a sua mensagem — %s', 'guebel-core' ), get_bloginfo( 'name' ) ),
			$this->contact_email_customer( $name, $message ),
			$cust_headers
		);

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

		if ( false === $stored ) {
			wp_send_json_error(
				array( 'message' => __( 'Não foi possível registar a mensagem. Por favor, tente mais tarde.', 'guebel-core' ) )
			);
		}

		wp_send_json_success(
			array( 'message' => __( 'Mensagem enviada com sucesso! Entraremos em contacto em breve.', 'guebel-core' ) )
		);
	}

	/**
	 * Resolve the recipient email for contact notifications.
	 *
	 * Order: dedicated setting, then general store email, then Customizer, then admin email.
	 *
	 * @return string
	 */
	private function contact_recipient() {
		$to = Guebel_Core::get_option( 'contact_recipient' );
		if ( empty( $to ) || ! is_email( $to ) ) {
			$to = Guebel_Core::get_option( 'email' );
		}
		if ( empty( $to ) || ! is_email( $to ) ) {
			$to = get_theme_mod( 'guebel_email', '' );
		}
		if ( empty( $to ) || ! is_email( $to ) ) {
			$to = get_option( 'admin_email' );
		}
		return $to;
	}

	/**
	 * Wrap email content in a branded HTML shell.
	 *
	 * @param string $inner Inner HTML.
	 * @return string
	 */
	private function email_shell( $inner ) {
		$name = esc_html( get_bloginfo( 'name' ) );
		return '<!DOCTYPE html><html><body style="margin:0;padding:0;background:#f4eddb;font-family:Helvetica,Arial,sans-serif;">'
			. '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eddb;padding:32px 0;"><tr><td align="center">'
			. '<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:4px;overflow:hidden;">'
			. '<tr><td style="background:#33604e;padding:28px 32px;text-align:center;">'
			. '<span style="color:#f4eddb;font-size:20px;letter-spacing:6px;text-transform:uppercase;">' . $name . '</span></td></tr>'
			. '<tr><td style="padding:32px;color:#3a4a44;font-size:15px;line-height:1.7;">' . $inner . '</td></tr>'
			. '<tr><td style="background:#efe7d0;padding:18px 32px;text-align:center;color:#8a938c;font-size:12px;">&copy; '
			. esc_html( gmdate( 'Y' ) ) . ' ' . $name . '</td></tr>'
			. '</table></td></tr></table></body></html>';
	}

	/**
	 * Build the admin notification email (HTML).
	 *
	 * @return string
	 */
	private function contact_email_admin( $name, $email, $phone, $message, $marketing, $ip ) {
		$rows  = '<h2 style="color:#33604e;font-size:18px;margin:0 0 20px;">' . esc_html__( 'Novo contacto do site', 'guebel-core' ) . '</h2>';
		$rows .= '<p style="margin:0 0 6px;"><strong>' . esc_html__( 'Nome', 'guebel-core' ) . ':</strong> ' . esc_html( $name ) . '</p>';
		$rows .= '<p style="margin:0 0 6px;"><strong>' . esc_html__( 'Email', 'guebel-core' ) . ':</strong> <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></p>';
		if ( $phone ) {
			$rows .= '<p style="margin:0 0 6px;"><strong>' . esc_html__( 'Telefone', 'guebel-core' ) . ':</strong> ' . esc_html( $phone ) . '</p>';
		}
		$rows .= '<p style="margin:16px 0 6px;"><strong>' . esc_html__( 'Mensagem', 'guebel-core' ) . ':</strong></p>';
		$rows .= '<div style="background:#f7f3e8;border-left:3px solid #33604e;padding:14px 16px;color:#3a4a44;">' . nl2br( esc_html( $message ) ) . '</div>';
		$rows .= '<p style="margin:18px 0 0;color:#8a938c;font-size:12px;">' . esc_html__( 'Marketing autorizado', 'guebel-core' ) . ': ' . ( $marketing ? esc_html__( 'Sim', 'guebel-core' ) : esc_html__( 'Não', 'guebel-core' ) ) . ' &middot; IP: ' . esc_html( $ip ) . '</p>';
		return $this->email_shell( $rows );
	}

	/**
	 * Build the customer confirmation email (HTML).
	 *
	 * @return string
	 */
	private function contact_email_customer( $name, $message ) {
		$inner  = '<p style="margin:0 0 16px;font-size:16px;color:#33604e;">' . sprintf( /* translators: %s: customer name */ esc_html__( 'Olá %s,', 'guebel-core' ), esc_html( $name ) ) . '</p>';
		$inner .= '<p style="margin:0 0 16px;">' . esc_html__( 'Obrigado pela sua mensagem! Recebemos o seu contacto e a nossa equipa responderá o mais breve possível.', 'guebel-core' ) . '</p>';
		$inner .= '<p style="margin:0 0 6px;color:#8a938c;font-size:13px;">' . esc_html__( 'Cópia da sua mensagem:', 'guebel-core' ) . '</p>';
		$inner .= '<div style="background:#f7f3e8;border-left:3px solid #c8a96e;padding:14px 16px;color:#3a4a44;">' . nl2br( esc_html( $message ) ) . '</div>';
		$inner .= '<p style="margin:24px 0 0;">' . esc_html__( 'Com os melhores cumprimentos,', 'guebel-core' ) . '<br><strong>' . esc_html( get_bloginfo( 'name' ) ) . '</strong></p>';
		return $this->email_shell( $inner );
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
