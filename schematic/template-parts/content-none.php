<?php
/**
 * Empty query.
 *
 * @package Schematic
 */
?>
<section class="content-none">
	<?php if ( is_search() ) : ?>
		<h2 class="content-none__title"><?php esc_html_e( 'No notes match that search.', 'schematic' ); ?></h2>
		<p><?php esc_html_e( 'Try a function name, a system, or a shorter phrase.', 'schematic' ); ?></p>
	<?php elseif ( is_home() ) : ?>
		<h2 class="content-none__title"><?php esc_html_e( 'No notes yet.', 'schematic' ); ?></h2>
		<p><?php esc_html_e( 'The first published note takes the lead position on this page.', 'schematic' ); ?></p>
		<?php if ( current_user_can( 'edit_posts' ) ) : ?>
			<p><a class="button" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php esc_html_e( 'Write a note', 'schematic' ); ?></a></p>
		<?php endif; ?>
	<?php else : ?>
		<h2 class="content-none__title"><?php esc_html_e( 'Nothing in this archive.', 'schematic' ); ?></h2>
	<?php endif; ?>
	<?php get_search_form(); ?>
</section>
