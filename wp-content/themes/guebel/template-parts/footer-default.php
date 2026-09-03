<?php
/**
 * Default footer template part.
 *
 * FLO & CO inspired layout: brand + newsletter on the left,
 * social links as text on the right, centred legal links at the bottom.
 * Fully editable via WordPress Customizer (Guebel > Footer Settings)
 * and menus (footer-legal location).
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$description  = get_theme_mod( 'guebel_store_description', __( 'Peças de decoração impressas em 3D, criadas em Portugal.', 'guebel' ) );
$copyright    = get_theme_mod( 'guebel_footer_text', '' );
$newsletter   = get_theme_mod( 'guebel_newsletter_text', __( 'Subscreva e receba novidades, lançamentos e ofertas exclusivas.', 'guebel' ) );
$social_links = guebel_get_social_links();
$brand_name   = get_bloginfo( 'name' );
?>

<footer class="site-footer" role="contentinfo">
	<div class="container">
		<div class="footer-top">

			<!-- Brand + Newsletter -->
			<div class="footer-newsletter">
				<div class="footer-brand">
					<?php if ( has_custom_logo() ) : ?>
						<?php
						$logo_id  = get_theme_mod( 'custom_logo' );
						$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
						?>
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $brand_name ); ?>" width="140" height="36" style="filter:brightness(0) invert(1);">
					<?php else : ?>
						<?php echo esc_html( $brand_name ); ?>
					<?php endif; ?>
				</div>

				<?php if ( $newsletter ) : ?>
					<p class="footer-newsletter-text"><?php echo esc_html( $newsletter ); ?></p>
				<?php endif; ?>

				<form class="footer-newsletter-form" data-newsletter-form>
					<label class="screen-reader-text" for="footer-newsletter-email"><?php esc_html_e( 'Email', 'guebel' ); ?></label>
					<input type="email" id="footer-newsletter-email" name="email" class="footer-newsletter-input" placeholder="<?php esc_attr_e( 'O seu email', 'guebel' ); ?>" required>
					<button type="submit" class="rule-btn footer-newsletter-btn"><?php esc_html_e( 'Subscrever', 'guebel' ); ?></button>
				</form>
			</div>

			<!-- Social links as text -->
			<div class="footer-social-text">
				<h3 class="footer-col-title"><?php esc_html_e( 'Siga-nos', 'guebel' ); ?></h3>
				<div class="footer-social-list">
					<?php if ( ! empty( $social_links ) ) : ?>
						<?php foreach ( $social_links as $network => $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( strtoupper( $network ) ); ?>
							</a>
						<?php endforeach; ?>
					<?php else : ?>
						<a href="https://instagram.com" target="_blank" rel="noopener noreferrer">INSTAGRAM</a>
						<a href="https://facebook.com" target="_blank" rel="noopener noreferrer">FACEBOOK</a>
						<a href="https://pinterest.com" target="_blank" rel="noopener noreferrer">PINTEREST</a>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Footer Bottom -->
		<div class="footer-bottom">
			<div class="footer-legal">
				<?php
				if ( has_nav_menu( 'footer-legal' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'footer-legal',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
						'items_wrap'     => '%3$s',
					) );
				} else {
					?>
					<a href="<?php echo esc_url( home_url( '/termos/' ) ); ?>"><?php esc_html_e( 'Termos e Condições', 'guebel' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/privacidade/' ) ); ?>"><?php esc_html_e( 'Política de Privacidade', 'guebel' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'guebel' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/envios/' ) ); ?>"><?php esc_html_e( 'Envios e Entregas', 'guebel' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/trocas/' ) ); ?>"><?php esc_html_e( 'Trocas e Devoluções', 'guebel' ); ?></a>
					<?php
				}
				?>
			</div>

			<div class="footer-copyright">
				<?php
				if ( $copyright ) {
					echo esc_html( $copyright );
				} else {
					printf(
						'&copy; %s %s. %s',
						esc_html( gmdate( 'Y' ) ),
						esc_html( $brand_name ),
						esc_html__( 'Todos os direitos reservados.', 'guebel' )
					);
				}
				?>
			</div>
		</div>
	</div>
</footer>
