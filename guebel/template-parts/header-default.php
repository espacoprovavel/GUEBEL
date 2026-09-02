<?php
/**
 * Cabeçalho padrão do tema (usado quando o Elementor Pro não define um).
 *
 * @package Guebel
 */

?>
<header class="site-header">
	<div class="container bar">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-brand">
			<?php bloginfo( 'name' ); ?>
		</a>

		<div class="header-actions">
			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'primary',
					'container'       => 'nav',
					'container_class' => 'site-nav',
					'menu_class'      => 'site-nav',
					'fallback_cb'     => 'guebel_fallback_menu',
					'depth'           => 1,
				)
			);
			?>

			<a class="cart-indicator" href="<?php echo esc_url( guebel_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cesto de compras', 'guebel' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
				<?php guebel_cart_count_markup(); ?>
			</a>

			<button class="menu-toggle" data-menu-toggle aria-label="<?php esc_attr_e( 'Abrir menu', 'guebel' ); ?>" aria-expanded="false">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
			</button>
		</div>
	</div>

	<nav class="mobile-nav" data-mobile-nav hidden>
		<a href="<?php echo esc_url( guebel_shop_url() ); ?>" class="wide-sm"><?php esc_html_e( 'Loja', 'guebel' ); ?></a>
		<a href="#sobre" class="wide-sm"><?php esc_html_e( 'Sobre', 'guebel' ); ?></a>
		<a href="#clube" class="wide-sm"><?php esc_html_e( 'Clube', 'guebel' ); ?></a>
		<a href="#contacto" class="wide-sm"><?php esc_html_e( 'Contacto', 'guebel' ); ?></a>
	</nav>
</header>
