<?php
/**
 * Default header template part.
 *
 * Rendered when Elementor Pro Theme Builder does not have
 * a header template assigned. Fully editable via WordPress
 * Customizer settings (Guebel > Header Settings).
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$topbar_text        = get_theme_mod( 'guebel_topbar_text', '' );
$is_transparent     = get_theme_mod( 'guebel_header_transparent', false ) && is_front_page();
$header_classes     = 'site-header';
if ( $is_transparent ) {
	$header_classes .= ' site-header--transparent';
}
?>

<?php if ( $topbar_text ) : ?>
<div class="top-bar" role="banner">
	<div class="container">
		<?php echo wp_kses_post( $topbar_text ); ?>
	</div>
</div>
<?php endif; ?>

<header class="<?php echo esc_attr( $header_classes ); ?>" role="banner">
	<div class="container">
		<div class="bar">
			<!-- Brand -->
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-brand" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php if ( has_custom_logo() ) : ?>
					<?php
					$logo_id  = get_theme_mod( 'custom_logo' );
					$logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
					?>
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="160" height="40">
				<?php else : ?>
					<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
				<?php endif; ?>
			</a>

			<!-- Primary Navigation -->
			<nav class="site-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'guebel' ); ?>">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'depth'          => 2,
						'fallback_cb'    => false,
					) );
				} else {
					guebel_fallback_menu();
				}
				?>
			</nav>

			<!-- Header Actions -->
			<div class="header-actions">
				<!-- Search -->
				<button class="header-icon" aria-label="<?php esc_attr_e( 'Search', 'guebel' ); ?>" data-search-toggle>
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
				</button>

				<!-- Account -->
				<a href="<?php echo esc_url( guebel_account_url() ); ?>" class="header-icon" aria-label="<?php esc_attr_e( 'My Account', 'guebel' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
				</a>

				<!-- Cart -->
				<a href="<?php echo esc_url( guebel_cart_url() ); ?>" class="header-icon cart-indicator" aria-label="<?php esc_attr_e( 'Shopping Cart', 'guebel' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
					<?php guebel_cart_count_markup(); ?>
				</a>

				<!-- Mobile Menu Toggle -->
				<button class="menu-toggle header-icon" aria-label="<?php esc_attr_e( 'Menu', 'guebel' ); ?>" aria-expanded="false" aria-controls="mobile-nav" data-menu-toggle>
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
				</button>
			</div>
		</div>
	</div>

	<!-- Mobile Navigation -->
	<nav id="mobile-nav" class="mobile-nav" hidden role="navigation" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'guebel' ); ?>">
		<?php
		if ( has_nav_menu( 'mobile' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'mobile',
				'container'      => false,
				'depth'          => 2,
				'fallback_cb'    => false,
			) );
		} elseif ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'depth'          => 2,
				'fallback_cb'    => false,
			) );
		}
		?>
	</nav>
</header>

<!-- Search Overlay -->
<div class="search-overlay" role="dialog" aria-label="<?php esc_attr_e( 'Search', 'guebel' ); ?>" aria-hidden="true">
	<button class="search-close" aria-label="<?php esc_attr_e( 'Close search', 'guebel' ); ?>">
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
	</button>
	<?php get_search_form(); ?>
</div>
