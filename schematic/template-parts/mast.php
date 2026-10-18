<?php
/**
 * Home masthead.
 *
 * @package Schematic
 */

global $wp_query;

$published = wp_count_posts( 'post' );
$count     = isset( $published->publish ) ? (int) $published->publish : 0;
$latest    = ! empty( $wp_query->posts ) ? $wp_query->posts[0] : null;
?>
<header class="mast">
	<div class="mast__copy">
		<p class="entry-kicker"><?php esc_html_e( 'Technical journal', 'schematic' ); ?></p>
		<h1 class="mast__title"><?php bloginfo( 'name' ); ?></h1>
		<p class="mast__deck"><?php echo esc_html( schematic_tagline() ); ?></p>
	</div>
	<aside class="mast__issue">
		<p><?php esc_html_e( 'Journal', 'schematic' ); ?></p>
		<p class="mast__count">
			<?php
			printf(
				esc_html(
					/* translators: %s: number of published notes */
					_n( '%s note', '%s notes', $count, 'schematic' )
				),
				esc_html( number_format_i18n( $count ) )
			);
			?>
		</p>
		<?php if ( $latest ) : ?>
			<p>
				<?php
				printf(
					/* translators: %s: date of the latest note */
					esc_html__( 'Updated %s', 'schematic' ),
					esc_html( get_the_date( '', $latest ) )
				);
				?>
			</p>
		<?php endif; ?>
	</aside>
</header>
