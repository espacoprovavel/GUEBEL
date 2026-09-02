<?php
/**
 * Template de artigo individual — compatível com o Elementor.
 *
 * @package Guebel
 */

get_header();

while ( have_posts() ) :
	the_post();

	$is_elementor = guebel_is_elementor_content();
	?>
	<article <?php post_class( $is_elementor ? 'page-wrap elementor-built' : 'page-wrap container' ); ?>>
		<?php if ( ! $is_elementor ) : ?>
			<h1 class="shop-title"><?php the_title(); ?></h1>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="entry-media"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>
		<?php endif; ?>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
