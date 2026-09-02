<?php
/**
 * Guebel — funções do tema.
 *
 * @package Guebel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Acesso directo proibido.
}

define( 'GUEBEL_VERSION', '1.0.0' );

/**
 * Configuração do tema.
 */
function guebel_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );

	register_nav_menus(
		array(
			'primary' => __( 'Menu principal', 'guebel' ),
			'footer'  => __( 'Menu do rodapé', 'guebel' ),
		)
	);
}
add_action( 'after_setup_theme', 'guebel_setup' );

/**
 * Estilos e scripts.
 */
function guebel_assets() {
	// Google Fonts: Fraunces (títulos) + Work Sans (texto).
	wp_enqueue_style(
		'guebel-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;1,9..144,300;1,9..144,400&family=Work+Sans:wght@300;400;500&display=swap',
		array(),
		null
	);

	// Folha de estilos principal do tema (style.css na raiz).
	wp_enqueue_style(
		'guebel-style',
		get_stylesheet_uri(),
		array( 'guebel-fonts' ),
		GUEBEL_VERSION
	);

	// JS do tema (menu mobile + contador do cesto).
	wp_enqueue_script(
		'guebel-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		GUEBEL_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'guebel_assets' );

/**
 * Menu de recurso quando não há menu atribuído no WordPress.
 */
function guebel_fallback_menu() {
	?>
	<nav class="site-nav">
		<a href="<?php echo esc_url( guebel_shop_url() ); ?>" class="wide-sm"><?php esc_html_e( 'Loja', 'guebel' ); ?></a>
		<a href="#sobre" class="wide-sm"><?php esc_html_e( 'Sobre', 'guebel' ); ?></a>
		<a href="#clube" class="wide-sm"><?php esc_html_e( 'Clube', 'guebel' ); ?></a>
		<a href="#contacto" class="wide-sm"><?php esc_html_e( 'Contacto', 'guebel' ); ?></a>
	</nav>
	<?php
}

/**
 * ---------------------------------------------------------------------------
 * WooCommerce
 * ---------------------------------------------------------------------------
 */

/**
 * Suporte a WooCommerce.
 */
function guebel_woocommerce_setup() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'guebel_woocommerce_setup' );

/**
 * WooCommerce está activo?
 *
 * @return bool
 */
function guebel_has_woocommerce() {
	return class_exists( 'WooCommerce' );
}

/**
 * Número de artigos no cesto.
 *
 * @return int
 */
function guebel_cart_count() {
	if ( ! guebel_has_woocommerce() || is_null( WC()->cart ) ) {
		return 0;
	}
	return (int) WC()->cart->get_cart_contents_count();
}

/**
 * Markup do contador do cesto (usado também como fragmento AJAX).
 */
function guebel_cart_count_markup() {
	?>
	<span class="cart-count" data-cart-count><?php echo esc_html( guebel_cart_count() ); ?></span>
	<?php
}

/**
 * Actualiza o contador sem recarregar a página.
 *
 * @param array $fragments Fragmentos.
 * @return array
 */
function guebel_cart_fragment( $fragments ) {
	ob_start();
	guebel_cart_count_markup();
	$fragments['span.cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'guebel_cart_fragment' );

/**
 * URL do cesto (ou âncora da loja quando o WooCommerce não está activo).
 *
 * @return string
 */
function guebel_cart_url() {
	return guebel_has_woocommerce() ? wc_get_cart_url() : '#loja';
}

// Envolver o conteúdo WooCommerce no layout do tema.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * Abertura do wrapper.
 */
function guebel_wc_wrapper_start() {
	echo '<section class="shop container woocommerce-page-wrap">';
}
add_action( 'woocommerce_before_main_content', 'guebel_wc_wrapper_start', 10 );

/**
 * Fecho do wrapper.
 */
function guebel_wc_wrapper_end() {
	echo '</section>';
}
add_action( 'woocommerce_after_main_content', 'guebel_wc_wrapper_end', 10 );

/**
 * Produtos por linha na loja.
 *
 * @return int
 */
function guebel_wc_columns() {
	return 3;
}
add_filter( 'loop_shop_columns', 'guebel_wc_columns' );

/**
 * Classe dos botões WooCommerce para seguir o estilo do tema.
 *
 * @param array $args Argumentos do botão.
 * @return array
 */
function guebel_wc_add_to_cart_args( $args ) {
	$args['class'] = trim( str_replace( 'button', '', isset( $args['class'] ) ? $args['class'] : '' ) . ' rule-btn' );
	return $args;
}
add_filter( 'woocommerce_loop_add_to_cart_args', 'guebel_wc_add_to_cart_args' );

/**
 * URL da loja (ou âncora dos destaques quando o WooCommerce não está activo).
 *
 * @return string
 */
function guebel_shop_url() {
	if ( guebel_has_woocommerce() ) {
		$shop = wc_get_page_permalink( 'shop' );
		if ( $shop ) {
			return $shop;
		}
	}
	return home_url( '/#loja' );
}

/**
 * Marca o body quando o WooCommerce está activo.
 *
 * @param array $classes Classes.
 * @return array
 */
function guebel_body_class( $classes ) {
	if ( guebel_has_woocommerce() ) {
		$classes[] = 'woocommerce-active';
	}
	return $classes;
}
add_filter( 'body_class', 'guebel_body_class' );

/**
 * ---------------------------------------------------------------------------
 * Elementor
 * ---------------------------------------------------------------------------
 */

/**
 * Suporte a Elementor (largura do conteúdo, locations e Theme Builder).
 */
function guebel_elementor_setup() {
	// Largura base do conteúdo usada pelo Elementor.
	if ( ! isset( $GLOBALS['content_width'] ) ) {
		$GLOBALS['content_width'] = 1200;
	}

	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );

	// Necessário para o Elementor Pro Theme Builder (cabeçalho/rodapé/single/archive).
	add_theme_support( 'elementor' );
	add_theme_support( 'elementor-pro' );
}
add_action( 'after_setup_theme', 'guebel_elementor_setup', 11 );

/**
 * Regista as Theme Locations do Elementor Pro.
 *
 * @param object $manager Gestor de locations.
 */
function guebel_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'guebel_elementor_locations' );

/**
 * O Elementor está activo?
 *
 * @return bool
 */
function guebel_has_elementor() {
	return did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
}

/**
 * O conteúdo actual foi construído com o Elementor?
 *
 * @param int|null $post_id ID do conteúdo.
 * @return bool
 */
function guebel_is_elementor_content( $post_id = null ) {
	if ( ! guebel_has_elementor() ) {
		return false;
	}
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id ) {
		return false;
	}
	return \Elementor\Plugin::$instance->documents->get( $post_id )
		&& \Elementor\Plugin::$instance->documents->get( $post_id )->is_built_with_elementor();
}

/**
 * Cabeçalho do tema, a não ser que o Elementor Pro tenha um cabeçalho próprio.
 */
function guebel_do_header() {
	if ( guebel_has_elementor() && class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
		if ( \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->do_location( 'header' ) ) {
			return;
		}
	}
	get_template_part( 'template-parts/header', 'default' );
}

/**
 * Rodapé do tema, a não ser que o Elementor Pro tenha um rodapé próprio.
 */
function guebel_do_footer() {
	if ( guebel_has_elementor() && class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
		if ( \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->do_location( 'footer' ) ) {
			return;
		}
	}
	get_template_part( 'template-parts/footer', 'default' );
}
