<?php
/**
 * The main template file.
 *
 * @package Guebel
 * @since   1.0.0
 */

get_header();

$is_elementor = guebel_is_elementor_content();
?>

<div class="page-wrap<?php echo $is_elementor ? ' elementor-built' : ''; ?>">
	<?php if ( ! $is_elementor ) : ?>
	<div class="container container--content">
		<?php if ( have_posts() ) : ?>
			<div class="post-grid post-grid--3">
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
	<?php else : ?>
		<?php the_content(); ?>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
