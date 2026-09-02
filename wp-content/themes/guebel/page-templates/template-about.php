<?php
/**
 * Template Name: Sobre
 * Template Post Type: page
 *
 * About page template with styled layout.
 * Override with Elementor for full visual editing.
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
		<span class="section-eyebrow"><?php esc_html_e( 'Our Story', 'guebel' ); ?></span>
		<?php the_title( '<h1 class="section-title">', '</h1>' ); ?>
	</div>
</section>

<section class="section">
	<div class="split animate-on-scroll">
		<div class="split-media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'large' ); ?>
			<?php else : ?>
				<img
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/about.jpg' ); ?>"
					alt="<?php esc_attr_e( 'About Guebel', 'guebel' ); ?>"
					width="960"
					height="720"
					loading="lazy"
				>
			<?php endif; ?>
		</div>
		<div class="split-body">
			<div class="inner">
				<span class="section-eyebrow"><?php esc_html_e( 'Who We Are', 'guebel' ); ?></span>
				<h2 class="section-title"><?php esc_html_e( 'Decoration for the Future', 'guebel' ); ?></h2>
				<p class="section-text">
					<?php esc_html_e( 'At Guebel, we believe that beautiful objects can also be sustainable. Our pieces are designed with care and produced using 3D printing technology, minimising waste and maximising creativity.', 'guebel' ); ?>
				</p>
				<p class="section-text">
					<?php esc_html_e( 'Every product is made to order using recycled plastics, ensuring zero waste production and a reduced environmental footprint.', 'guebel' ); ?>
				</p>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="split flip animate-on-scroll">
		<div class="split-media">
			<img
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/p1.jpg' ); ?>"
				alt="<?php esc_attr_e( 'Guebel design process', 'guebel' ); ?>"
				width="960"
				height="720"
				loading="lazy"
			>
		</div>
		<div class="split-body">
			<div class="inner">
				<span class="section-eyebrow"><?php esc_html_e( 'Our Mission', 'guebel' ); ?></span>
				<h2 class="section-title"><?php esc_html_e( 'Design That Speaks', 'guebel' ); ?></h2>
				<p class="section-text">
					<?php esc_html_e( 'We create decorative objects that tell stories. Each piece in our collection blends contemporary design with artisanal attention to detail, transforming any space into a personal sanctuary.', 'guebel' ); ?>
				</p>
				<p class="section-text">
					<?php esc_html_e( 'Our pieces are carefully selected for their form, material quality, and ability to create atmosphere in every home.', 'guebel' ); ?>
				</p>
			</div>
		</div>
	</div>
</section>

<section class="about-values section">
	<div class="container container--content">
		<div class="text-center">
			<span class="section-eyebrow"><?php esc_html_e( 'Our Values', 'guebel' ); ?></span>
			<h2 class="section-title"><?php esc_html_e( 'What Drives Us', 'guebel' ); ?></h2>
		</div>

		<div class="grid grid--3 mt-12">
			<div class="about-value-card animate-on-scroll">
				<svg class="about-value-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
				<h3><?php esc_html_e( 'Sustainability', 'guebel' ); ?></h3>
				<p><?php esc_html_e( 'We use recycled materials and produce on demand. Zero waste, maximum impact.', 'guebel' ); ?></p>
			</div>

			<div class="about-value-card animate-on-scroll">
				<svg class="about-value-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
				<h3><?php esc_html_e( 'Quality', 'guebel' ); ?></h3>
				<p><?php esc_html_e( 'Every piece passes rigorous quality controls before reaching your hands.', 'guebel' ); ?></p>
			</div>

			<div class="about-value-card animate-on-scroll">
				<svg class="about-value-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
				<h3><?php esc_html_e( 'Passion', 'guebel' ); ?></h3>
				<p><?php esc_html_e( 'Created with love and dedication by Portuguese artisans and designers.', 'guebel' ); ?></p>
			</div>
		</div>
	</div>
</section>

<div class="container container--content py-12">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( get_the_content() ) :
	?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php
		endif;
	endwhile;
	?>
</div>

<?php get_template_part( 'template-parts/section', 'benefits' ); ?>

<?php
endif;

get_footer();
