<?php
/**
 * Template Name: Full Width
 * Template Post Type: page
 *
 * Full-width page template without sidebar.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="container container--wide py-16">
	<?php
	while ( have_posts() ) :
		the_post();

		if ( guebel_is_elementor_content() ) :
			the_content();
		else :
	?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
	<?php
		endif;
	endwhile;
	?>
</div>

<?php
get_footer();
