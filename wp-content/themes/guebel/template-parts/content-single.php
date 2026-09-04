<?php
/**
 * Template part for displaying single post content.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
	<header class="post-header">
		<?php
		$categories = get_the_category();
		if ( ! empty( $categories ) ) :
		?>
			<div class="post-categories">
				<?php foreach ( $categories as $cat ) : ?>
					<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="post-category-link">
						<?php echo esc_html( $cat->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php the_title( '<h1 class="post-title">', '</h1>' ); ?>

		<div class="post-meta">
			<span class="post-author">
				<?php
				printf(
					/* translators: %s: author name */
					esc_html__( 'By %s', 'guebel' ),
					'<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
				);
				?>
			</span>
			<span class="meta-sep">&middot;</span>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
			<span class="meta-sep">&middot;</span>
			<span><?php echo esc_html( guebel_reading_time() ); ?></span>
		</div>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="post-featured-image">
			<?php the_post_thumbnail( 'large' ); ?>
			<?php if ( get_the_post_thumbnail_caption() ) : ?>
				<figcaption><?php the_post_thumbnail_caption(); ?></figcaption>
			<?php endif; ?>
		</figure>
	<?php endif; ?>

	<div class="post-content entry-content">
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'guebel' ),
			'after'  => '</nav>',
		) );
		?>
	</div>

	<footer class="post-footer">
		<?php
		$tags = get_the_tags();
		if ( ! empty( $tags ) ) :
		?>
			<div class="post-tags">
				<?php foreach ( $tags as $tag ) : ?>
					<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="post-tag" rel="tag">
						<?php echo esc_html( $tag->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( function_exists( 'guebel_get_social_links' ) ) : ?>
			<div class="post-share">
				<span class="post-share-label"><?php esc_html_e( 'Share:', 'guebel' ); ?></span>
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( get_permalink() ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on Facebook', 'guebel' ); ?>">Facebook</a>
				<a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on Twitter', 'guebel' ); ?>">Twitter</a>
				<a href="https://pinterest.com/pin/create/button/?url=<?php echo rawurlencode( get_permalink() ); ?>&description=<?php echo rawurlencode( get_the_title() ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on Pinterest', 'guebel' ); ?>">Pinterest</a>
			</div>
		<?php endif; ?>
	</footer>
</article>
