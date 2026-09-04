<?php
/**
 * Template Name: Clube
 * Template Post Type: page
 *
 * Club/membership page template. Override with Elementor for full visual editing.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( guebel_is_elementor_content() ) :
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
else :
?>

<section class="page-hero">
	<div class="container container--narrow text-center py-16">
		<span class="section-eyebrow"><?php esc_html_e( 'Exclusive', 'guebel' ); ?></span>
		<?php the_title( '<h1 class="section-title">', '</h1>' ); ?>
		<p class="section-text text-center mt-6">
			<?php esc_html_e( 'Join our community and enjoy exclusive benefits, early access to new collections, and special discounts reserved for members.', 'guebel' ); ?>
		</p>
	</div>
</section>

<section class="club-benefits section">
	<div class="container container--content">
		<div class="grid grid--3 gap-12">
			<div class="club-benefit-card animate-on-scroll">
				<div class="club-benefit-icon">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
				</div>
				<h3 class="club-benefit-title"><?php esc_html_e( '20% Off Everything', 'guebel' ); ?></h3>
				<p class="club-benefit-text"><?php esc_html_e( 'Members enjoy 20% discount on the entire store, on every purchase, all year round.', 'guebel' ); ?></p>
			</div>

			<div class="club-benefit-card animate-on-scroll">
				<div class="club-benefit-icon">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
				</div>
				<h3 class="club-benefit-title"><?php esc_html_e( 'Free Shipping', 'guebel' ); ?></h3>
				<p class="club-benefit-text"><?php esc_html_e( 'Free shipping on all orders, no minimum purchase required. Your pieces arrive safely at your door.', 'guebel' ); ?></p>
			</div>

			<div class="club-benefit-card animate-on-scroll">
				<div class="club-benefit-icon">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
				</div>
				<h3 class="club-benefit-title"><?php esc_html_e( 'Early Access', 'guebel' ); ?></h3>
				<p class="club-benefit-text"><?php esc_html_e( 'Be the first to discover and purchase new collections before anyone else.', 'guebel' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="club-cta section">
	<div class="container container--narrow text-center">
		<div class="club-cta-box">
			<span class="section-eyebrow"><?php esc_html_e( 'Monthly Curations', 'guebel' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Curated Pieces, Delivered Monthly', 'guebel' ); ?></h2>
			<p class="section-text text-center">
				<?php esc_html_e( 'Every month, receive a carefully selected decorative piece at your door. Each item is chosen by our team of designers to complement your home.', 'guebel' ); ?>
			</p>

			<div class="club-price mt-8">
				<span class="club-price-value">&euro;29<small>,99</small></span>
				<span class="club-price-period">/<?php esc_html_e( 'month', 'guebel' ); ?></span>
			</div>

			<ul class="club-features mt-8">
				<li>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
					<?php esc_html_e( '1 exclusive curated piece per month', 'guebel' ); ?>
				</li>
				<li>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
					<?php esc_html_e( '20% discount on the entire store', 'guebel' ); ?>
				</li>
				<li>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
					<?php esc_html_e( 'Free shipping on all orders', 'guebel' ); ?>
				</li>
				<li>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
					<?php esc_html_e( 'Early access to new collections', 'guebel' ); ?>
				</li>
				<li>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
					<?php esc_html_e( 'Cancel anytime, no commitment', 'guebel' ); ?>
				</li>
			</ul>

			<a href="<?php echo esc_url( guebel_shop_url() ); ?>" class="btn btn--primary btn--lg mt-8">
				<?php esc_html_e( 'Join Now', 'guebel' ); ?>
			</a>
		</div>
	</div>
</section>

<section class="club-how section">
	<div class="container container--content">
		<div class="text-center">
			<span class="section-eyebrow"><?php esc_html_e( 'How It Works', 'guebel' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'Simple, Three Steps', 'guebel' ); ?></h2>
		</div>

		<div class="grid grid--3 mt-12">
			<div class="club-step animate-on-scroll">
				<span class="club-step-number">01</span>
				<h3 class="club-step-title"><?php esc_html_e( 'Subscribe', 'guebel' ); ?></h3>
				<p class="club-step-text"><?php esc_html_e( 'Choose your plan and create your account. Quick registration, instant access.', 'guebel' ); ?></p>
			</div>

			<div class="club-step animate-on-scroll">
				<span class="club-step-number">02</span>
				<h3 class="club-step-title"><?php esc_html_e( 'We Curate', 'guebel' ); ?></h3>
				<p class="club-step-text"><?php esc_html_e( 'Our team selects a unique piece based on the latest trends in decoration and design.', 'guebel' ); ?></p>
			</div>

			<div class="club-step animate-on-scroll">
				<span class="club-step-number">03</span>
				<h3 class="club-step-title"><?php esc_html_e( 'Receive', 'guebel' ); ?></h3>
				<p class="club-step-text"><?php esc_html_e( 'Your piece arrives safely packaged at your door. Enjoy and transform your space.', 'guebel' ); ?></p>
			</div>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/section', 'newsletter' ); ?>

<?php
endif;

get_footer();
