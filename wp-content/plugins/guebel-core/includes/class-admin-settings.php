<?php
/**
 * Admin Settings Page.
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the plugin settings page.
 */
class Guebel_Admin_Settings {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	private $option_name = 'guebel_settings';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add settings page under Settings menu.
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Guebel - Definições', 'guebel-core' ),
			__( 'Guebel', 'guebel-core' ),
			'manage_options',
			'guebel-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register all settings.
	 */
	public function register_settings() {
		register_setting(
			'guebel_settings_group',
			$this->option_name,
			array( $this, 'sanitize_settings' )
		);

		// Brand Section.
		add_settings_section(
			'guebel_brand',
			__( 'Marca', 'guebel-core' ),
			array( $this, 'render_brand_section' ),
			'guebel-settings'
		);

		$this->add_field( 'store_name', __( 'Nome da Loja', 'guebel-core' ), 'text', 'guebel_brand' );
		$this->add_field( 'tagline', __( 'Slogan', 'guebel-core' ), 'text', 'guebel_brand' );
		$this->add_field( 'description', __( 'Descrição', 'guebel-core' ), 'textarea', 'guebel_brand' );

		// Contact Section.
		add_settings_section(
			'guebel_contact',
			__( 'Contacto', 'guebel-core' ),
			array( $this, 'render_contact_section' ),
			'guebel-settings'
		);

		$this->add_field( 'email', __( 'Email', 'guebel-core' ), 'email', 'guebel_contact' );
		$this->add_field( 'phone', __( 'Telefone', 'guebel-core' ), 'text', 'guebel_contact' );
		$this->add_field( 'whatsapp', __( 'WhatsApp', 'guebel-core' ), 'text', 'guebel_contact' );
		$this->add_field( 'address', __( 'Morada', 'guebel-core' ), 'text', 'guebel_contact' );
		$this->add_field( 'city', __( 'Cidade', 'guebel-core' ), 'text', 'guebel_contact' );
		$this->add_field( 'postal_code', __( 'Código Postal', 'guebel-core' ), 'text', 'guebel_contact' );
		$this->add_field( 'country', __( 'País', 'guebel-core' ), 'text', 'guebel_contact' );
		$this->add_field( 'business_hours', __( 'Horário de Funcionamento', 'guebel-core' ), 'textarea', 'guebel_contact' );

		// Social Section.
		add_settings_section(
			'guebel_social',
			__( 'Redes Sociais', 'guebel-core' ),
			array( $this, 'render_social_section' ),
			'guebel-settings'
		);

		$this->add_field( 'instagram_url', __( 'Instagram URL', 'guebel-core' ), 'url', 'guebel_social' );
		$this->add_field( 'pinterest_url', __( 'Pinterest URL', 'guebel-core' ), 'url', 'guebel_social' );
		$this->add_field( 'facebook_url', __( 'Facebook URL', 'guebel-core' ), 'url', 'guebel_social' );
		$this->add_field( 'tiktok_url', __( 'TikTok URL', 'guebel-core' ), 'url', 'guebel_social' );
		$this->add_field( 'youtube_url', __( 'YouTube URL', 'guebel-core' ), 'url', 'guebel_social' );

		// E-commerce Section.
		add_settings_section(
			'guebel_ecommerce',
			__( 'E-commerce', 'guebel-core' ),
			array( $this, 'render_ecommerce_section' ),
			'guebel-settings'
		);

		$this->add_field( 'currency', __( 'Moeda', 'guebel-core' ), 'text', 'guebel_ecommerce' );
		$this->add_field( 'free_shipping_threshold', __( 'Portes grátis a partir de (EUR)', 'guebel-core' ), 'number', 'guebel_ecommerce' );
		$this->add_field( 'production_time_message', __( 'Mensagem de tempo de produção', 'guebel-core' ), 'text', 'guebel_ecommerce' );
		$this->add_field( 'badges_enabled', __( 'Ativar selos nos produtos', 'guebel-core' ), 'checkbox', 'guebel_ecommerce' );

		// SEO Section.
		add_settings_section(
			'guebel_seo',
			__( 'SEO', 'guebel-core' ),
			array( $this, 'render_seo_section' ),
			'guebel-settings'
		);

		$this->add_field( 'schema_org_name', __( 'Nome da Organização (Schema)', 'guebel-core' ), 'text', 'guebel_seo' );
		$this->add_field( 'schema_logo_url', __( 'URL do Logótipo (Schema)', 'guebel-core' ), 'url', 'guebel_seo' );
		$this->add_field( 'schema_social_profiles', __( 'Perfis sociais (Schema, um por linha)', 'guebel-core' ), 'textarea', 'guebel_seo' );
	}

	/**
	 * Add a settings field.
	 *
	 * @param string $id      Field ID.
	 * @param string $label   Field label.
	 * @param string $type    Field type.
	 * @param string $section Section ID.
	 */
	private function add_field( $id, $label, $type, $section ) {
		add_settings_field(
			'guebel_' . $id,
			$label,
			array( $this, 'render_field' ),
			'guebel-settings',
			$section,
			array(
				'id'   => $id,
				'type' => $type,
			)
		);
	}

	/**
	 * Render a settings field.
	 *
	 * @param array $args Field arguments.
	 */
	public function render_field( $args ) {
		$options = get_option( $this->option_name, array() );
		$id      = $args['id'];
		$type    = $args['type'];
		$value   = isset( $options[ $id ] ) ? $options[ $id ] : '';
		$name    = $this->option_name . '[' . $id . ']';

		switch ( $type ) {
			case 'text':
			case 'email':
			case 'url':
			case 'number':
				printf(
					'<input type="%1$s" id="guebel_%2$s" name="%3$s" value="%4$s" class="regular-text" />',
					esc_attr( $type ),
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value )
				);
				break;

			case 'textarea':
				printf(
					'<textarea id="guebel_%1$s" name="%2$s" rows="4" class="large-text">%3$s</textarea>',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_textarea( $value )
				);
				break;

			case 'checkbox':
				printf(
					'<input type="checkbox" id="guebel_%1$s" name="%2$s" value="1" %3$s />',
					esc_attr( $id ),
					esc_attr( $name ),
					checked( $value, '1', false )
				);
				break;
		}
	}

	/**
	 * Render brand section description.
	 */
	public function render_brand_section() {
		echo '<p>' . esc_html__( 'Informações sobre a sua marca.', 'guebel-core' ) . '</p>';
	}

	/**
	 * Render contact section description.
	 */
	public function render_contact_section() {
		echo '<p>' . esc_html__( 'Dados de contacto da sua loja.', 'guebel-core' ) . '</p>';
	}

	/**
	 * Render social section description.
	 */
	public function render_social_section() {
		echo '<p>' . esc_html__( 'Links para as suas redes sociais.', 'guebel-core' ) . '</p>';
	}

	/**
	 * Render e-commerce section description.
	 */
	public function render_ecommerce_section() {
		echo '<p>' . esc_html__( 'Definições de e-commerce.', 'guebel-core' ) . '</p>';
	}

	/**
	 * Render SEO section description.
	 */
	public function render_seo_section() {
		echo '<p>' . esc_html__( 'Definições para Schema.org e SEO.', 'guebel-core' ) . '</p>';
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $input Raw input.
	 * @return array Sanitized output.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		// Text fields.
		$text_fields = array(
			'store_name',
			'tagline',
			'phone',
			'whatsapp',
			'address',
			'city',
			'postal_code',
			'country',
			'currency',
			'production_time_message',
			'schema_org_name',
		);

		foreach ( $text_fields as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$sanitized[ $field ] = sanitize_text_field( $input[ $field ] );
			}
		}

		// Email.
		if ( isset( $input['email'] ) ) {
			$sanitized['email'] = sanitize_email( $input['email'] );
		}

		// URL fields.
		$url_fields = array(
			'instagram_url',
			'pinterest_url',
			'facebook_url',
			'tiktok_url',
			'youtube_url',
			'schema_logo_url',
		);

		foreach ( $url_fields as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$sanitized[ $field ] = esc_url_raw( $input[ $field ] );
			}
		}

		// Textarea fields.
		$textarea_fields = array( 'description', 'business_hours', 'schema_social_profiles' );
		foreach ( $textarea_fields as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$sanitized[ $field ] = sanitize_textarea_field( $input[ $field ] );
			}
		}

		// Number fields.
		if ( isset( $input['free_shipping_threshold'] ) ) {
			$sanitized['free_shipping_threshold'] = absint( $input['free_shipping_threshold'] );
		}

		// Checkbox fields.
		$sanitized['badges_enabled'] = isset( $input['badges_enabled'] ) ? '1' : '0';

		return $sanitized;
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Guebel - Definições', 'guebel-core' ); ?></h1>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'guebel_settings_group' );
				do_settings_sections( 'guebel-settings' );
				submit_button( __( 'Guardar Definições', 'guebel-core' ) );
				?>
			</form>
		</div>
		<?php
	}
}
