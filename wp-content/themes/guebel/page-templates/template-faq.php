<?php
/**
 * Template Name: Perguntas Frequentes
 * Template Post Type: page
 *
 * FAQ page template with accordion layout.
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
		<span class="section-eyebrow"><?php esc_html_e( 'Help Center', 'guebel' ); ?></span>
		<?php the_title( '<h1 class="section-title">', '</h1>' ); ?>
	</div>
</section>

<div class="container container--content py-12">
	<?php
	while ( have_posts() ) :
		the_post();
	?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php
endif;

get_footer();
