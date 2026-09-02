<?php
/**
 * Template de recurso — listagem de artigos.
 *
 * @package Guebel
 */

get_header();
?>

<section class="shop container">
	<h1 class="shop-title"><?php single_post_title( '', true ); ?></h1>

	<?php if ( have_posts() ) : ?>
		<div class="product-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'product' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="product-media">
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a>
						</div>
					<?php endif; ?>
					<h2 class="product-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p class="section-text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
					<a href="<?php the_permalink(); ?>" class="rule-btn"><?php esc_html_e( 'Ler mais', 'guebel' ); ?></a>
				</article>
				<?php
			endwhile;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p class="section-text" style="text-align:center; margin-top:3rem;">
			<?php esc_html_e( 'Ainda não há conteúdo por aqui.', 'guebel' ); ?>
		</p>
	<?php endif; ?>
</section>

<?php
get_footer();
