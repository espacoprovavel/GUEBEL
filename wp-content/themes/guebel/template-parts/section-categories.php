<?php
/**
 * Categories showcase section for the front page.
 *
 * Displays WooCommerce product categories in a visual grid.
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

$categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'parent'     => 0,
	'exclude'    => array( get_option( 'default_product_cat' ) ),
	'number'     => 4,
	'orderby'    => 'count',
	'order'      => 'DESC',
) );

if ( is_wp_error( $categories ) || empty( $categories ) ) {
	return;
}
?>

<section class="categories-showcase section" aria-label="<?php esc_attr_e( 'Shop by Category', 'guebel' ); ?>">
	<div class="container">
		<div class="text-center">
			<span class="section-eyebrow"><?php esc_html_e( 'Collections', 'guebel' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Shop by Category', 'guebel' ); ?></h2>
		</div>

		<div class="grid grid--2 mt-12">
			<?php foreach ( $categories as $category ) : ?>
				<?php
				$thumb_id  = get_term_meta( $category->term_id, 'thumbnail_id', true );
				$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'guebel-card' ) : get_template_directory_uri() . '/assets/img/p1.jpg';
				?>
				<a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="category-card animate-on-scroll">
					<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" width="600" height="800" loading="lazy">
					<div class="category-card-overlay">
						<div>
							<h3 class="category-card-title"><?php echo esc_html( $category->name ); ?></h3>
							<p class="category-card-count">
								<?php
								printf(
									/* translators: %d: number of products */
									esc_html( _n( '%d Product', '%d Products', $category->count, 'guebel' ) ),
									$category->count
								);
								?>
							</p>
						</div>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
