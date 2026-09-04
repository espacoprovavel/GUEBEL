<?php
/**
 * SMTP delivery configuration.
 *
 * Routes wp_mail() through an authenticated SMTP server (e.g. Hostinger),
 * so contact/newsletter/WooCommerce emails are delivered reliably instead
 * of failing silently or landing in spam. Fully configurable in the admin.
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles SMTP settings, wp_mail routing and a test-email tool.
 */
class Guebel_SMTP {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	const OPTION = 'guebel_smtp';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'phpmailer_init', array( $this, 'configure' ) );
		add_filter( 'wp_mail_from', array( $this, 'mail_from' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'mail_from_name' ) );

		add_action( 'admin_menu', array( $this, 'add_page' ), 20 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_post_guebel_smtp_test', array( $this, 'send_test' ) );
	}

	/**
	 * Get settings with defaults.
	 *
	 * @return array
	 */
	public static function get() {
		$defaults = array(
			'enabled'    => 0,
			'host'       => 'smtp.hostinger.com',
			'port'       => '465',
			'encryption' => 'ssl',
			'auth'       => 1,
			'username'   => '',
			'password'   => '',
			'from_email' => '',
			'from_name'  => '',
		);
		$saved = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
	}

	/**
	 * Configure PHPMailer to use SMTP.
	 *
	 * @param PHPMailer\PHPMailer\PHPMailer|object $phpmailer PHPMailer instance.
	 */
	public function configure( $phpmailer ) {
		$s = self::get();
		if ( empty( $s['enabled'] ) || empty( $s['host'] ) ) {
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host        = $s['host'];
		$phpmailer->Port        = (int) $s['port'];
		$phpmailer->SMTPAuth    = ! empty( $s['auth'] );
		$phpmailer->SMTPSecure  = in_array( $s['encryption'], array( 'ssl', 'tls' ), true ) ? $s['encryption'] : '';
		$phpmailer->SMTPAutoTLS = ! empty( $s['encryption'] );

		if ( ! empty( $s['auth'] ) ) {
			$phpmailer->Username = $s['username'];
			$phpmailer->Password = $s['password'];
		}
	}

	/**
	 * Override the From email address.
	 *
	 * @param string $from Current from email.
	 * @return string
	 */
	public function mail_from( $from ) {
		$s = self::get();
		if ( ! empty( $s['enabled'] ) && ! empty( $s['from_email'] ) && is_email( $s['from_email'] ) ) {
			return $s['from_email'];
		}
		return $from;
	}

	/**
	 * Override the From name.
	 *
	 * @param string $name Current from name.
	 * @return string
	 */
	public function mail_from_name( $name ) {
		$s = self::get();
		if ( ! empty( $s['enabled'] ) && ! empty( $s['from_name'] ) ) {
			return $s['from_name'];
		}
		return $name;
	}

	/**
	 * Register the settings + fields.
	 */
	public function register_settings() {
		register_setting( 'guebel_smtp_group', self::OPTION, array( $this, 'sanitize' ) );
	}

	/**
	 * Sanitize settings; keep the existing password when the field is blank.
	 *
	 * @param array $input Raw input.
	 * @return array
	 */
	public function sanitize( $input ) {
		$current = self::get();
		$out     = array();

		$out['enabled']    = empty( $input['enabled'] ) ? 0 : 1;
		$out['host']       = isset( $input['host'] ) ? sanitize_text_field( $input['host'] ) : '';
		$out['port']       = isset( $input['port'] ) ? preg_replace( '/[^0-9]/', '', $input['port'] ) : '465';
		$out['encryption'] = isset( $input['encryption'] ) && in_array( $input['encryption'], array( 'none', 'ssl', 'tls' ), true ) ? ( 'none' === $input['encryption'] ? '' : $input['encryption'] ) : 'ssl';
		$out['auth']       = empty( $input['auth'] ) ? 0 : 1;
		$out['username']   = isset( $input['username'] ) ? sanitize_text_field( $input['username'] ) : '';
		$out['from_email'] = isset( $input['from_email'] ) ? sanitize_email( $input['from_email'] ) : '';
		$out['from_name']  = isset( $input['from_name'] ) ? sanitize_text_field( $input['from_name'] ) : '';

		// Password: blank means keep the stored one.
		$out['password'] = ( isset( $input['password'] ) && '' !== $input['password'] )
			? $input['password']
			: $current['password'];

		return $out;
	}

	/**
	 * Add the SMTP admin page under the Guebel Clientes menu.
	 */
	public function add_page() {
		add_submenu_page(
			'guebel-contacts',
			__( 'Envio de Email (SMTP)', 'guebel-core' ),
			__( 'Envio de Email (SMTP)', 'guebel-core' ),
			'manage_options',
			'guebel-smtp',
			array( $this, 'render' )
		);
	}

	/**
	 * Render the SMTP settings page.
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$s = self::get();

		if ( isset( $_GET['guebel_smtp_test'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$ok = 'ok' === sanitize_key( wp_unslash( $_GET['guebel_smtp_test'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			printf(
				'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
				$ok ? 'success' : 'error',
				$ok
					? esc_html__( 'Email de teste enviado! Verifique a sua caixa de entrada (e spam).', 'guebel-core' )
					: esc_html__( 'Falha ao enviar o email de teste. Confirme os dados SMTP (servidor, porta, utilizador e palavra-passe).', 'guebel-core' )
			);
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Envio de Email (SMTP)', 'guebel-core' ); ?></h1>
			<p class="description" style="max-width:720px;">
				<?php esc_html_e( 'Configure o servidor de email da sua loja para que as mensagens de contacto, confirmações e emails do WooCommerce sejam entregues de forma fiável. Para a Hostinger: servidor smtp.hostinger.com, porta 465 (SSL), utilizador = o email completo, palavra-passe = a palavra-passe desse email.', 'guebel-core' ); ?>
			</p>

			<form method="post" action="options.php">
				<?php settings_fields( 'guebel_smtp_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Ativar SMTP', 'guebel-core' ); ?></th>
						<td><label><input type="checkbox" name="guebel_smtp[enabled]" value="1" <?php checked( $s['enabled'], 1 ); ?>> <?php esc_html_e( 'Enviar emails através deste servidor SMTP', 'guebel-core' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Servidor SMTP', 'guebel-core' ); ?></th>
						<td><input type="text" class="regular-text" name="guebel_smtp[host]" value="<?php echo esc_attr( $s['host'] ); ?>" placeholder="smtp.hostinger.com"></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Porta', 'guebel-core' ); ?></th>
						<td><input type="text" class="small-text" name="guebel_smtp[port]" value="<?php echo esc_attr( $s['port'] ); ?>" placeholder="465"> <span class="description"><?php esc_html_e( '465 (SSL) ou 587 (TLS)', 'guebel-core' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Encriptação', 'guebel-core' ); ?></th>
						<td>
							<select name="guebel_smtp[encryption]">
								<option value="ssl" <?php selected( $s['encryption'], 'ssl' ); ?>>SSL</option>
								<option value="tls" <?php selected( $s['encryption'], 'tls' ); ?>>TLS</option>
								<option value="none" <?php selected( $s['encryption'], '' ); ?>><?php esc_html_e( 'Nenhuma', 'guebel-core' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Autenticação', 'guebel-core' ); ?></th>
						<td><label><input type="checkbox" name="guebel_smtp[auth]" value="1" <?php checked( $s['auth'], 1 ); ?>> <?php esc_html_e( 'O servidor exige utilizador e palavra-passe', 'guebel-core' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Utilizador (email)', 'guebel-core' ); ?></th>
						<td><input type="text" class="regular-text" name="guebel_smtp[username]" value="<?php echo esc_attr( $s['username'] ); ?>" placeholder="loja@guebel.pt" autocomplete="off"></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Palavra-passe', 'guebel-core' ); ?></th>
						<td>
							<input type="password" class="regular-text" name="guebel_smtp[password]" value="" placeholder="<?php echo $s['password'] ? esc_attr__( '•••••••• (guardada — deixe em branco para manter)', 'guebel-core' ) : ''; ?>" autocomplete="new-password">
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Email de origem (From)', 'guebel-core' ); ?></th>
						<td><input type="email" class="regular-text" name="guebel_smtp[from_email]" value="<?php echo esc_attr( $s['from_email'] ); ?>" placeholder="loja@guebel.pt"> <span class="description"><?php esc_html_e( 'Normalmente igual ao utilizador.', 'guebel-core' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Nome de origem', 'guebel-core' ); ?></th>
						<td><input type="text" class="regular-text" name="guebel_smtp[from_name]" value="<?php echo esc_attr( $s['from_name'] ? $s['from_name'] : get_bloginfo( 'name' ) ); ?>"></td>
					</tr>
				</table>
				<?php submit_button( __( 'Guardar Definições', 'guebel-core' ) ); ?>
			</form>

			<hr>
			<h2><?php esc_html_e( 'Enviar email de teste', 'guebel-core' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Guarde as definições primeiro. O teste é enviado para o email do administrador do site.', 'guebel-core' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="guebel_smtp_test">
				<?php wp_nonce_field( 'guebel_smtp_test' ); ?>
				<?php submit_button( __( 'Enviar email de teste', 'guebel-core' ), 'secondary', 'submit', false ); ?>
				<span class="description" style="margin-left:8px;"><?php echo esc_html( sprintf( /* translators: %s: admin email */ __( 'Para: %s', 'guebel-core' ), get_option( 'admin_email' ) ) ); ?></span>
			</form>
		</div>
		<?php
	}

	/**
	 * Send a test email and redirect back with the result.
	 */
	public function send_test() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sem permissões.', 'guebel-core' ) );
		}
		check_admin_referer( 'guebel_smtp_test' );

		$to      = get_option( 'admin_email' );
		$subject = sprintf( /* translators: %s: site name */ __( 'Teste SMTP — %s', 'guebel-core' ), get_bloginfo( 'name' ) );
		$body    = '<p>' . esc_html__( 'Este é um email de teste do seu site. Se o recebeu, o envio SMTP está a funcionar corretamente.', 'guebel-core' ) . '</p>';
		$ok      = wp_mail( $to, $subject, $body, array( 'Content-Type: text/html; charset=UTF-8' ) );

		wp_safe_redirect( admin_url( 'admin.php?page=guebel-smtp&guebel_smtp_test=' . ( $ok ? 'ok' : 'fail' ) ) );
		exit;
	}
}
