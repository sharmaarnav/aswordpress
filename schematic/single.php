<?php
/**
 * Single note.
 *
 * @package Schematic
 */

get_header();

while ( have_posts() ) :
	the_post();
	$stats  = schematic_reading_stats();
	$series = schematic_series_context();
	?>
	<article <?php post_class( 'entry' ); ?>>
		<?php
		$content = '';
		$has_toc = false;
		if ( ! post_password_required() ) {
			ob_start();
			the_content();
			wp_link_pages(
				array(
					'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'schematic' ),
					'after'  => '</nav>',
				)
			);
			$content = ob_get_clean();
			$outline = schematic_outline();
			$has_toc = count( $outline ) >= 2;
		}
		?>
		<div class="reading<?php echo $has_toc ? ' reading--toc' : ''; ?>">
		<header class="entry-header">
			<?php schematic_topic_kicker(); ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="entry-deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<?php if ( $series ) : ?>
				<?php schematic_series_label( $series ); ?>
			<?php endif; ?>
			<div class="byline">
				<span class="byline__author">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 36, '', '', array( 'class' => 'byline__avatar' ) ); ?>
					<a href="<?php echo esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
				</span>
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<?php if ( get_the_date( 'Y-m-d' ) !== get_the_modified_date( 'Y-m-d' ) ) : ?>
					<span>
						<?php
						printf(
							/* translators: %s: modified date */
							esc_html__( 'Updated %s', 'schematic' ),
							esc_html( get_the_modified_date() )
						);
						?>
					</span>
				<?php endif; ?>
				<span><?php echo esc_html( schematic_reading_time_label() ); ?></span>
				<span>
					<?php
					printf(
						/* translators: %s: word count */
						esc_html__( '%s words', 'schematic' ),
						esc_html( number_format_i18n( $stats['words'] ) )
					);
					?>
				</span>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="entry-figure">
				<?php the_post_thumbnail( 'schematic-lead' ); ?>
				<?php $caption = wp_get_attachment_caption( get_post_thumbnail_id() ); ?>
				<?php if ( $caption ) : ?>
					<figcaption><?php echo esc_html( $caption ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<?php if ( post_password_required() ) : ?>
			<div class="entry-locked">
				<?php echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core form. ?>
			</div>
		<?php else : ?>
			<?php if ( $has_toc ) : ?>
				<?php schematic_render_toc( $outline ); ?>
			<?php endif; ?>
			<div class="entry-column">
				<div class="entry-content">
					<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content. ?>
				</div>
				<footer class="entry-supplement">
						<?php $tags = get_the_tags(); ?>
						<?php if ( $tags ) : ?>
							<ul class="tag-list">
								<?php foreach ( $tags as $tag ) : ?>
									<li><a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>"><?php echo esc_html( $tag->name ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<?php
						if ( $series ) {
							schematic_series_nav( $series );
						} else {
							schematic_adjacent_nav();
						}
						?>
					</footer>
				</div>
			<?php endif; ?>
		</div>
	</article>

	<?php
	$related = schematic_related_posts();
	if ( $related ) :
		?>
		<aside class="related wrap" aria-label="<?php esc_attr_e( 'Related notes', 'schematic' ); ?>">
			<h2 class="desk__label"><?php esc_html_e( 'Related', 'schematic' ); ?></h2>
			<ul class="related__grid">
				<?php foreach ( $related as $related_post ) : ?>
					<li>
						<a class="related__link" href="<?php echo esc_url( get_permalink( $related_post ) ); ?>">
							<?php
							$related_cats = get_the_category( $related_post->ID );
							if ( $related_cats && 'uncategorized' !== $related_cats[0]->slug ) :
								?>
								<span class="related__topic"><?php echo esc_html( $related_cats[0]->name ); ?></span>
							<?php endif; ?>
							<span class="related__title"><?php echo esc_html( get_the_title( $related_post ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</aside>
	<?php endif; ?>

	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;

get_footer();
