<?php
/**
 * Search form.
 *
 * @package Schematic
 */

$schematic_search_id = wp_unique_id( 'schematic-search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $schematic_search_id ); ?>"><?php esc_html_e( 'Search notes', 'schematic' ); ?></label>
	<input id="<?php echo esc_attr( $schematic_search_id ); ?>" type="search" class="search-field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search notes', 'schematic' ); ?>">
	<button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'schematic' ); ?></button>
</form>
