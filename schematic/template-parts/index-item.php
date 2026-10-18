<?php
/**
 * One row in the journal index.
 *
 * @package Schematic
 */

$number   = isset( $args['number'] ) ? (int) $args['number'] : 0;
$category = schematic_primary_category();
$deck     = get_the_excerpt();
?>
<li class="index-item">
	<article <?php post_class(); ?>>
		<a class="index-item__link" href="<?php the_permalink(); ?>">
			<span class="index-item__num"><?php echo esc_html( schematic_index_number( $number ) ); ?></span>
			<span class="index-item__topic">
				<?php
				if ( is_sticky() && ( is_home() || is_front_page() ) ) {
					esc_html_e( 'Pinned', 'schematic' );
					if ( $category ) {
						echo ' · ' . esc_html( $category->name );
					}
				} elseif ( $category ) {
					echo esc_html( $category->name );
				} else {
					esc_html_e( 'Note', 'schematic' );
				}
				?>
			</span>
			<span class="index-item__title"><?php the_title(); ?></span>
			<?php if ( $deck ) : ?>
				<span class="index-item__excerpt"><?php echo esc_html( wp_trim_words( $deck, 24, '…' ) ); ?></span>
			<?php endif; ?>
			<span class="index-item__meta">
				<span><?php echo esc_html( schematic_reading_time_label() ); ?></span>
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			</span>
		</a>
	</article>
</li>
