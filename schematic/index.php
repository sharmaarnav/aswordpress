<?php
/**
 * Fallback index.
 *
 * @package Schematic
 */

get_header();
?>
<div class="wrap ledger">
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
