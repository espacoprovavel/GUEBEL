<?php
/**
 * Página inicial — loja Guebel
 *
 * @package Guebel
 */

$img = get_template_directory_uri() . '/assets/img';

$products = array(
	array(
		'name'  => __( 'Conjunto Vibrante', 'guebel' ),
		'price' => '189,99€',
		'sale'  => '',
		'img'   => $img . '/p1.jpg',
		'tag'   => __( 'Novo', 'guebel' ),
	),
	array(
		'name'  => __( 'Vulcano', 'guebel' ),
		'price' => '49,99€',
		'sale'  => '',
		'img'   => $img . '/p2.jpg',
		'tag'   => '',
	),
	array(
		'name'  => __( 'Garrafa Larga', 'guebel' ),
		'price' => '34,99€',
		'sale'  => '29,99€',
		'img'   => $img . '/p3.jpg',
		'tag'   => __( 'Promoção', 'guebel' ),
	),
);

get_header();

// Se a página inicial for uma página construída com o Elementor, mostra esse conteúdo.
if ( ! is_home() && guebel_is_elementor_content( get_queried_object_id() ) ) {
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	get_footer();
	return;
}
?>

<!-- Hero -->
<section class="hero">
	<img src="<?php echo esc_url( $img . '/hero.jpg' ); ?>" alt="<?php esc_attr_e( 'Vasos coloridos impressos em 3D com plantas', 'guebel' ); ?>" width="1920" height="1088" fetchpriority="high">
	<div class="hero-overlay">
		<div class="container">
			<h1 class="hero-title"><?php esc_html_e( 'Vasos para o', 'guebel' ); ?> <em><?php esc_html_e( 'futuro', 'guebel' ); ?></em></h1>
			<a href="#loja" class="rule-btn"><?php esc_html_e( 'Ver loja', 'guebel' ); ?></a>
		</div>
	</div>
</section>

<!-- Sobre -->
<section id="sobre" class="split">
	<div class="split-media">
		<img src="<?php echo esc_url( $img . '/about.jpg' ); ?>" alt="<?php esc_attr_e( 'Vaso cubo laranja impresso em 3D sobre pedestal roxo', 'guebel' ); ?>" loading="lazy" width="1024" height="1024">
	</div>
	<div class="split-body">
		<div class="inner">
			<p class="section-eyebrow wide"><?php bloginfo( 'name' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( 'Peças impressas em 3D', 'guebel' ); ?></h2>
			<p class="section-text">
				<?php esc_html_e( 'Alegre a sua casa e proteja o planeta com as nossas peças impressas em 3D. Vasos, candeeiros, mimos e objectos diferentes — desenhados por artistas e produzidos apenas com plásticos reciclados.', 'guebel' ); ?>
			</p>
			<p class="section-text">
				<?php esc_html_e( 'Cada peça é feita por encomenda, na cor que escolher, sem stock parado e sem desperdício.', 'guebel' ); ?>
			</p>
		</div>
	</div>
</section>

<!-- Loja -->
<section id="loja" class="shop container">
	<h2 class="shop-title"><?php esc_html_e( 'Destaques', 'guebel' ); ?></h2>
	<p class="shop-subtitle wide-sm"><?php esc_html_e( 'Feito por encomenda · Plásticos reciclados', 'guebel' ); ?></p>

	<?php
	$wc_products = null;
	if ( guebel_has_woocommerce() ) {
		$wc_products = new WP_Query(
			array(
				'post_type'           => 'product',
				'posts_per_page'      => 6,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'tax_query'           => array(
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'exclude-from-catalog',
						'operator' => 'NOT IN',
					),
				),
			)
		);
	}
	?>

	<?php if ( $wc_products && $wc_products->have_posts() ) : ?>
		<div class="product-grid woocommerce">
			<?php
			while ( $wc_products->have_posts() ) :
				$wc_products->the_post();
				global $product;
				?>
				<article <?php wc_product_class( 'product', $product ); ?>>
					<div class="product-media">
						<a href="<?php the_permalink(); ?>">
							<?php echo wp_kses_post( woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' ) ); ?>
						</a>
						<?php if ( $product->is_on_sale() ) : ?>
							<span class="product-tag"><?php esc_html_e( 'Promoção', 'guebel' ); ?></span>
						<?php endif; ?>
					</div>
					<h3 class="product-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<p class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
					<?php woocommerce_template_loop_add_to_cart(); ?>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>

		<div class="shop-more">
			<a href="<?php echo esc_url( guebel_shop_url() ); ?>" class="rule-btn"><?php esc_html_e( 'Ver toda a loja', 'guebel' ); ?></a>
		</div>
	<?php else : ?>
		<?php if ( guebel_has_woocommerce() && current_user_can( 'manage_woocommerce' ) ) : ?>
			<p class="shop-subtitle wide-sm"><?php esc_html_e( 'Ainda não há produtos publicados — adicione produtos no WooCommerce para os ver aqui.', 'guebel' ); ?></p>
		<?php endif; ?>
		<div class="product-grid">
			<?php foreach ( $products as $p ) : ?>
				<article class="product">
					<div class="product-media">
						<img src="<?php echo esc_url( $p['img'] ); ?>" alt="<?php echo esc_attr( $p['name'] ); ?>" loading="lazy" width="900" height="900">
						<?php if ( $p['tag'] ) : ?>
							<span class="product-tag"><?php echo esc_html( $p['tag'] ); ?></span>
						<?php endif; ?>
					</div>
					<h3 class="product-name"><?php echo esc_html( $p['name'] ); ?></h3>
					<p class="product-price">
						<?php if ( $p['sale'] ) : ?>
							<del><?php echo esc_html( $p['price'] ); ?></del>
							<ins><?php echo esc_html( $p['sale'] ); ?></ins>
						<?php else : ?>
							<?php echo esc_html( $p['price'] ); ?>
						<?php endif; ?>
					</p>
					<button class="rule-btn" data-add-to-cart><?php esc_html_e( 'Adicionar ao cesto', 'guebel' ); ?></button>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<!-- Clube -->
<section id="clube" class="club">
	<h2 class="section-title"><?php esc_html_e( 'Clube Guebel', 'guebel' ); ?></h2>
	<p class="club-text">
		<?php esc_html_e( 'Uma peça nova todos os meses, escolhida a dedo pelos nossos designers, com 20% de desconto em toda a loja e envio gratuito.', 'guebel' ); ?>
	</p>
	<a href="#contacto" class="rule-btn"><?php esc_html_e( 'Quero aderir', 'guebel' ); ?></a>
</section>

<!-- Contacto -->
<section id="contacto" class="split flip">
	<div class="split-media">
		<img src="<?php echo esc_url( $img . '/contact.jpg' ); ?>" alt="<?php esc_attr_e( 'Composição minimalista com flores secas', 'guebel' ); ?>" loading="lazy" width="1000" height="1200">
	</div>
	<div class="split-body">
		<form class="contact-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
			<h2 class="section-title"><?php esc_html_e( 'Falamos?', 'guebel' ); ?></h2>
			<div class="form-row">
				<input required name="nome" placeholder="<?php esc_attr_e( 'O SEU NOME', 'guebel' ); ?>">
				<input required type="email" name="email" placeholder="<?php esc_attr_e( 'O SEU EMAIL', 'guebel' ); ?>">
			</div>
			<div class="form-row">
				<input name="telefone" placeholder="<?php esc_attr_e( 'O SEU TELEFONE', 'guebel' ); ?>" style="grid-column: 1 / -1;">
			</div>
			<div class="form-row">
				<textarea name="mensagem" rows="3" placeholder="<?php esc_attr_e( 'A SUA MENSAGEM', 'guebel' ); ?>" style="grid-column: 1 / -1;"></textarea>
			</div>
			<div class="submit-row">
				<button type="submit" class="rule-btn"><?php esc_html_e( 'Enviar', 'guebel' ); ?></button>
			</div>
		</form>
	</div>
</section>

<?php
get_footer();
