<?php
/**
 * The template for displaying archive pages.
 *
 * @package Guebel
 * @since   1.0.0
 */

get_header();
?>

<div class="page-wrap">
	<div class="container container--content">
		<header class="post-header text-center">
			<?php the_archive_title( '<h1>', '</h1>' ); ?>
			<?php the_archive_description( '<div class="section-text">', '</div>' ); ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="post-grid post-grid--3 mt-12">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'post' ); ?>
				<?php endwhile; ?>
			</div>

			<?php the_posts_pagination( array(
				'prev_text' => esc_html__( 'Previous', 'guebel' ),
				'next_text' => esc_html__( 'Next', 'guebel' ),
			) ); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
