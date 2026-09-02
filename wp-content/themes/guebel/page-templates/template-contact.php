<?php
/**
 * Template Name: Contacto
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
		<p class="section-text text-center mt-6">
			<?php esc_html_e( 'Have a question or need assistance? We are here to help. Fill out the form or use one of our contact channels below.', 'guebel' ); ?>
		</p>
	</div>
</section>

<div class="container container--content py-12">
	<div class="grid grid--2 gap-12">
		<div class="contact-info">
			<?php if ( $email ) : ?>
				<div class="contact-item animate-on-scroll">
					<div class="contact-item-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
					</div>
					<div>
						<h3 class="contact-item-title"><?php esc_html_e( 'Email', 'guebel' ); ?></h3>
						<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $phone ) : ?>
				<div class="contact-item animate-on-scroll">
					<div class="contact-item-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
					</div>
					<div>
						<h3 class="contact-item-title"><?php esc_html_e( 'Phone', 'guebel' ); ?></h3>
						<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^+0-9]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $whatsapp ) : ?>
				<div class="contact-item animate-on-scroll">
					<div class="contact-item-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
					</div>
					<div>
						<h3 class="contact-item-title"><?php esc_html_e( 'WhatsApp', 'guebel' ); ?></h3>
						<p><a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $whatsapp ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $whatsapp ); ?></a></p>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $address ) : ?>
				<div class="contact-item animate-on-scroll">
					<div class="contact-item-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
					</div>
					<div>
						<h3 class="contact-item-title"><?php esc_html_e( 'Address', 'guebel' ); ?></h3>
						<p><?php echo nl2br( esc_html( $address ) ); ?></p>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $hours ) : ?>
				<div class="contact-item animate-on-scroll">
					<div class="contact-item-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
					</div>
					<div>
						<h3 class="contact-item-title"><?php esc_html_e( 'Business Hours', 'guebel' ); ?></h3>
						<p><?php echo nl2br( esc_html( $hours ) ); ?></p>
					</div>
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

			<?php if ( ! get_the_content() ) : ?>
				<div class="contact-form">
					<h2 class="section-title"><?php esc_html_e( 'Send Us a Message', 'guebel' ); ?></h2>
					<p class="section-text"><?php esc_html_e( 'Fill out the form and we will get back to you within 24 hours.', 'guebel' ); ?></p>
					<form class="mt-8" method="post">
						<div class="form-row form-row--2">
							<div class="form-group">
								<label class="form-label" for="contact-name"><?php esc_html_e( 'Name', 'guebel' ); ?></label>
								<input type="text" id="contact-name" name="name" class="form-input" required>
							</div>
							<div class="form-group">
								<label class="form-label" for="contact-email"><?php esc_html_e( 'Email', 'guebel' ); ?></label>
								<input type="email" id="contact-email" name="email" class="form-input" required>
							</div>
						</div>
						<div class="form-group">
							<label class="form-label" for="contact-subject"><?php esc_html_e( 'Subject', 'guebel' ); ?></label>
							<input type="text" id="contact-subject" name="subject" class="form-input">
						</div>
						<div class="form-group">
							<label class="form-label" for="contact-message"><?php esc_html_e( 'Message', 'guebel' ); ?></label>
							<textarea id="contact-message" name="message" class="form-textarea" rows="5" required></textarea>
						</div>
						<div class="submit-row">
							<button type="submit" class="btn btn--primary btn--lg"><?php esc_html_e( 'Send Message', 'guebel' ); ?></button>
						</div>
					</form>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
endif;

get_footer();
