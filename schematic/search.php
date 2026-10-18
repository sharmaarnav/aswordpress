<?php
/**
 * Search results.
 *
 * @package Schematic
 */

get_header();
global $wp_query;
?>
<div class="wrap ledger">
	<header class="archive-header">
		<p class="entry-kicker"><?php esc_html_e( 'Search', 'schematic' ); ?></p>
		<h1 class="archive-header__title">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Results for “%s”', 'schematic' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
		<p class="archive-header__deck">
			<?php
			printf(
				esc_html(
					/* translators: %s: result count */
					_n( '%s note', '%s notes', (int) $wp_query->found_posts, 'schematic' )
				),
				esc_html( number_format_i18n( (int) $wp_query->found_posts ) )
			);
			?>
		</p>
	</header>

	<?php if ( have_posts() ) : ?>
		<ol class="index-list">
			<?php
			$number = ( max( 1, (int) get_query_var( 'paged' ) ) - 1 ) * schematic_per_page();
			while ( have_posts() ) :
				the_post();
				++$number;
				get_template_part(
					'template-parts/index',
					'item',
					array( 'number' => $number )
				);
			endwhile;
			?>
		</ol>
		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => __( 'Previous', 'schematic' ),
				'next_text' => __( 'Next', 'schematic' ),
			)
		);
		?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
