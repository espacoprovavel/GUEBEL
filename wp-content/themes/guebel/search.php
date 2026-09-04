<?php
/**
 * The template for displaying search results.
 *
 * @package Guebel
 * @since   1.0.0
 */

get_header();
?>

<div class="page-wrap">
	<div class="container container--content">
		<header class="post-header text-center">
			<h1>
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Results for: %s', 'guebel' ),
					'<span>' . get_search_query() . '</span>'
				);
				?>
			</h1>
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
			<div class="text-center mt-16">
				<p class="section-text"><?php esc_html_e( 'No results found. Please try a different search term.', 'guebel' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
