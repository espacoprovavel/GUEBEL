<?php
/**
 * The template for displaying pages.
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
		<div class="container container--content">
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="post-header">
						<?php the_title( '<h1>', '</h1>' ); ?>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="post-featured-image">
							<?php the_post_thumbnail( 'guebel-hero' ); ?>
						</div>
					<?php endif; ?>

					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
