<?php
/**
 * Default footer template part.
 *
 * Rendered when Elementor Pro Theme Builder does not have
 * a footer template assigned.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$description    = get_theme_mod( 'guebel_store_description', __( 'Curated contemporary decoration for refined interiors.', 'guebel' ) );
$copyright      = get_theme_mod( 'guebel_footer_text', '' );
$payments       = get_theme_mod( 'guebel_payment_methods', 'Visa · Mastercard · PayPal · MB Way' );
$social_links   = guebel_get_social_links();
$email          = get_theme_mod( 'guebel_email', '' );
$phone          = get_theme_mod( 'guebel_phone', '' );
$address        = get_theme_mod( 'guebel_address', '' );

$social_svg = array(
	'instagram' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
	'pinterest' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>',
	'facebook'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
	'tiktok'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.11V9a6.27 6.27 0 00-.79-.05A6.34 6.34 0 003.15 15.3a6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.34-6.34V9.41a8.16 8.16 0 004.76 1.53V7.56a4.85 4.85 0 01-1-.87z"/></svg>',
	'youtube'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.35z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
	'twitter'   => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
);
?>

<footer class="site-footer" role="contentinfo">
	<div class="container">
		<div class="footer-grid">
			<!-- Brand Column -->
			<div>
				<div class="footer-brand">
					<?php if ( has_custom_logo() ) : ?>
						<?php
						$logo_id  = get_theme_mod( 'custom_logo' );
						$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
						?>
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="120" height="30" style="filter:brightness(0) invert(1);">
					<?php else : ?>
						<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
					<?php endif; ?>
				</div>
				<?php if ( $description ) : ?>
					<p class="footer-description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $social_links ) ) : ?>
					<div class="social-links mt-8">
						<?php foreach ( $social_links as $network => $url ) : ?>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $network ) ); ?>">
								<?php echo isset( $social_svg[ $network ] ) ? $social_svg[ $network ] : ''; ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Shop Links -->
			<div>
				<h3 class="footer-col-title"><?php esc_html_e( 'Shop', 'guebel' ); ?></h3>
				<div class="footer-links">
					<?php
					if ( has_nav_menu( 'footer-shop' ) ) {
						wp_nav_menu( array(
							'theme_location' => 'footer-shop',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
					} else {
						?>
						<a href="<?php echo esc_url( guebel_shop_url() ); ?>"><?php esc_html_e( 'All Products', 'guebel' ); ?></a>
						<?php
					}
					?>
				</div>
			</div>

			<!-- Information Links -->
			<div>
				<h3 class="footer-col-title"><?php esc_html_e( 'Information', 'guebel' ); ?></h3>
				<div class="footer-links">
					<?php
					if ( has_nav_menu( 'footer-info' ) ) {
						wp_nav_menu( array(
							'theme_location' => 'footer-info',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						) );
					} else {
						?>
						<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'guebel' ); ?></a>
						<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'guebel' ); ?></a>
						<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'guebel' ); ?></a>
						<?php
					}
					?>
				</div>
			</div>

			<!-- Contact -->
			<div>
				<h3 class="footer-col-title"><?php esc_html_e( 'Contact', 'guebel' ); ?></h3>
				<div class="footer-contact">
					<?php if ( $email ) : ?>
						<div class="footer-contact-item">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</div>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<div class="footer-contact-item">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
							<a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
						</div>
					<?php endif; ?>
					<?php if ( $address ) : ?>
						<div class="footer-contact-item">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
							<span><?php echo esc_html( $address ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Footer Bottom -->
		<div class="footer-bottom">
			<div class="footer-copyright">
				<?php
				if ( $copyright ) {
					echo esc_html( $copyright );
				} else {
					printf(
						'&copy; %s %s. %s',
						esc_html( gmdate( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) ),
						esc_html__( 'All rights reserved.', 'guebel' )
					);
				}
				?>
			</div>

			<?php if ( $payments ) : ?>
				<div class="footer-payments"><?php echo esc_html( $payments ); ?></div>
			<?php endif; ?>

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
				}
				?>
			</div>
		</div>
	</div>
</footer>
