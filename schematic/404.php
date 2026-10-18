<?php
/**
 * Missing page.
 *
 * @package Schematic
 */

get_header();
?>
<div class="wrap error-404">
	<p class="error-404__code">404</p>
	<h1 class="error-404__title"><?php esc_html_e( 'This address is not in the index.', 'schematic' ); ?></h1>
	<p class="error-404__deck"><?php esc_html_e( 'The note may have moved. Search the journal, or return to the latest entries.', 'schematic' ); ?></p>
	<?php get_search_form(); ?>
	<p class="error-404__home"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the index', 'schematic' ); ?></a></p>
</div>
<?php
get_footer();
