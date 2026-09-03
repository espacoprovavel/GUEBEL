<?php
/**
 * Custom Post Types and Taxonomies.
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers custom post types and taxonomies.
 */
class Guebel_Custom_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
	}

	/**
	 * Register custom post types.
	 */
	public function register_post_types() {
		$this->register_collection_post_type();
		$this->register_testimonial_post_type();
	}

	/**
	 * Register the Collection post type.
	 */
	private function register_collection_post_type() {
		$labels = array(
			'name'                  => __( 'Coleções', 'guebel-core' ),
			'singular_name'         => __( 'Coleção', 'guebel-core' ),
			'menu_name'             => __( 'Coleções', 'guebel-core' ),
			'name_admin_bar'        => __( 'Coleção', 'guebel-core' ),
			'add_new'               => __( 'Adicionar Nova', 'guebel-core' ),
			'add_new_item'          => __( 'Adicionar Nova Coleção', 'guebel-core' ),
			'new_item'              => __( 'Nova Coleção', 'guebel-core' ),
			'edit_item'             => __( 'Editar Coleção', 'guebel-core' ),
			'view_item'             => __( 'Ver Coleção', 'guebel-core' ),
			'all_items'             => __( 'Todas as Coleções', 'guebel-core' ),
			'search_items'          => __( 'Pesquisar Coleções', 'guebel-core' ),
			'parent_item_colon'     => __( 'Coleção Superior:', 'guebel-core' ),
			'not_found'             => __( 'Nenhuma coleção encontrada.', 'guebel-core' ),
			'not_found_in_trash'    => __( 'Nenhuma coleção no lixo.', 'guebel-core' ),
			'featured_image'        => __( 'Imagem da Coleção', 'guebel-core' ),
			'set_featured_image'    => __( 'Definir imagem da coleção', 'guebel-core' ),
			'remove_featured_image' => __( 'Remover imagem da coleção', 'guebel-core' ),
			'use_featured_image'    => __( 'Usar como imagem da coleção', 'guebel-core' ),
			'archives'              => __( 'Arquivo de Coleções', 'guebel-core' ),
			'insert_into_item'      => __( 'Inserir na coleção', 'guebel-core' ),
			'uploaded_to_this_item' => __( 'Carregado para esta coleção', 'guebel-core' ),
			'filter_items_list'     => __( 'Filtrar lista de coleções', 'guebel-core' ),
			'items_list_navigation' => __( 'Navegação da lista de coleções', 'guebel-core' ),
			'items_list'            => __( 'Lista de coleções', 'guebel-core' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'colecoes' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-images-alt2',
			'show_in_rest'       => true,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		);

		register_post_type( 'guebel_collection', $args );
	}

	/**
	 * Register the Testimonial post type.
	 */
	private function register_testimonial_post_type() {
		$labels = array(
			'name'                  => __( 'Testemunhos', 'guebel-core' ),
			'singular_name'         => __( 'Testemunho', 'guebel-core' ),
			'menu_name'             => __( 'Testemunhos', 'guebel-core' ),
			'name_admin_bar'        => __( 'Testemunho', 'guebel-core' ),
			'add_new'               => __( 'Adicionar Novo', 'guebel-core' ),
			'add_new_item'          => __( 'Adicionar Novo Testemunho', 'guebel-core' ),
			'new_item'              => __( 'Novo Testemunho', 'guebel-core' ),
			'edit_item'             => __( 'Editar Testemunho', 'guebel-core' ),
			'view_item'             => __( 'Ver Testemunho', 'guebel-core' ),
			'all_items'             => __( 'Todos os Testemunhos', 'guebel-core' ),
			'search_items'          => __( 'Pesquisar Testemunhos', 'guebel-core' ),
			'not_found'             => __( 'Nenhum testemunho encontrado.', 'guebel-core' ),
			'not_found_in_trash'    => __( 'Nenhum testemunho no lixo.', 'guebel-core' ),
			'featured_image'        => __( 'Foto do Cliente', 'guebel-core' ),
			'set_featured_image'    => __( 'Definir foto do cliente', 'guebel-core' ),
			'remove_featured_image' => __( 'Remover foto do cliente', 'guebel-core' ),
			'use_featured_image'    => __( 'Usar como foto do cliente', 'guebel-core' ),
			'archives'              => __( 'Arquivo de Testemunhos', 'guebel-core' ),
			'insert_into_item'      => __( 'Inserir no testemunho', 'guebel-core' ),
			'uploaded_to_this_item' => __( 'Carregado para este testemunho', 'guebel-core' ),
			'filter_items_list'     => __( 'Filtrar lista de testemunhos', 'guebel-core' ),
			'items_list_navigation' => __( 'Navegação da lista de testemunhos', 'guebel-core' ),
			'items_list'            => __( 'Lista de testemunhos', 'guebel-core' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'testemunhos' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 21,
			'menu_icon'          => 'dashicons-format-quote',
			'show_in_rest'       => true,
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
		);

		register_post_type( 'guebel_testimonial', $args );
	}

	/**
	 * Register custom taxonomies.
	 */
	public function register_taxonomies() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		$this->register_material_taxonomy();
		$this->register_finish_taxonomy();
	}

	/**
	 * Register Materials taxonomy.
	 */
	private function register_material_taxonomy() {
		$labels = array(
			'name'                       => __( 'Materiais', 'guebel-core' ),
			'singular_name'              => __( 'Material', 'guebel-core' ),
			'search_items'               => __( 'Pesquisar Materiais', 'guebel-core' ),
			'popular_items'              => __( 'Materiais Populares', 'guebel-core' ),
			'all_items'                  => __( 'Todos os Materiais', 'guebel-core' ),
			'parent_item'               => __( 'Material Superior', 'guebel-core' ),
			'parent_item_colon'          => __( 'Material Superior:', 'guebel-core' ),
			'edit_item'                  => __( 'Editar Material', 'guebel-core' ),
			'view_item'                  => __( 'Ver Material', 'guebel-core' ),
			'update_item'                => __( 'Atualizar Material', 'guebel-core' ),
			'add_new_item'               => __( 'Adicionar Novo Material', 'guebel-core' ),
			'new_item_name'              => __( 'Nome do Novo Material', 'guebel-core' ),
			'separate_items_with_commas' => __( 'Separar materiais com vírgulas', 'guebel-core' ),
			'add_or_remove_items'        => __( 'Adicionar ou remover materiais', 'guebel-core' ),
			'choose_from_most_used'      => __( 'Escolher dos mais usados', 'guebel-core' ),
			'not_found'                  => __( 'Nenhum material encontrado.', 'guebel-core' ),
			'no_terms'                   => __( 'Sem materiais', 'guebel-core' ),
			'menu_name'                  => __( 'Materiais', 'guebel-core' ),
			'items_list_navigation'      => __( 'Navegação da lista de materiais', 'guebel-core' ),
			'items_list'                 => __( 'Lista de materiais', 'guebel-core' ),
			'back_to_items'              => __( 'Voltar aos Materiais', 'guebel-core' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'material' ),
		);

		register_taxonomy( 'guebel_material', array( 'product' ), $args );
	}

	/**
	 * Register Finishes taxonomy.
	 */
	private function register_finish_taxonomy() {
		$labels = array(
			'name'                       => __( 'Acabamentos', 'guebel-core' ),
			'singular_name'              => __( 'Acabamento', 'guebel-core' ),
			'search_items'               => __( 'Pesquisar Acabamentos', 'guebel-core' ),
			'popular_items'              => __( 'Acabamentos Populares', 'guebel-core' ),
			'all_items'                  => __( 'Todos os Acabamentos', 'guebel-core' ),
			'parent_item'               => __( 'Acabamento Superior', 'guebel-core' ),
			'parent_item_colon'          => __( 'Acabamento Superior:', 'guebel-core' ),
			'edit_item'                  => __( 'Editar Acabamento', 'guebel-core' ),
			'view_item'                  => __( 'Ver Acabamento', 'guebel-core' ),
			'update_item'                => __( 'Atualizar Acabamento', 'guebel-core' ),
			'add_new_item'               => __( 'Adicionar Novo Acabamento', 'guebel-core' ),
			'new_item_name'              => __( 'Nome do Novo Acabamento', 'guebel-core' ),
			'separate_items_with_commas' => __( 'Separar acabamentos com vírgulas', 'guebel-core' ),
			'add_or_remove_items'        => __( 'Adicionar ou remover acabamentos', 'guebel-core' ),
			'choose_from_most_used'      => __( 'Escolher dos mais usados', 'guebel-core' ),
			'not_found'                  => __( 'Nenhum acabamento encontrado.', 'guebel-core' ),
			'no_terms'                   => __( 'Sem acabamentos', 'guebel-core' ),
			'menu_name'                  => __( 'Acabamentos', 'guebel-core' ),
			'items_list_navigation'      => __( 'Navegação da lista de acabamentos', 'guebel-core' ),
			'items_list'                 => __( 'Lista de acabamentos', 'guebel-core' ),
			'back_to_items'              => __( 'Voltar aos Acabamentos', 'guebel-core' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'acabamento' ),
		);

		register_taxonomy( 'guebel_finish', array( 'product' ), $args );
	}
}
