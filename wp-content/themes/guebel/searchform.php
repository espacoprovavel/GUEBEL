<?php
/**
 * Custom search form.
 *
 * @package Guebel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="visually-hidden" for="search-field-<?php echo esc_attr( wp_unique_id() ); ?>">
		<?php esc_html_e( 'Search', 'guebel' ); ?>
	</label>
	<input
		type="search"
		id="search-field-<?php echo esc_attr( wp_unique_id() ); ?>"
		class="search-field form-input"
		placeholder="<?php esc_attr_e( 'Search&hellip;', 'guebel' ); ?>"
		value="<?php echo get_search_query(); ?>"
		name="s"
	>
	<button type="submit" class="search-submit btn btn--primary">
		<?php esc_html_e( 'Search', 'guebel' ); ?>
	</button>
</form>
