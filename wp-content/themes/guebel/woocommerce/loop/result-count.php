<?php
/**
 * Result Count
 *
 * Override: guebel/woocommerce/loop/result-count.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Guebel
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="woocommerce-result-count guebel-result-count section-text" role="status" aria-live="polite">
	<?php
	if ( 1 === intval( $total ) ) {
		esc_html_e( 'Showing the single result', 'guebel' );
	} elseif ( $total <= $per_page || -1 === $per_page ) {
		/* translators: %d: total results */
		printf( esc_html( _n( 'Showing all %d result', 'Showing all %d results', $total, 'guebel' ) ), intval( $total ) );
	} else {
		$first = ( $per_page * $current ) - $per_page + 1;
		$last  = min( $total, $per_page * $current );
		/* translators: 1: first result, 2: last result, 3: total results */
		printf( esc_html( _nx( 'Showing %1$d&ndash;%2$d of %3$d result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'guebel' ) ), intval( $first ), intval( $last ), intval( $total ) );
	}
	?>
</p>
