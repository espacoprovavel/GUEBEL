<?php
/**
 * WooCommerce wrapper template.
 *
 * Falls back to the default WooCommerce content wrapper. When Elementor Pro
 * Theme Builder has a product-archive or product template assigned, those
 * take precedence through the Elementor locations system (see inc/elementor.php).
 *
 * @package Guebel
 * @since   1.0.0
 */

get_header();
?>

<div class="page-wrap">
	<?php woocommerce_content(); ?>
</div>

<?php get_footer(); ?>
