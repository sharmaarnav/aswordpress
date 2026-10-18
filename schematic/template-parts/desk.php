<?php
/**
 * Home rail: colophon, topics, and series.
 *
 * @package Schematic
 */

$topics = get_categories(
	array(
		'hide_empty' => true,
		'number'     => 12,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
$series = get_terms(
	array(
		'taxonomy'   => 'series',
		'hide_empty' => true,
		'number'     => 8,
	)
);
$about  = get_page_by_path( 'about' );
?>
<aside class="desk" aria-label="<?php esc_attr_e( 'Journal desk', 'schematic' ); ?>">
	<section>
		<h2 class="desk__label"><?php esc_html_e( 'Colophon', 'schematic' ); ?></h2>
		<p class="desk__text"><?php echo esc_html( schematic_tagline() ); ?></p>
		<p class="desk__links">
			<?php if ( $about ) : ?>
				<a href="<?php echo esc_url( get_permalink( $about ) ); ?>"><?php esc_html_e( 'About', 'schematic' ); ?></a>
				<span aria-hidden="true"> · </span>
			<?php endif; ?>
			<a href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>"><?php esc_html_e( 'Feed', 'schematic' ); ?></a>
		</p>
	</section>

	<?php if ( $topics ) : ?>
		<section>
			<h2 class="desk__label"><?php esc_html_e( 'Topics', 'schematic' ); ?></h2>
			<ul class="topic-list">
				<?php foreach ( $topics as $topic ) : ?>
					<?php if ( 'uncategorized' === $topic->slug ) { continue; } ?>
					<li>
						<a href="<?php echo esc_url( get_category_link( $topic ) ); ?>">
							<span><?php echo esc_html( $topic->name ); ?></span>
							<span class="topic-list__count"><?php echo esc_html( number_format_i18n( (int) $topic->count ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>

	<?php if ( $series && ! is_wp_error( $series ) ) : ?>
		<section>
			<h2 class="desk__label"><?php esc_html_e( 'Series', 'schematic' ); ?></h2>
			<ul class="series-list">
				<?php foreach ( $series as $term ) : ?>
					<li>
						<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
							<span><?php echo esc_html( $term->name ); ?></span>
							<span class="topic-list__count"><?php echo esc_html( number_format_i18n( (int) $term->count ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>
</aside>
