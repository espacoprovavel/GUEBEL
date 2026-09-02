<?php
/**
 * Template part for displaying posts in archive/index views.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card animate-on-scroll' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-card-media">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'guebel-blog', array( 'loading' => 'lazy' ) ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="post-card-body">
		<?php
		$categories = get_the_category();
		if ( ! empty( $categories ) ) :
		?>
			<span class="post-card-category">
				<a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
					<?php echo esc_html( $categories[0]->name ); ?>
				</a>
			</span>
		<?php endif; ?>

		<h2 class="post-card-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="post-card-excerpt">
			<?php the_excerpt(); ?>
		</div>

		<div class="post-card-meta">
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
			<span class="meta-sep">&middot;</span>
			<span><?php echo esc_html( guebel_reading_time() ); ?></span>
		</div>
	</div>
</article>
