<?php
/**
 * Benefits / trust signals section.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="benefits" aria-label="<?php esc_attr_e( 'Why Guebel', 'guebel' ); ?>">
	<div class="container">
		<div class="benefits-grid">
			<div class="benefit-item animate-on-scroll">
				<svg class="benefit-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
				<h3 class="benefit-title"><?php esc_html_e( 'Secure Delivery', 'guebel' ); ?></h3>
				<p class="benefit-text"><?php esc_html_e( 'Carefully packaged and delivered to your door with full tracking.', 'guebel' ); ?></p>
			</div>

			<div class="benefit-item animate-on-scroll">
				<svg class="benefit-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
				<h3 class="benefit-title"><?php esc_html_e( 'Secure Payment', 'guebel' ); ?></h3>
				<p class="benefit-text"><?php esc_html_e( 'SSL-encrypted checkout with trusted payment providers.', 'guebel' ); ?></p>
			</div>

			<div class="benefit-item animate-on-scroll">
				<svg class="benefit-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
				<h3 class="benefit-title"><?php esc_html_e( 'Curated Selection', 'guebel' ); ?></h3>
				<p class="benefit-text"><?php esc_html_e( 'Every piece is selected for quality, design, and craftsmanship.', 'guebel' ); ?></p>
			</div>

			<div class="benefit-item animate-on-scroll">
				<svg class="benefit-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
				<h3 class="benefit-title"><?php esc_html_e( 'Dedicated Support', 'guebel' ); ?></h3>
				<p class="benefit-text"><?php esc_html_e( 'Personal assistance for orders, questions, and custom requests.', 'guebel' ); ?></p>
			</div>
		</div>
	</div>
</section>
