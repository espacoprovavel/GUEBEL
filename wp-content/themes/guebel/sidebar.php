<?php
/**
 * The sidebar template.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( guebel_has_woocommerce() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
	$sidebar_id = 'sidebar-shop';
} else {
	$sidebar_id = 'sidebar-blog';
}

if ( ! is_active_sidebar( $sidebar_id ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'guebel' ); ?>">
	<?php dynamic_sidebar( $sidebar_id ); ?>
</aside>
