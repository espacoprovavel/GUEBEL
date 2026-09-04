<?php
/**
 * Show options for ordering
 *
 * Override: guebel/woocommerce/loop/orderby.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form class="woocommerce-ordering guebel-ordering" method="get">
	<label for="guebel-orderby" class="screen-reader-text"><?php esc_html_e( 'Shop order', 'guebel' ); ?></label>
	<select name="orderby" id="guebel-orderby" class="orderby guebel-orderby-select" aria-label="<?php esc_attr_e( 'Shop order', 'guebel' ); ?>">
		<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
			<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>>
				<?php echo esc_html( $name ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<input type="hidden" name="paged" value="1" />
	<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
</form>
