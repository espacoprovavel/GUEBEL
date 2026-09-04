<?php
/**
 * Demo Content Installer.
 *
 * @package Guebel_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles demo content installation and removal.
 */
class Guebel_Demo_Content {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );
	}

	/**
	 * Add admin page under Tools.
	 */
	public function add_admin_page() {
		add_management_page(
			__( 'Guebel - Conteúdo Demo', 'guebel-core' ),
			__( 'Guebel Demo', 'guebel-core' ),
			'manage_options',
			'guebel-demo-content',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Handle install/remove actions.
	 */
	public function handle_actions() {
		if ( ! isset( $_POST['guebel_demo_action'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['guebel_demo_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['guebel_demo_nonce'] ) ), 'guebel_demo_content' ) ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_POST['guebel_demo_action'] ) );

		if ( 'install' === $action ) {
			$this->install_demo_content();
			add_settings_error(
				'guebel_demo',
				'demo_installed',
				__( 'Conteúdo demo instalado com sucesso!', 'guebel-core' ),
				'success'
			);
		} elseif ( 'remove' === $action ) {
			$this->remove_demo_content();
			add_settings_error(
				'guebel_demo',
				'demo_removed',
				__( 'Conteúdo demo removido com sucesso!', 'guebel-core' ),
				'success'
			);
		}

		set_transient( 'settings_errors', get_settings_errors(), 30 );
	}

	/**
	 * Render admin page.
	 */
	public function render_admin_page() {
		$is_installed = get_option( 'guebel_demo_content_installed', false );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Guebel - Conteúdo Demo', 'guebel-core' ); ?></h1>

			<?php settings_errors( 'guebel_demo' ); ?>

			<div class="card" style="max-width: 600px; padding: 20px;">
				<h2><?php esc_html_e( 'Gerir Conteúdo Demo', 'guebel-core' ); ?></h2>
				<p><?php esc_html_e( 'Instale conteúdo de demonstração para visualizar rapidamente como a sua loja ficará. Isto irá criar produtos, categorias, coleções, páginas e menus de exemplo.', 'guebel-core' ); ?></p>

				<?php if ( ! Guebel_Core::is_woocommerce_active() ) : ?>
					<div class="notice notice-warning inline" style="margin: 15px 0;">
						<p><?php esc_html_e( 'O WooCommerce não está ativo. Os produtos e categorias de demonstração não serão criados.', 'guebel-core' ); ?></p>
					</div>
				<?php endif; ?>

				<form method="post">
					<?php wp_nonce_field( 'guebel_demo_content', 'guebel_demo_nonce' ); ?>

					<?php if ( $is_installed ) : ?>
						<p style="color: #27ae60; font-weight: bold;">
							<?php esc_html_e( 'O conteúdo demo está instalado.', 'guebel-core' ); ?>
						</p>
						<button type="submit" name="guebel_demo_action" value="remove" class="button button-secondary" onclick="return confirm('<?php esc_attr_e( 'Tem a certeza que deseja remover todo o conteúdo demo?', 'guebel-core' ); ?>');">
							<?php esc_html_e( 'Remover Conteúdo Demo', 'guebel-core' ); ?>
						</button>
					<?php else : ?>
						<button type="submit" name="guebel_demo_action" value="install" class="button button-primary">
							<?php esc_html_e( 'Instalar Conteúdo Demo', 'guebel-core' ); ?>
						</button>
					<?php endif; ?>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Install all demo content.
	 */
	private function install_demo_content() {
		$demo_ids = array(
			'pages'       => array(),
			'products'    => array(),
			'categories'  => array(),
			'collections' => array(),
			'menus'       => array(),
		);

		$demo_ids['pages']       = $this->create_pages();
		$demo_ids['collections'] = $this->create_collections();

		if ( Guebel_Core::is_woocommerce_active() ) {
			$demo_ids['categories'] = $this->create_product_categories();
			$demo_ids['products']   = $this->create_products( $demo_ids['categories'] );
			$this->setup_woocommerce_pages();
		}

		$demo_ids['menus'] = $this->create_menus( $demo_ids );

		update_option( 'guebel_demo_content_installed', true );
		update_option( 'guebel_demo_content_ids', $demo_ids );
	}

	/**
	 * Remove all demo content.
	 */
	private function remove_demo_content() {
		$demo_ids = get_option( 'guebel_demo_content_ids', array() );

		// Remove pages.
		if ( ! empty( $demo_ids['pages'] ) ) {
			foreach ( $demo_ids['pages'] as $page_id ) {
				wp_delete_post( $page_id, true );
			}
		}

		// Remove products.
		if ( ! empty( $demo_ids['products'] ) ) {
			foreach ( $demo_ids['products'] as $product_id ) {
				wp_delete_post( $product_id, true );
			}
		}

		// Remove categories.
		if ( ! empty( $demo_ids['categories'] ) && Guebel_Core::is_woocommerce_active() ) {
			foreach ( $demo_ids['categories'] as $cat_id ) {
				wp_delete_term( $cat_id, 'product_cat' );
			}
		}

		// Remove collections.
		if ( ! empty( $demo_ids['collections'] ) ) {
			foreach ( $demo_ids['collections'] as $collection_id ) {
				wp_delete_post( $collection_id, true );
			}
		}

		// Remove menus.
		if ( ! empty( $demo_ids['menus'] ) ) {
			foreach ( $demo_ids['menus'] as $menu_id ) {
				wp_delete_nav_menu( $menu_id );
			}
		}

		delete_option( 'guebel_demo_content_installed' );
		delete_option( 'guebel_demo_content_ids' );
	}

	/**
	 * Create sample pages.
	 *
	 * @return array Page IDs.
	 */
	private function create_pages() {
		$pages = array(
			array(
				'title'   => 'Sobre',
				'content' => '<h2>A Nossa História</h2>
<p>A Guebel nasceu da paixão pela decoração e pelo design contemporâneo. Especializamo-nos em peças decorativas únicas, muitas delas produzidas com tecnologia de impressão 3D, combinando inovação com estética.</p>
<p>Cada peça é cuidadosamente desenhada e produzida em Portugal, com materiais de alta qualidade e processos sustentáveis.</p>
<h2>A Nossa Missão</h2>
<p>Transformar espaços comuns em ambientes extraordinários, oferecendo peças de decoração que aliam design moderno, sustentabilidade e acessibilidade.</p>
<h2>Os Nossos Valores</h2>
<ul>
<li><strong>Qualidade</strong> - Cada peça passa por rigoroso controlo de qualidade</li>
<li><strong>Sustentabilidade</strong> - Materiais eco-friendly e processos responsáveis</li>
<li><strong>Inovação</strong> - Tecnologia de ponta ao serviço do design</li>
<li><strong>Design Português</strong> - Orgulhosamente desenhado e produzido em Portugal</li>
</ul>',
				'slug'    => 'sobre',
			),
			array(
				'title'   => 'Contacto',
				'content' => '<h2>Entre em Contacto</h2>
<p>Estamos disponíveis para responder a todas as suas questões. Não hesite em contactar-nos através dos seguintes meios:</p>
<p><strong>Email:</strong> [guebel_email]</p>
<p><strong>Telefone:</strong> [guebel_phone]</p>
<p><strong>WhatsApp:</strong> [guebel_whatsapp]</p>
<p><strong>Morada:</strong> [guebel_address]</p>
<h3>Horário de Funcionamento</h3>
<p>Segunda a Sexta: 9h00 - 18h00<br>Sábado: 10h00 - 13h00<br>Domingo: Encerrado</p>',
				'slug'    => 'contacto',
			),
			array(
				'title'   => 'FAQ - Perguntas Frequentes',
				'content' => '<h2>Perguntas Frequentes</h2>
<h3>Quanto tempo demora a entrega?</h3>
<p>As encomendas são normalmente entregues em 3 a 5 dias úteis para Portugal Continental. Para as ilhas, o prazo pode ser de 5 a 8 dias úteis.</p>
<h3>Posso devolver um produto?</h3>
<p>Sim, tem 14 dias após a receção para solicitar a devolução. O produto deve estar nas condições originais e na embalagem original.</p>
<h3>Os produtos são realmente impressos em 3D?</h3>
<p>Sim! Muitos dos nossos produtos são fabricados com tecnologia de impressão 3D FDM ou resina, utilizando materiais de alta qualidade como PLA, PETG e resina fotopolimérica.</p>
<h3>Posso personalizar um produto?</h3>
<p>Alguns dos nossos produtos são personalizáveis. Procure o selo "Personalizável" na página do produto ou contacte-nos para soluções à medida.</p>
<h3>Quais são os métodos de pagamento aceites?</h3>
<p>Aceitamos Multibanco, MB Way, cartão de crédito/débito (Visa, Mastercard), PayPal e transferência bancária.</p>',
				'slug'    => 'faq',
			),
			array(
				'title'   => 'Blog',
				'content' => '<p>Bem-vindo ao nosso blog! Aqui partilhamos dicas de decoração, novidades sobre os nossos produtos e inspiração para transformar os seus espaços.</p>',
				'slug'    => 'blog',
			),
			array(
				'title'   => 'Política de Privacidade',
				'content' => '<h2>Política de Privacidade</h2>
<p>A Guebel compromete-se a proteger a privacidade dos seus clientes. Esta política descreve como recolhemos, utilizamos e protegemos os seus dados pessoais.</p>
<h3>Dados Recolhidos</h3>
<p>Recolhemos apenas os dados necessários para processar as suas encomendas: nome, email, morada de envio, número de telefone e dados de pagamento.</p>
<h3>Utilização dos Dados</h3>
<p>Os seus dados são utilizados exclusivamente para processar encomendas, comunicar sobre o estado das mesmas e, caso autorize, enviar newsletters com novidades e promoções.</p>
<h3>Proteção dos Dados</h3>
<p>Utilizamos encriptação SSL e seguimos as melhores práticas de segurança para proteger os seus dados. Nunca partilhamos os seus dados com terceiros para fins comerciais.</p>
<h3>Os Seus Direitos</h3>
<p>Nos termos do RGPD, tem direito a aceder, retificar, apagar e portar os seus dados pessoais. Para exercer estes direitos, contacte-nos.</p>',
				'slug'    => 'politica-de-privacidade',
			),
			array(
				'title'   => 'Termos e Condições',
				'content' => '<h2>Termos e Condições Gerais</h2>
<p>Ao utilizar o website e serviços da Guebel, aceita os presentes termos e condições.</p>
<h3>Identificação</h3>
<p>A Guebel é uma marca dedicada à comercialização de artigos de decoração premium, com sede em Portugal.</p>
<h3>Preços</h3>
<p>Todos os preços apresentados incluem IVA à taxa legal em vigor. A Guebel reserva-se o direito de alterar preços sem aviso prévio, não afetando encomendas já confirmadas.</p>
<h3>Encomendas</h3>
<p>A confirmação de encomenda é enviada por email após a receção do pagamento. Os prazos de entrega indicados são estimativas e podem variar.</p>',
				'slug'    => 'termos-e-condicoes',
			),
			array(
				'title'   => 'Política de Cookies',
				'content' => '<h2>Política de Cookies</h2>
<p>Este website utiliza cookies para melhorar a sua experiência de navegação.</p>
<h3>O que são cookies?</h3>
<p>Cookies são pequenos ficheiros de texto armazenados no seu dispositivo quando visita um website.</p>
<h3>Cookies utilizados</h3>
<ul>
<li><strong>Cookies essenciais</strong> - Necessários para o funcionamento do website (carrinho de compras, sessão)</li>
<li><strong>Cookies analíticos</strong> - Ajudam-nos a compreender como os visitantes utilizam o website</li>
<li><strong>Cookies de marketing</strong> - Utilizados para apresentar publicidade relevante</li>
</ul>
<h3>Gestão de cookies</h3>
<p>Pode gerir as suas preferências de cookies através das definições do seu navegador.</p>',
				'slug'    => 'politica-de-cookies',
			),
			array(
				'title'   => 'Entregas',
				'content' => '<h2>Informação sobre Entregas</h2>
<h3>Portugal Continental</h3>
<p>Entrega em 3-5 dias úteis. Portes grátis em encomendas superiores a 50,00 EUR.</p>
<h3>Ilhas (Açores e Madeira)</h3>
<p>Entrega em 5-8 dias úteis. Consulte os portes no checkout.</p>
<h3>Produtos Personalizados</h3>
<p>Produtos personalizados ou feitos por encomenda podem ter prazos de produção adicionais de 3-7 dias úteis.</p>
<h3>Seguimento</h3>
<p>Após o envio, receberá um email com o número de seguimento para acompanhar a sua encomenda.</p>',
				'slug'    => 'entregas',
			),
			array(
				'title'   => 'Trocas e Devoluções',
				'content' => '<h2>Política de Trocas e Devoluções</h2>
<h3>Direito de Devolução</h3>
<p>Tem 14 dias após a receção para solicitar a devolução de qualquer produto, sem necessidade de indicar motivo.</p>
<h3>Condições</h3>
<ul>
<li>O produto deve estar nas condições originais</li>
<li>Deve estar na embalagem original</li>
<li>Produtos personalizados não são elegíveis para devolução</li>
</ul>
<h3>Processo</h3>
<p>Contacte-nos por email ou telefone para iniciar o processo de devolução. O reembolso será processado no prazo de 14 dias após a receção do produto devolvido.</p>',
				'slug'    => 'trocas-e-devolucoes',
			),
			array(
				'title'   => 'Métodos de Pagamento',
				'content' => '<h2>Métodos de Pagamento</h2>
<p>Oferecemos vários métodos de pagamento seguros para a sua conveniência:</p>
<ul>
<li><strong>Multibanco</strong> - Referência gerada automaticamente, válida por 72 horas</li>
<li><strong>MB Way</strong> - Pagamento rápido e seguro pelo telemóvel</li>
<li><strong>Cartão de Crédito/Débito</strong> - Visa e Mastercard aceites</li>
<li><strong>PayPal</strong> - Para maior segurança e proteção ao comprador</li>
<li><strong>Transferência Bancária</strong> - Dados bancários enviados após a encomenda</li>
</ul>
<p>Todos os pagamentos são processados de forma segura com encriptação SSL.</p>',
				'slug'    => 'metodos-de-pagamento',
			),
		);

		$page_ids = array();
		foreach ( $pages as $page ) {
			$page_id = wp_insert_post(
				array(
					'post_title'   => $page['title'],
					'post_content' => $page['content'],
					'post_name'    => $page['slug'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_author'  => get_current_user_id(),
				)
			);
			if ( ! is_wp_error( $page_id ) ) {
				$page_ids[] = $page_id;
			}
		}

		return $page_ids;
	}

	/**
	 * Create sample collections.
	 *
	 * @return array Collection IDs.
	 */
	private function create_collections() {
		$collections = array(
			array(
				'title'   => 'Coleção Orgânica',
				'content' => 'Inspirada nas formas da natureza, a Coleção Orgânica traz peças com linhas fluídas e texturas que evocam o mundo natural. Cada peça é uma celebração da beleza orgânica, perfeita para espaços que valorizam a harmonia com a natureza.',
				'excerpt' => 'Formas fluídas inspiradas na natureza.',
			),
			array(
				'title'   => 'Coleção Geométrica',
				'content' => 'A Coleção Geométrica explora a beleza da matemática e da simetria. Peças com linhas precisas e ângulos calculados que transformam qualquer espaço num ambiente contemporâneo e sofisticado.',
				'excerpt' => 'Design geométrico contemporâneo.',
			),
			array(
				'title'   => 'Coleção Minimalista',
				'content' => 'Menos é mais. A Coleção Minimalista apresenta peças de design limpo e funcional, onde cada detalhe foi cuidadosamente pensado para criar harmonia e equilíbrio no seu espaço.',
				'excerpt' => 'Simplicidade elegante para o seu espaço.',
			),
		);

		$collection_ids = array();
		foreach ( $collections as $collection ) {
			$post_id = wp_insert_post(
				array(
					'post_title'   => $collection['title'],
					'post_content' => $collection['content'],
					'post_excerpt' => $collection['excerpt'],
					'post_status'  => 'publish',
					'post_type'    => 'guebel_collection',
					'post_author'  => get_current_user_id(),
				)
			);
			if ( ! is_wp_error( $post_id ) ) {
				$collection_ids[] = $post_id;
			}
		}

		return $collection_ids;
	}

	/**
	 * Create product categories.
	 *
	 * @return array Category IDs.
	 */
	private function create_product_categories() {
		$categories = array(
			'Vasos'               => 'vasos',
			'Objetos Decorativos' => 'objetos-decorativos',
			'Esculturas'          => 'esculturas',
			'Luminárias'          => 'luminarias',
			'Decoração 3D'        => 'decoracao-3d',
			'Acessórios'          => 'acessorios',
		);

		$cat_ids = array();
		foreach ( $categories as $name => $slug ) {
			$term = wp_insert_term(
				$name,
				'product_cat',
				array( 'slug' => $slug )
			);
			if ( ! is_wp_error( $term ) ) {
				$cat_ids[] = $term['term_id'];
			}
		}

		return $cat_ids;
	}

	/**
	 * Create sample products.
	 *
	 * @param array $category_ids Category IDs.
	 * @return array Product IDs.
	 */
	private function create_products( $category_ids ) {
		$products = array(
			array(
				'title'       => 'Vaso Espiral Nórdico',
				'description' => 'Vaso decorativo com design espiral inspirado na estética nórdica. Impresso em 3D com PLA de alta qualidade, este vaso traz um toque de modernidade e elegância a qualquer divisão. Perfeito para flores secas ou como peça decorativa autónoma.',
				'short_desc'  => 'Vaso espiral impresso em 3D com design nórdico.',
				'price'       => '29.90',
				'sale_price'  => '',
				'sku'         => 'GBL-VS-001',
				'cat_index'   => 0,
				'meta'        => array(
					'_guebel_is_3d_printed'   => 'yes',
					'_guebel_production_time' => '3-5 dias úteis',
					'_guebel_customizable'    => 'yes',
					'_guebel_care_instructions' => 'Limpar com pano húmido. Evitar exposição solar direta prolongada.',
				),
			),
			array(
				'title'       => 'Escultura Ondas do Mar',
				'description' => 'Escultura decorativa que captura o movimento das ondas do oceano. Cada peça é única, com variações subtis que a tornam verdadeiramente especial. Fabricada em resina de alta resistência com acabamento matte.',
				'short_desc'  => 'Escultura abstrata inspirada no oceano.',
				'price'       => '45.00',
				'sale_price'  => '39.90',
				'sku'         => 'GBL-ES-001',
				'cat_index'   => 2,
				'meta'        => array(
					'_guebel_is_3d_printed'      => 'yes',
					'_guebel_production_time'    => '5-7 dias úteis',
					'_guebel_sustainability_info' => 'Produzida com resina eco-friendly e processos de baixo desperdício.',
					'_guebel_dimensions_detail'  => 'Largura: 20cm | Altura: 15cm | Profundidade: 10cm | Peso: 350g',
				),
			),
			array(
				'title'       => 'Luminária Lua Crescente',
				'description' => 'Luminária decorativa em forma de lua crescente que cria uma atmosfera acolhedora e mágica. Com luz LED integrada de intensidade regulável, é perfeita para mesas de cabeceira ou como iluminação ambiente. Disponível em branco e cinza.',
				'short_desc'  => 'Luminária LED em forma de lua com intensidade regulável.',
				'price'       => '59.90',
				'sale_price'  => '',
				'sku'         => 'GBL-LM-001',
				'cat_index'   => 3,
				'meta'        => array(
					'_guebel_is_3d_printed'     => 'yes',
					'_guebel_production_time'   => '5-7 dias úteis',
					'_guebel_customizable'      => 'yes',
					'_guebel_care_instructions' => 'Não submergir em água. Limpar apenas com pano seco. Utilizar apenas com transformador fornecido.',
				),
			),
			array(
				'title'       => 'Porta-Velas Geométrico',
				'description' => 'Conjunto de três porta-velas com formas geométricas distintas - cubo, esfera e cilindro. Desenhados para criar uma composição harmoniosa na sua mesa ou aparador. Acabamento em betão polido.',
				'short_desc'  => 'Conjunto de 3 porta-velas geométricos em betão.',
				'price'       => '34.50',
				'sale_price'  => '28.90',
				'sku'         => 'GBL-OD-001',
				'cat_index'   => 1,
				'meta'        => array(
					'_guebel_is_3d_printed'      => 'no',
					'_guebel_production_time'    => '2-3 dias úteis',
					'_guebel_sustainability_info' => 'Fabricado com betão reciclado e pigmentos naturais.',
					'_guebel_dimensions_detail'  => 'Cubo: 8x8x8cm | Esfera: Ø9cm | Cilindro: Ø7x10cm',
				),
			),
			array(
				'title'       => 'Painel Decorativo 3D Hexagonal',
				'description' => 'Painel decorativo modular composto por peças hexagonais que podem ser dispostas de múltiplas formas. Cada kit inclui 12 peças que se fixam facilmente à parede. Crie padrões únicos e personalize o seu espaço.',
				'short_desc'  => 'Kit de 12 peças hexagonais para decoração de parede.',
				'price'       => '49.90',
				'sale_price'  => '',
				'sku'         => 'GBL-3D-001',
				'cat_index'   => 4,
				'meta'        => array(
					'_guebel_is_3d_printed'     => 'yes',
					'_guebel_production_time'   => '5-7 dias úteis',
					'_guebel_customizable'      => 'yes',
					'_guebel_care_instructions' => 'Fixar com fita adesiva dupla face (incluída). Limpar com pano seco.',
				),
			),
			array(
				'title'       => 'Vaso Minimalista Cilíndrico',
				'description' => 'Vaso cilíndrico de linhas limpas e design minimalista. A sua simplicidade elegante permite que as flores sejam as protagonistas. Disponível em três tamanhos e várias cores. Interior impermeável para flores frescas.',
				'short_desc'  => 'Vaso cilíndrico minimalista com interior impermeável.',
				'price'       => '22.50',
				'sale_price'  => '',
				'sku'         => 'GBL-VS-002',
				'cat_index'   => 0,
				'meta'        => array(
					'_guebel_is_3d_printed'      => 'yes',
					'_guebel_production_time'    => '3-5 dias úteis',
					'_guebel_sustainability_info' => 'Impresso com PLA biodegradável derivado de amido de milho.',
					'_guebel_dimensions_detail'  => 'Pequeno: Ø8x12cm | Médio: Ø10x18cm | Grande: Ø12x24cm',
				),
			),
			array(
				'title'       => 'Organizador de Secretária Modular',
				'description' => 'Sistema modular de organização para secretária com compartimentos para canetas, clips, cartões e telemóvel. Design contemporâneo que mantém o seu espaço de trabalho arrumado com estilo. Peças encaixáveis que se adaptam às suas necessidades.',
				'short_desc'  => 'Organizador modular para secretária com design contemporâneo.',
				'price'       => '19.90',
				'sale_price'  => '15.90',
				'sku'         => 'GBL-AC-001',
				'cat_index'   => 5,
				'meta'        => array(
					'_guebel_is_3d_printed'     => 'yes',
					'_guebel_production_time'   => '3-5 dias úteis',
					'_guebel_customizable'      => 'yes',
					'_guebel_care_instructions' => 'Limpar com pano húmido. Material resistente a impactos.',
				),
			),
			array(
				'title'       => 'Escultura Abstrata Torção',
				'description' => 'Peça escultórica que explora o conceito de torção e movimento. Uma adição sofisticada para qualquer espaço, esta escultura funciona como ponto focal numa estante, mesa de centro ou aparador. Acabamento glossy em branco pérola.',
				'short_desc'  => 'Escultura abstrata com acabamento glossy.',
				'price'       => '55.00',
				'sale_price'  => '',
				'sku'         => 'GBL-ES-002',
				'cat_index'   => 2,
				'meta'        => array(
					'_guebel_is_3d_printed'     => 'yes',
					'_guebel_production_time'   => '5-7 dias úteis',
					'_guebel_dimensions_detail' => 'Largura: 12cm | Altura: 25cm | Profundidade: 12cm | Peso: 280g',
				),
			),
		);

		$product_ids = array();

		// Prefer the WooCommerce CRUD API so products are fully registered
		// (price/visibility lookup tables populated) and appear in the shop.
		$use_crud = class_exists( 'WC_Product_Simple' );

		foreach ( $products as $product_data ) {
			if ( $use_crud ) {
				$product = new WC_Product_Simple();
				$product->set_name( $product_data['title'] );
				$product->set_status( 'publish' );
				$product->set_catalog_visibility( 'visible' );
				$product->set_description( $product_data['description'] );
				$product->set_short_description( $product_data['short_desc'] );
				$product->set_sku( $product_data['sku'] );
				$product->set_regular_price( $product_data['price'] );
				if ( ! empty( $product_data['sale_price'] ) ) {
					$product->set_sale_price( $product_data['sale_price'] );
				}
				$product->set_manage_stock( false );
				$product->set_stock_status( 'instock' );

				if ( isset( $category_ids[ $product_data['cat_index'] ] ) ) {
					$product->set_category_ids( array( (int) $category_ids[ $product_data['cat_index'] ] ) );
				}

				$product_id = $product->save();

				if ( ! $product_id ) {
					continue;
				}
			} else {
				$product_id = wp_insert_post(
					array(
						'post_title'   => $product_data['title'],
						'post_content' => $product_data['description'],
						'post_excerpt' => $product_data['short_desc'],
						'post_status'  => 'publish',
						'post_type'    => 'product',
						'post_author'  => get_current_user_id(),
					)
				);

				if ( is_wp_error( $product_id ) ) {
					continue;
				}

				wp_set_object_terms( $product_id, 'simple', 'product_type' );
				if ( isset( $category_ids[ $product_data['cat_index'] ] ) ) {
					wp_set_object_terms( $product_id, array( (int) $category_ids[ $product_data['cat_index'] ] ), 'product_cat' );
				}
				update_post_meta( $product_id, '_regular_price', $product_data['price'] );
				update_post_meta( $product_id, '_price', ! empty( $product_data['sale_price'] ) ? $product_data['sale_price'] : $product_data['price'] );
				if ( ! empty( $product_data['sale_price'] ) ) {
					update_post_meta( $product_id, '_sale_price', $product_data['sale_price'] );
				}
				update_post_meta( $product_id, '_sku', $product_data['sku'] );
				update_post_meta( $product_id, '_stock_status', 'instock' );
				update_post_meta( $product_id, '_manage_stock', 'no' );
			}

			// Set Guebel custom meta (product feature fields).
			if ( ! empty( $product_data['meta'] ) ) {
				foreach ( $product_data['meta'] as $meta_key => $meta_value ) {
					update_post_meta( $product_id, $meta_key, $meta_value );
				}
			}

			$product_ids[] = $product_id;
		}

		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients();
		}

		return $product_ids;
	}

	/**
	 * Set up WooCommerce pages.
	 */
	private function setup_woocommerce_pages() {
		$wc_pages = array(
			'shop'       => array(
				'name'  => _x( 'Loja', 'Page slug', 'guebel-core' ),
				'title' => _x( 'Loja', 'Page title', 'guebel-core' ),
			),
			'cart'       => array(
				'name'  => _x( 'carrinho', 'Page slug', 'guebel-core' ),
				'title' => _x( 'Carrinho', 'Page title', 'guebel-core' ),
			),
			'checkout'   => array(
				'name'  => _x( 'finalizar-compra', 'Page slug', 'guebel-core' ),
				'title' => _x( 'Finalizar Compra', 'Page title', 'guebel-core' ),
			),
			'myaccount'  => array(
				'name'  => _x( 'minha-conta', 'Page slug', 'guebel-core' ),
				'title' => _x( 'A Minha Conta', 'Page title', 'guebel-core' ),
			),
		);

		foreach ( $wc_pages as $key => $page_data ) {
			$option_key = 'woocommerce_' . $key . '_page_id';
			$page_id    = get_option( $option_key );

			// Only create if page does not exist.
			if ( empty( $page_id ) || ! get_post( $page_id ) ) {
				$page_content = '';
				if ( 'cart' === $key ) {
					$page_content = '[woocommerce_cart]';
				} elseif ( 'checkout' === $key ) {
					$page_content = '[woocommerce_checkout]';
				} elseif ( 'myaccount' === $key ) {
					$page_content = '[woocommerce_my_account]';
				}

				$new_page_id = wp_insert_post(
					array(
						'post_title'   => $page_data['title'],
						'post_name'    => $page_data['name'],
						'post_content' => $page_content,
						'post_status'  => 'publish',
						'post_type'    => 'page',
						'post_author'  => get_current_user_id(),
					)
				);

				if ( ! is_wp_error( $new_page_id ) ) {
					update_option( $option_key, $new_page_id );
				}
			}
		}
	}

	/**
	 * Create navigation menus.
	 *
	 * @param array $demo_ids All created demo content IDs.
	 * @return array Menu IDs.
	 */
	private function create_menus( $demo_ids ) {
		$menu_ids = array();

		// Primary Menu.
		$primary_menu_id = wp_create_nav_menu( __( 'Menu Principal', 'guebel-core' ) );
		if ( ! is_wp_error( $primary_menu_id ) ) {
			$menu_ids[] = $primary_menu_id;

			wp_update_nav_menu_item(
				$primary_menu_id,
				0,
				array(
					'menu-item-title'  => __( 'Início', 'guebel-core' ),
					'menu-item-url'    => home_url( '/' ),
					'menu-item-status' => 'publish',
					'menu-item-type'   => 'custom',
				)
			);

			if ( Guebel_Core::is_woocommerce_active() ) {
				$shop_page_id = get_option( 'woocommerce_shop_page_id' );
				if ( $shop_page_id ) {
					wp_update_nav_menu_item(
						$primary_menu_id,
						0,
						array(
							'menu-item-title'     => __( 'Loja', 'guebel-core' ),
							'menu-item-object'    => 'page',
							'menu-item-object-id' => $shop_page_id,
							'menu-item-type'      => 'post_type',
							'menu-item-status'    => 'publish',
						)
					);
				}
			}

			wp_update_nav_menu_item(
				$primary_menu_id,
				0,
				array(
					'menu-item-title'  => __( 'Coleções', 'guebel-core' ),
					'menu-item-url'    => home_url( '/colecoes/' ),
					'menu-item-status' => 'publish',
					'menu-item-type'   => 'custom',
				)
			);

			// Add pages to menu.
			$menu_pages = array( 'Sobre', 'Blog', 'Contacto' );
			if ( ! empty( $demo_ids['pages'] ) ) {
				foreach ( $demo_ids['pages'] as $page_id ) {
					$page = get_post( $page_id );
					if ( $page && in_array( $page->post_title, $menu_pages, true ) ) {
						wp_update_nav_menu_item(
							$primary_menu_id,
							0,
							array(
								'menu-item-title'     => $page->post_title,
								'menu-item-object'    => 'page',
								'menu-item-object-id' => $page_id,
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
							)
						);
					}
				}
			}

			// Assign to theme location if available.
			$locations = get_theme_mod( 'nav_menu_locations', array() );
			$locations['primary'] = $primary_menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}

		// Footer Menu.
		$footer_menu_id = wp_create_nav_menu( __( 'Menu Rodapé', 'guebel-core' ) );
		if ( ! is_wp_error( $footer_menu_id ) ) {
			$menu_ids[] = $footer_menu_id;

			$footer_pages = array(
				'Política de Privacidade',
				'Termos e Condições',
				'Política de Cookies',
				'Entregas',
				'Trocas e Devoluções',
				'Métodos de Pagamento',
				'FAQ - Perguntas Frequentes',
			);

			if ( ! empty( $demo_ids['pages'] ) ) {
				foreach ( $demo_ids['pages'] as $page_id ) {
					$page = get_post( $page_id );
					if ( $page && in_array( $page->post_title, $footer_pages, true ) ) {
						wp_update_nav_menu_item(
							$footer_menu_id,
							0,
							array(
								'menu-item-title'     => $page->post_title,
								'menu-item-object'    => 'page',
								'menu-item-object-id' => $page_id,
								'menu-item-type'      => 'post_type',
								'menu-item-status'    => 'publish',
							)
						);
					}
				}
			}

			$locations = get_theme_mod( 'nav_menu_locations', array() );
			$locations['footer'] = $footer_menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}

		// Categories Menu.
		$cat_menu_id = wp_create_nav_menu( __( 'Menu Categorias', 'guebel-core' ) );
		if ( ! is_wp_error( $cat_menu_id ) ) {
			$menu_ids[] = $cat_menu_id;

			if ( Guebel_Core::is_woocommerce_active() && ! empty( $demo_ids['categories'] ) ) {
				foreach ( $demo_ids['categories'] as $cat_id ) {
					$term = get_term( $cat_id, 'product_cat' );
					if ( $term && ! is_wp_error( $term ) ) {
						wp_update_nav_menu_item(
							$cat_menu_id,
							0,
							array(
								'menu-item-title'     => $term->name,
								'menu-item-object'    => 'product_cat',
								'menu-item-object-id' => $cat_id,
								'menu-item-type'      => 'taxonomy',
								'menu-item-status'    => 'publish',
							)
						);
					}
				}
			}

			$locations = get_theme_mod( 'nav_menu_locations', array() );
			$locations['categories'] = $cat_menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}

		return $menu_ids;
	}
}
