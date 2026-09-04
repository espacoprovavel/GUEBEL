<?php
/**
 * Plugin Name: Guebel Core
 * Description: Core functionality for the Guebel store - custom post types, taxonomies, widgets, contact form, RGPD newsletter, and integrations
 * Version: 1.2.0
 * Author: Guebel
 * Text Domain: guebel-core
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GUEBEL_CORE_VERSION', '1.2.0' );
define( 'GUEBEL_CORE_PLUGIN_FILE', __FILE__ );
define( 'GUEBEL_CORE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GUEBEL_CORE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GUEBEL_CORE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Guebel Core plugin class.
 */
final class Guebel_Core {

	/**
	 * Single instance of the class.
	 *
	 * @var Guebel_Core|null
	 */
	private static $instance = null;

	/**
	 * Get the single instance.
	 *
	 * @return Guebel_Core
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Include required files.
	 */
	private function includes() {
		$dir = GUEBEL_CORE_PLUGIN_DIR;

		$files = array(
			'includes/class-custom-post-types.php',
			'includes/class-product-features.php',
			'includes/class-demo-content.php',
			'includes/class-admin-settings.php',
			'includes/class-shortcodes.php',
			'includes/class-ajax-handlers.php',
			'includes/class-submissions.php',
		);

		foreach ( $files as $file ) {
			$path = $dir . $file;
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		register_activation_hook( GUEBEL_CORE_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( GUEBEL_CORE_PLUGIN_FILE, array( $this, 'deactivate' ) );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'maybe_upgrade' ) );
		add_action( 'admin_notices', array( $this, 'dependency_notices' ) );
		add_action( 'init', array( $this, 'init_modules' ), 5 );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Plugin activation.
	 */
	public function activate() {
		if ( class_exists( 'Guebel_Custom_Post_Types' ) ) {
			$cpt = new Guebel_Custom_Post_Types();
			$cpt->register_post_types();
			$cpt->register_taxonomies();
		}

		flush_rewrite_rules();
		$this->create_newsletter_table();
		$this->create_contacts_table();
		update_option( 'guebel_core_version', GUEBEL_CORE_VERSION );
	}

	/**
	 * Plugin deactivation.
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Create newsletter subscribers table.
	 */
	private function create_newsletter_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'guebel_newsletter';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			email varchar(255) NOT NULL,
			name varchar(255) DEFAULT '',
			status varchar(20) NOT NULL DEFAULT 'subscribed',
			consent tinyint(1) NOT NULL DEFAULT 0,
			consent_date datetime DEFAULT NULL,
			consent_ip varchar(45) DEFAULT '',
			created_at datetime NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY email (email)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Create the contact submissions table.
	 */
	private function create_contacts_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'guebel_contacts';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			name varchar(255) DEFAULT '',
			email varchar(255) NOT NULL,
			phone varchar(60) DEFAULT '',
			message text,
			marketing tinyint(1) NOT NULL DEFAULT 0,
			consent tinyint(1) NOT NULL DEFAULT 0,
			consent_ip varchar(45) DEFAULT '',
			status varchar(20) NOT NULL DEFAULT 'new',
			created_at datetime NOT NULL,
			PRIMARY KEY (id),
			KEY email (email)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Run database upgrades when the stored version is behind.
	 *
	 * Ensures the newsletter table gains the RGPD consent columns on
	 * existing installs (dbDelta adds missing columns safely).
	 */
	public function maybe_upgrade() {
		$stored = get_option( 'guebel_core_version', '' );
		if ( GUEBEL_CORE_VERSION === $stored ) {
			return;
		}
		$this->create_newsletter_table();
		$this->create_contacts_table();
		update_option( 'guebel_core_version', GUEBEL_CORE_VERSION );
	}

	/**
	 * Load plugin text domain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'guebel-core',
			false,
			dirname( GUEBEL_CORE_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Show admin notices for missing dependencies.
	 */
	public function dependency_notices() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<div class="notice notice-warning is-dismissible"><p>';
			echo '<strong>Guebel Core</strong> ';
			echo esc_html__( 'recomenda o WooCommerce para funcionalidades completas de e-commerce.', 'guebel-core' );
			echo '</p></div>';
		}
	}

	/**
	 * Initialize plugin modules.
	 */
	public function init_modules() {
		if ( class_exists( 'Guebel_Custom_Post_Types' ) ) {
			new Guebel_Custom_Post_Types();
		}
		if ( class_exists( 'Guebel_Product_Features' ) ) {
			new Guebel_Product_Features();
		}
		if ( class_exists( 'Guebel_Demo_Content' ) ) {
			new Guebel_Demo_Content();
		}
		if ( class_exists( 'Guebel_Admin_Settings' ) ) {
			new Guebel_Admin_Settings();
		}
		if ( class_exists( 'Guebel_Shortcodes' ) ) {
			new Guebel_Shortcodes();
		}
		if ( class_exists( 'Guebel_Ajax_Handlers' ) ) {
			new Guebel_Ajax_Handlers();
		}
		if ( class_exists( 'Guebel_Submissions' ) ) {
			new Guebel_Submissions();
		}
	}

	/**
	 * Register widgets.
	 */
	public function register_widgets() {
		$widget_file = GUEBEL_CORE_PLUGIN_DIR . 'widgets/class-widget-contact-info.php';
		if ( file_exists( $widget_file ) ) {
			require_once $widget_file;
		}
		if ( class_exists( 'Guebel_Widget_Contact_Info' ) ) {
			register_widget( 'Guebel_Widget_Contact_Info' );
		}
	}

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return bool
	 */
	public static function is_woocommerce_active() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Get a plugin option.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public static function get_option( $key, $default = '' ) {
		$options = get_option( 'guebel_settings', array() );
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}
}

/**
 * Uninstall hook.
 */
register_uninstall_hook( GUEBEL_CORE_PLUGIN_FILE, 'guebel_core_uninstall' );

/**
 * Plugin uninstall callback.
 */
function guebel_core_uninstall() {
	delete_option( 'guebel_settings' );
	delete_option( 'guebel_core_version' );
	delete_option( 'guebel_demo_content_installed' );

	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}guebel_newsletter" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}guebel_contacts" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

	$post_types = array( 'guebel_collection', 'guebel_testimonial' );
	foreach ( $post_types as $post_type ) {
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'fields'         => 'ids',
			)
		);
		foreach ( $posts as $post_id ) {
			wp_delete_post( $post_id, true );
		}
	}

	delete_post_meta_by_key( '_guebel_is_3d_printed' );
	delete_post_meta_by_key( '_guebel_production_time' );
	delete_post_meta_by_key( '_guebel_sustainability_info' );
	delete_post_meta_by_key( '_guebel_customizable' );
	delete_post_meta_by_key( '_guebel_dimensions_detail' );
	delete_post_meta_by_key( '_guebel_care_instructions' );

	$taxonomies = array( 'guebel_material', 'guebel_finish' );
	foreach ( $taxonomies as $taxonomy ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'fields'     => 'ids',
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term_id ) {
				wp_delete_term( $term_id, $taxonomy );
			}
		}
	}

	flush_rewrite_rules();
}

/**
 * Returns the main Guebel_Core instance.
 *
 * @return Guebel_Core
 */
function guebel_core() {
	return Guebel_Core::instance();
}

guebel_core();
