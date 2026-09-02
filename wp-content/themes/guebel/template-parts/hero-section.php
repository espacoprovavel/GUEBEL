<?php
/**
 * Hero section for the front page.
 *
 * This fallback renders when the homepage is NOT built with Elementor.
 * The hero image and text can be set via the Customizer or replaced
 * entirely by an Elementor template.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_image = get_header_image();
if ( ! $hero_image ) {
	$hero_image = get_template_directory_uri() . '/assets/img/hero.jpg';
}
?>

<section class="hero" aria-label="<?php esc_attr_e( 'Featured', 'guebel' ); ?>">
	<img
		src="<?php echo esc_url( $hero_image ); ?>"
		alt="<?php esc_attr_e( 'Guebel — Contemporary Decoration', 'guebel' ); ?>"
		width="1920"
		height="1080"
		fetchpriority="high"
		loading="eager"
	>
	<div class="hero-overlay">
		<div class="container">
			<h1 class="hero-title animate-on-scroll">
				<?php esc_html_e( 'Objects That Define Spaces', 'guebel' ); ?>
			</h1>
			<p class="hero-subtitle animate-on-scroll">
				<?php esc_html_e( 'Discover our curated collection of contemporary decorative pieces, designed to elevate every interior.', 'guebel' ); ?>
			</p>
			<a href="<?php echo esc_url( guebel_shop_url() ); ?>" class="btn btn--white btn--lg animate-on-scroll">
				<?php esc_html_e( 'Explore Collection', 'guebel' ); ?>
			</a>
		</div>
	</div>
</section>
