<?php
/**
 * Featured products section for the front page.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! guebel_has_woocommerce() ) {
	return;
}

$products = wc_get_products( array(
	'status'   => 'publish',
	'limit'    => 6,
	'featured' => true,
	'orderby'  => 'date',
	'order'    => 'DESC',
) );

if ( empty( $products ) ) {
	$products = wc_get_products( array(
		'status'  => 'publish',
		'limit'   => 6,
		'orderby' => 'date',
		'order'   => 'DESC',
	) );
}

if ( empty( $products ) ) {
	return;
}
?>

<section class="shop section" aria-label="<?php esc_attr_e( 'Featured Products', 'guebel' ); ?>">
	<div class="container">
		<div class="text-center">
			<span class="section-eyebrow"><?php esc_html_e( 'Curated Selection', 'guebel' ); ?></span>
			<h2 class="shop-title"><?php esc_html_e( 'Featured Pieces', 'guebel' ); ?></h2>
			<p class="shop-subtitle"><?php esc_html_e( 'Handpicked objects for contemporary spaces', 'guebel' ); ?></p>
		</div>

		<div class="product-grid">
			<?php foreach ( $products as $product_obj ) : ?>
				<?php
				$post_object = get_post( $product_obj->get_id() );
				setup_postdata( $GLOBALS['post'] = $post_object );
				$product = wc_get_product( $product_obj->get_id() );
				$image   = wp_get_attachment_image_url( $product->get_image_id(), 'guebel-card' );
				if ( ! $image ) {
					$image = wc_placeholder_img_src( 'guebel-card' );
				}
				?>
				<article class="product animate-on-scroll">
					<div class="product-media">
						<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
							<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" width="600" height="600" loading="lazy">
						</a>
						<?php guebel_product_badges( $product ); ?>
						<div class="product-actions">
							<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="btn btn--white btn--sm" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
								<?php echo esc_html( $product->add_to_cart_text() ); ?>
							</a>
						</div>
					</div>
					<h3 class="product-name">
						<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
							<?php echo esc_html( $product->get_name() ); ?>
						</a>
					</h3>
					<div class="product-price">
						<?php echo $product->get_price_html(); ?>
					</div>
				</article>
			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>
		</div>

		<div class="shop-more">
			<a href="<?php echo esc_url( guebel_shop_url() ); ?>" class="rule-btn">
				<?php esc_html_e( 'View All Products', 'guebel' ); ?>
			</a>
		</div>
	</div>
</section>
