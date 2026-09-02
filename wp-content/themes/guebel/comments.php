<?php
/**
 * The template for displaying comments.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area mt-16">
	<?php if ( have_comments() ) : ?>
		<h2 class="section-title text-center">
			<?php
			$comment_count = (int) get_comments_number();
			printf(
				/* translators: %d: number of comments */
				esc_html( _n( '%d Comment', '%d Comments', $comment_count, 'guebel' ) ),
				$comment_count
			);
			?>
		</h2>

		<ol class="comment-list mt-8">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 48,
			) );
			?>
		</ol>

		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments text-center text-muted"><?php esc_html_e( 'Comments are closed.', 'guebel' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form( array(
		'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title section-title">',
		'title_reply_after'  => '</h3>',
		'class_form'         => 'comment-form contact-form',
		'class_submit'       => 'btn btn--primary',
	) );
	?>
</div>
