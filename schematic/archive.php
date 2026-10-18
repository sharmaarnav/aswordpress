<?php
/**
 * Topic, series, author, and date archives.
 *
 * @package Schematic
 */

get_header();
?>
<div class="wrap ledger">
	<header class="archive-header">
		<p class="entry-kicker"><?php echo esc_html( schematic_archive_kicker() ); ?></p>
		<h1 class="archive-header__title"><?php echo esc_html( schematic_archive_heading() ); ?></h1>
		<?php if ( is_author() ) : ?>
			<?php $author = get_queried_object(); ?>
			<?php if ( $author && ! empty( $author->description ) ) : ?>
				<p class="archive-header__deck"><?php echo esc_html( $author->description ); ?></p>
			<?php endif; ?>
		<?php else : ?>
			<?php the_archive_description( '<div class="archive-header__deck">', '</div>' ); ?>
		<?php endif; ?>
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
