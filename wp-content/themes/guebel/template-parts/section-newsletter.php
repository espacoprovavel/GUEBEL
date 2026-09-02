<?php
/**
 * Newsletter / club section.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="club" id="club" aria-label="<?php esc_attr_e( 'Newsletter', 'guebel' ); ?>">
	<div class="container container--narrow">
		<span class="section-eyebrow"><?php esc_html_e( 'Guebel Club', 'guebel' ); ?></span>
		<h2 class="section-title"><?php esc_html_e( 'Join Our Community', 'guebel' ); ?></h2>
		<p class="club-text">
			<?php esc_html_e( 'Members enjoy 20% discount on the entire store, free shipping on all orders, and early access to new collections.', 'guebel' ); ?>
		</p>
		<a href="<?php echo esc_url( home_url( '/clube/' ) ); ?>" class="btn btn--white btn--lg" style="margin-top:var(--space-6);">
			<?php esc_html_e( 'Discover the Club', 'guebel' ); ?>
		</a>
		<div class="club-divider"></div>
		<p class="club-text" style="margin-top:var(--space-8);">
			<?php esc_html_e( 'Or subscribe to our newsletter for exclusive offers and design inspiration.', 'guebel' ); ?>
		</p>
		<form class="newsletter-form" action="#" method="post" data-newsletter-form>
			<label class="visually-hidden" for="newsletter-email"><?php esc_html_e( 'Email address', 'guebel' ); ?></label>
			<input
				type="email"
				id="newsletter-email"
				name="email"
				placeholder="<?php esc_attr_e( 'Your email address', 'guebel' ); ?>"
				required
				autocomplete="email"
			>
			<button type="submit" class="rule-btn"><?php esc_html_e( 'Subscribe', 'guebel' ); ?></button>
		</form>
	</div>
</section>
