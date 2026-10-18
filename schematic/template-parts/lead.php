<?php
/**
 * Lead note on the journal home.
 *
 * @package Schematic
 */

$category = schematic_primary_category();
?>
<article <?php post_class( 'lead' . ( has_post_thumbnail() ? ' lead--media' : ' lead--text' ) ); ?>>
	<div class="lead__copy">
		<p class="lead__kicker">
			<?php if ( is_sticky() ) : ?>
				<span class="lead__pin"><?php esc_html_e( 'Pinned', 'schematic' ); ?></span>
			<?php endif; ?>
			<?php if ( $category ) : ?>
				<a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
			<?php elseif ( ! is_sticky() ) : ?>
				<?php esc_html_e( 'Latest', 'schematic' ); ?>
			<?php endif; ?>
		</p>
		<h2 class="lead__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php $deck = get_the_excerpt(); ?>
		<?php if ( $deck ) : ?>
			<p class="lead__deck"><?php echo esc_html( wp_trim_words( $deck, 36, '…' ) ); ?></p>
		<?php endif; ?>
		<p class="lead__meta">
			<span><?php echo esc_html( schematic_reading_time_label() ); ?></span>
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</p>
		<p class="lead__more"><a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read note', 'schematic' ); ?></a></p>
	</div>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="lead__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'schematic-lead' ); ?>
		</a>
	<?php endif; ?>
</article>
