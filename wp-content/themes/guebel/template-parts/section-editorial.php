<?php
/**
 * Editorial split section for the front page.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="section" aria-label="<?php esc_attr_e( 'About Guebel', 'guebel' ); ?>">
	<div class="split animate-on-scroll">
		<div class="split-media">
			<img
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/about.jpg' ); ?>"
				alt="<?php esc_attr_e( 'Guebel interior decoration', 'guebel' ); ?>"
				width="960"
				height="720"
				loading="lazy"
			>
		</div>
		<div class="split-body">
			<div class="inner">
				<span class="section-eyebrow"><?php esc_html_e( 'Our Story', 'guebel' ); ?></span>
				<h2 class="section-title"><?php esc_html_e( 'Design That Speaks', 'guebel' ); ?></h2>
				<p class="section-text">
					<?php esc_html_e( 'At Guebel, we believe every object tells a story. Our collection brings together contemporary design and artisanal craft, creating pieces that transform spaces into personal sanctuaries.', 'guebel' ); ?>
				</p>
				<p class="section-text">
					<?php esc_html_e( 'Each piece is carefully selected for its form, material quality, and ability to create atmosphere.', 'guebel' ); ?>
				</p>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="rule-btn mt-8">
					<?php esc_html_e( 'Discover More', 'guebel' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
