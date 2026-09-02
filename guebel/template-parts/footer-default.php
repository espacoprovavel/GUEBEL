<?php
/**
 * Rodapé padrão do tema (usado quando o Elementor Pro não define um).
 *
 * @package Guebel
 */

?>
<footer class="site-footer">
	<div class="container footer-grid">
		<div>
			<p class="footer-brand"><?php bloginfo( 'name' ); ?></p>
			<p class="footer-text">
				<?php esc_html_e( 'Gosta das nossas peças? Subscreva para novidades e ofertas exclusivas.', 'guebel' ); ?>
			</p>
			<form class="newsletter" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
				<input required type="email" name="s" placeholder="<?php esc_attr_e( 'EMAIL', 'guebel' ); ?>" aria-label="<?php esc_attr_e( 'O seu email', 'guebel' ); ?>">
				<button type="submit" class="rule-btn"><?php esc_html_e( 'Subscrever', 'guebel' ); ?></button>
			</form>
		</div>

		<div class="social-links">
			<a href="#" class="wide-sm">Facebook</a>
			<a href="#" class="wide-sm">Instagram</a>
			<a href="#" class="wide-sm">Pinterest</a>
		</div>
	</div>

	<div class="container footer-legal">
		<a href="#"><?php esc_html_e( 'Termos', 'guebel' ); ?></a>
		<a href="#"><?php esc_html_e( 'Privacidade', 'guebel' ); ?></a>
		<a href="#">FAQ</a>
		<a href="#"><?php esc_html_e( 'Envios', 'guebel' ); ?></a>
		<a href="#"><?php esc_html_e( 'Trocas e devoluções', 'guebel' ); ?></a>
	</div>
</footer>
