<?php
/**
 * The template for displaying single posts.
 *
 * @package Guebel
 * @since   1.0.0
 */

get_header();

$is_elementor = guebel_is_elementor_content();
?>

<div class="page-wrap<?php echo $is_elementor ? ' elementor-built' : ''; ?>">
	<?php if ( $is_elementor ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; ?>
	<?php else : ?>
		<div class="container container--narrow">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'single' ); ?>
			<?php endwhile; ?>

			<?php
			the_post_navigation( array(
				'prev_text' => '<span class="nav-label">' . esc_html__( 'Previous', 'guebel' ) . '</span><span class="nav-title">%title</span>',
				'next_text' => '<span class="nav-label">' . esc_html__( 'Next', 'guebel' ) . '</span><span class="nav-title">%title</span>',
			) );
			?>

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
