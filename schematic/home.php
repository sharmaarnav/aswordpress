<?php
/**
 * Journal home: mast, lead note, and the index.
 *
 * @package Schematic
 */

get_header();

$paged = max( 1, (int) get_query_var( 'paged' ) );
?>
<div class="wrap">
	<?php get_template_part( 'template-parts/mast' ); ?>

	<?php if ( have_posts() ) : ?>
		<div class="journal">
			<div class="journal__main">
				<?php
				$number = ( $paged - 1 ) * schematic_per_page();
				if ( ! is_paged() ) {
					the_post();
					$number = 1;
					get_template_part( 'template-parts/lead' );
				}

				if ( have_posts() ) :
					echo '<ol class="index-list">';
					while ( have_posts() ) :
						the_post();
						++$number;
						get_template_part(
							'template-parts/index',
							'item',
							array( 'number' => $number )
						);
					endwhile;
					echo '</ol>';
				endif;

				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => __( 'Previous', 'schematic' ),
						'next_text' => __( 'Next', 'schematic' ),
					)
				);
				?>
			</div>
			<?php get_template_part( 'template-parts/desk' ); ?>
		</div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
