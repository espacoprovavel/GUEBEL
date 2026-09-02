<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Contact page template. Override with Elementor for full visual editing.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( guebel_is_elementor_content() ) :
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
else :
	$email    = get_theme_mod( 'guebel_email', '' );
	$phone    = get_theme_mod( 'guebel_phone', '' );
	$whatsapp = get_theme_mod( 'guebel_whatsapp', '' );
	$address  = get_theme_mod( 'guebel_address', '' );
	$hours    = get_theme_mod( 'guebel_hours', '' );
?>

<section class="page-hero">
	<div class="container container--narrow text-center py-16">
		<span class="section-eyebrow"><?php esc_html_e( 'Get in Touch', 'guebel' ); ?></span>
		<?php the_title( '<h1 class="section-title">', '</h1>' ); ?>
	</div>
</section>

<div class="container py-12">
	<div class="grid grid--2 gap-12">
		<div class="contact-info">
			<?php if ( $email ) : ?>
				<div class="contact-item">
					<h3 class="contact-item-title"><?php esc_html_e( 'Email', 'guebel' ); ?></h3>
					<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
				</div>
			<?php endif; ?>

			<?php if ( $phone ) : ?>
				<div class="contact-item">
					<h3 class="contact-item-title"><?php esc_html_e( 'Phone', 'guebel' ); ?></h3>
					<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
				</div>
			<?php endif; ?>

			<?php if ( $whatsapp ) : ?>
				<div class="contact-item">
					<h3 class="contact-item-title"><?php esc_html_e( 'WhatsApp', 'guebel' ); ?></h3>
					<p><a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $whatsapp ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $whatsapp ); ?></a></p>
				</div>
			<?php endif; ?>

			<?php if ( $address ) : ?>
				<div class="contact-item">
					<h3 class="contact-item-title"><?php esc_html_e( 'Address', 'guebel' ); ?></h3>
					<p><?php echo nl2br( esc_html( $address ) ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( $hours ) : ?>
				<div class="contact-item">
					<h3 class="contact-item-title"><?php esc_html_e( 'Business Hours', 'guebel' ); ?></h3>
					<p><?php echo nl2br( esc_html( $hours ) ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<div class="contact-form-wrap">
			<?php
			while ( have_posts() ) :
				the_post();
			?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
</div>

<?php
endif;

get_footer();
