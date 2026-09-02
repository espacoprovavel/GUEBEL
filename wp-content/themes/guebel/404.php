<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Guebel
 * @since   1.0.0
 */

get_header();
?>

<div class="page-wrap">
	<div class="page-404">
		<div class="error-code" aria-hidden="true">404</div>
		<h1 class="error-title"><?php esc_html_e( 'Page Not Found', 'guebel' ); ?></h1>
		<p class="error-text"><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'guebel' ); ?></p>

		<?php get_search_form(); ?>

		<div class="suggested-links">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--outline"><?php esc_html_e( 'Home', 'guebel' ); ?></a>
			<?php if ( guebel_has_woocommerce() ) : ?>
				<a href="<?php echo esc_url( guebel_shop_url() ); ?>" class="btn btn--outline"><?php esc_html_e( 'Shop', 'guebel' ); ?></a>
			<?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn btn--outline"><?php esc_html_e( 'Blog', 'guebel' ); ?></a>
		</div>
	</div>
</div>

<?php get_footer(); ?>
