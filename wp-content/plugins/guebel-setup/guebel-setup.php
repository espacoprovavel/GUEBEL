<?php
/**
 * Plugin Name: Guebel Setup
 * Description: Cria as páginas iniciais e importa os templates Elementor do tema Guebel. Reactivar volta a importar os templates (actualiza o design das páginas).
 * Version: 1.1.0
 * Author: Guebel
 * Text Domain: guebel-setup
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package Guebel_Setup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, 'guebel_setup_activate' );

/**
 * Run on plugin activation: create pages and import Elementor templates.
 */
function guebel_setup_activate() {
	// Force a fresh import of the Elementor templates on every (re)activation,
	// so re-activating the plugin refreshes the page designs.
	delete_option( 'guebel_setup_elementor_imported' );
	guebel_setup_create_pages();
	flush_rewrite_rules();
}

add_action( 'elementor/loaded', 'guebel_setup_maybe_import_elementor' );

/**
 * Fallback: import Elementor data when Elementor loads (in case it was not available at activation).
 */
function guebel_setup_maybe_import_elementor() {
	if ( get_option( 'guebel_setup_elementor_imported' ) ) {
		return;
	}
	guebel_setup_import_elementor_data();
	update_option( 'guebel_setup_elementor_imported', '1' );
}

/**
 * Create all pages.
 */
function guebel_setup_create_pages() {
	$pages = array(
		'inicio'       => array(
			'title'    => 'Início',
			'slug'     => 'inicio',
			'template' => 'elementor_header_footer',
			'json'     => 'inicio.json',
		),
		'sobre'        => array(
			'title'    => 'Sobre',
			'slug'     => 'sobre',
			'template' => 'elementor_header_footer',
			'json'     => 'sobre.json',
		),
		'contacto'     => array(
			'title'    => 'Contacto',
			'slug'     => 'contacto',
			'template' => 'elementor_header_footer',
			'json'     => 'contacto.json',
		),
		'faq'          => array(
			'title'    => 'FAQ',
			'slug'     => 'faq',
			'template' => 'elementor_header_footer',
			'json'     => 'faq.json',
		),
		'envios'       => array(
			'title'    => 'Envios e Entregas',
			'slug'     => 'envios',
			'template' => 'elementor_header_footer',
			'json'     => 'envios.json',
		),
		'trocas'       => array(
			'title'    => 'Trocas e Devoluções',
			'slug'     => 'trocas',
			'template' => 'elementor_header_footer',
			'json'     => 'trocas.json',
		),
		'termos'       => array(
			'title'    => 'Termos e Condições',
			'slug'     => 'termos',
			'template' => 'elementor_header_footer',
			'json'     => 'termos.json',
		),
		'privacidade'  => array(
			'title'    => 'Política de Privacidade',
			'slug'     => 'privacidade',
			'template' => 'elementor_header_footer',
			'json'     => 'privacidade.json',
		),
		'loja'         => array(
			'title'    => 'Loja',
			'slug'     => 'loja',
			'template' => '',
			'json'     => '',
			'wc_option' => 'woocommerce_shop_page_id',
		),
		'carrinho'     => array(
			'title'    => 'Carrinho',
			'slug'     => 'carrinho',
			'template' => '',
			'json'     => '',
			'wc_option' => 'woocommerce_cart_page_id',
		),
		'checkout'     => array(
			'title'    => 'Checkout',
			'slug'     => 'checkout',
			'template' => '',
			'json'     => '',
			'wc_option' => 'woocommerce_checkout_page_id',
		),
		'minha-conta'  => array(
			'title'    => 'Minha Conta',
			'slug'     => 'minha-conta',
			'template' => '',
			'json'     => '',
			'wc_option' => 'woocommerce_myaccount_page_id',
		),
	);

	$created_ids = array();

	foreach ( $pages as $key => $page ) {
		$existing = get_page_by_path( $page['slug'] );
		if ( $existing ) {
			$created_ids[ $key ] = $existing->ID;
			continue;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'   => $page['title'],
				'post_name'    => $page['slug'],
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);

		if ( is_wp_error( $page_id ) || ! $page_id ) {
			continue;
		}

		$created_ids[ $key ] = $page_id;

		if ( ! empty( $page['template'] ) ) {
			update_post_meta( $page_id, '_wp_page_template', $page['template'] );
		}

		if ( ! empty( $page['wc_option'] ) ) {
			update_option( $page['wc_option'], $page_id );
		}
	}

	if ( isset( $created_ids['inicio'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $created_ids['inicio'] );
	}

	if ( isset( $created_ids['loja'] ) ) {
		update_option( 'woocommerce_shop_page_id', $created_ids['loja'] );
	}

	update_option( 'guebel_setup_page_ids', $created_ids );

	guebel_setup_import_elementor_data();
}

/**
 * Import Elementor JSON data into pages.
 */
function guebel_setup_import_elementor_data() {
	$page_ids = get_option( 'guebel_setup_page_ids', array() );
	if ( empty( $page_ids ) ) {
		return;
	}

	$template_dir = get_template_directory() . '/elementor-templates/';

	$pages_with_json = array(
		'inicio'      => 'inicio.json',
		'sobre'       => 'sobre.json',
		'contacto'    => 'contacto.json',
		'faq'         => 'faq.json',
		'envios'      => 'envios.json',
		'trocas'      => 'trocas.json',
		'termos'      => 'termos.json',
		'privacidade' => 'privacidade.json',
	);

	foreach ( $pages_with_json as $key => $json_file ) {
		if ( ! isset( $page_ids[ $key ] ) ) {
			continue;
		}

		$page_id   = $page_ids[ $key ];
		$json_path = $template_dir . $json_file;

		if ( ! file_exists( $json_path ) ) {
			continue;
		}

		$json_content = file_get_contents( $json_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( empty( $json_content ) ) {
			continue;
		}

		$decoded = json_decode( $json_content, true );
		if ( null === $decoded ) {
			continue;
		}

		// Replace the theme URI token so bundled images resolve on any domain.
		$json_content = str_replace( '{{THEME_URI}}', get_template_directory_uri(), $json_content );

		update_post_meta( $page_id, '_elementor_data', wp_slash( $json_content ) );
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_version', '3.21.0' );
		update_post_meta( $page_id, '_elementor_template_type', 'page' );
		update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
	}

	update_option( 'guebel_setup_elementor_imported', '1' );
}

/**
 * Show admin notice after setup.
 */
function guebel_setup_admin_notice() {
	if ( ! get_option( 'guebel_setup_page_ids' ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'plugins' !== $screen->id ) {
		return;
	}

	echo '<div class="notice notice-success is-dismissible"><p>';
	echo '<strong>Guebel Setup:</strong> ';
	echo esc_html__( 'Páginas criadas com sucesso! Pode agora desactivar e apagar este plugin — as páginas ficam permanentemente.', 'guebel-setup' );
	echo '</p></div>';
}

add_action( 'admin_notices', 'guebel_setup_admin_notice' );
