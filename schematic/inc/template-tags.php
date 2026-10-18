<?php
/**
 * Template helpers.
 *
 * @package Schematic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Square trace used as the wordmark.
 *
 * @return string
 */
function schematic_mark() {
	return '<svg class="mark" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><rect x="1.5" y="1.5" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.25"/><path d="M3 12h6.5l3.2-6 3.6 12 2.4-6H21" fill="none" stroke="currentColor" stroke-width="1.25"/></svg>';
}

/**
 * Site description, with a journal-specific fallback for the default tagline.
 *
 * @return string
 */
function schematic_tagline() {
	$raw = (string) get_option( 'blogdescription' );

	if ( '' === $raw || 'Just another WordPress site' === $raw ) {
		return __( 'Field notes on software, systems, and the craft of explanation.', 'schematic' );
	}

	return $raw;
}

/**
 * Word count and reading time for a post.
 *
 * @param int $post_id Post ID.
 * @return array{words:int,minutes:int}
 */
function schematic_reading_stats( $post_id = 0 ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return array(
			'words'   => 0,
			'minutes' => 1,
		);
	}

	$text  = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
	$parts = preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
	$words = is_array( $parts ) ? count( $parts ) : 0;

	return array(
		'words'   => $words,
		'minutes' => max( 1, (int) ceil( $words / 230 ) ),
	);
}

/**
 * Localized reading-time label.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function schematic_reading_time_label( $post_id = 0 ) {
	$minutes = schematic_reading_stats( $post_id )['minutes'];

	return sprintf(
		/* translators: %d: estimated minutes */
		_n( '%d min read', '%d min read', $minutes, 'schematic' ),
		$minutes
	);
}

/**
 * First real category, ignoring the default Uncategorized bucket.
 *
 * @return WP_Term|null
 */
function schematic_primary_category() {
	$categories = get_the_category();
	if ( ! $categories ) {
		return null;
	}

	foreach ( $categories as $category ) {
		if ( 'uncategorized' !== $category->slug ) {
			return $category;
		}
	}

	return null;
}

/**
 * Kicker linking to the primary topic.
 */
function schematic_topic_kicker() {
	$category = schematic_primary_category();
	if ( ! $category ) {
		return;
	}

	printf(
		'<a class="entry-kicker" href="%1$s">%2$s</a>',
		esc_url( get_category_link( $category ) ),
		esc_html( $category->name )
	);
}

/**
 * Primary navigation, or a short fallback of pages.
 */
function schematic_primary_nav() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'primary-nav__list',
				'fallback_cb'    => false,
				'depth'          => 2,
			)
		);
		return;
	}

	echo '<ul class="primary-nav__list">';
	printf(
		'<li><a href="%1$s">%2$s</a></li>',
		esc_url( home_url( '/' ) ),
		esc_html__( 'Index', 'schematic' )
	);

	$pages = get_pages(
		array(
			'sort_column' => 'menu_order,post_title',
			'number'      => 5,
		)
	);

	foreach ( $pages as $page ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( get_permalink( $page ) ),
			esc_html( get_the_title( $page ) )
		);
	}

	echo '</ul>';
}

/**
 * Series membership for the current post, ordered oldest first.
 *
 * @param int $post_id Post ID.
 * @return array<string,mixed>|null
 */
function schematic_series_context( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$terms   = get_the_terms( $post_id, 'series' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}

	$term = $terms[0];
	$ids  = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 100,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tax_query'      => array(
				array(
					'taxonomy' => 'series',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
		)
	);

	$index = array_search( $post_id, $ids, true );
	if ( false === $index ) {
		return null;
	}

	return array(
		'term'  => $term,
		'ids'   => $ids,
		'index' => (int) $index,
		'total' => count( $ids ),
		'prev'  => $index > 0 ? (int) $ids[ $index - 1 ] : 0,
		'next'  => $index < count( $ids ) - 1 ? (int) $ids[ $index + 1 ] : 0,
	);
}

/**
 * "Part 2 of 5" label.
 *
 * @param array<string,mixed> $series Series context.
 */
function schematic_series_label( $series ) {
	printf(
		'<p class="series-label"><a href="%1$s">%2$s</a> · %3$s</p>',
		esc_url( get_term_link( $series['term'] ) ),
		esc_html( $series['term']->name ),
		esc_html(
			sprintf(
				/* translators: 1: part number, 2: number of parts */
				__( 'Part %1$d of %2$d', 'schematic' ),
				$series['index'] + 1,
				$series['total']
			)
		)
	);
}

/**
 * Previous and next notes inside a series.
 *
 * @param array<string,mixed> $series Series context.
 */
function schematic_series_nav( $series ) {
	if ( ! $series['prev'] && ! $series['next'] ) {
		return;
	}

	echo '<nav class="series-nav" aria-label="' . esc_attr__( 'Series', 'schematic' ) . '">';

	if ( $series['prev'] ) {
		printf(
			'<a class="series-nav__link" href="%1$s"><span class="series-nav__dir">%2$s</span><span class="series-nav__title">%3$s</span></a>',
			esc_url( get_permalink( $series['prev'] ) ),
			esc_html__( 'Previous part', 'schematic' ),
			esc_html( get_the_title( $series['prev'] ) )
		);
	} else {
		echo '<span></span>';
	}

	if ( $series['next'] ) {
		printf(
			'<a class="series-nav__link series-nav__link--next" href="%1$s"><span class="series-nav__dir">%2$s</span><span class="series-nav__title">%3$s</span></a>',
			esc_url( get_permalink( $series['next'] ) ),
			esc_html__( 'Next part', 'schematic' ),
			esc_html( get_the_title( $series['next'] ) )
		);
	}

	echo '</nav>';
}

/**
 * Adjacent posts when the note is not part of a series.
 */
function schematic_adjacent_nav() {
	$prev = get_previous_post();
	$next = get_next_post();

	if ( ! $prev && ! $next ) {
		return;
	}

	echo '<nav class="series-nav" aria-label="' . esc_attr__( 'More notes', 'schematic' ) . '">';

	if ( $prev ) {
		printf(
			'<a class="series-nav__link" href="%1$s"><span class="series-nav__dir">%2$s</span><span class="series-nav__title">%3$s</span></a>',
			esc_url( get_permalink( $prev ) ),
			esc_html__( 'Previous note', 'schematic' ),
			esc_html( get_the_title( $prev ) )
		);
	} else {
		echo '<span></span>';
	}

	if ( $next ) {
		printf(
			'<a class="series-nav__link series-nav__link--next" href="%1$s"><span class="series-nav__dir">%2$s</span><span class="series-nav__title">%3$s</span></a>',
			esc_url( get_permalink( $next ) ),
			esc_html__( 'Next note', 'schematic' ),
			esc_html( get_the_title( $next ) )
		);
	}

	echo '</nav>';
}

/**
 * Three later notes that share a topic.
 *
 * @param int $post_id Post ID.
 * @return WP_Post[]
 */
function schematic_related_posts( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$cats    = wp_get_post_categories( $post_id );

	if ( ! $cats ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( $post_id ),
			'category__in'        => $cats,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
}

/**
 * Archive eyebrow.
 *
 * @return string
 */
function schematic_archive_kicker() {
	if ( is_category() ) {
		return __( 'Topic', 'schematic' );
	}
	if ( is_tag() ) {
		return __( 'Tag', 'schematic' );
	}
	if ( is_tax( 'series' ) ) {
		return __( 'Series', 'schematic' );
	}
	if ( is_author() ) {
		return __( 'Author', 'schematic' );
	}
	if ( is_year() ) {
		return __( 'Year', 'schematic' );
	}
	if ( is_month() ) {
		return __( 'Month', 'schematic' );
	}
	if ( is_day() ) {
		return __( 'Day', 'schematic' );
	}

	return __( 'Archive', 'schematic' );
}

/**
 * Archive heading without the core "Category:" prefix.
 *
 * @return string
 */
function schematic_archive_heading() {
	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}

	if ( is_author() ) {
		$author = get_queried_object();
		return ( $author && isset( $author->display_name ) ) ? $author->display_name : '';
	}

	if ( is_year() ) {
		return get_the_date( 'Y' );
	}

	if ( is_month() ) {
		return get_the_date( 'F Y' );
	}

	if ( is_day() ) {
		return get_the_date();
	}

	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}

	return wp_strip_all_tags( get_the_archive_title() );
}

/**
 * Zero-padded index number until 100.
 *
 * @param int $number Position.
 * @return string
 */
function schematic_index_number( $number ) {
	$number = (int) $number;
	if ( $number < 100 ) {
		return sprintf( '%02d', $number );
	}

	return (string) $number;
}

/**
 * Single comment markup. The walker closes the list item.
 *
 * @param WP_Comment $comment Comment object.
 * @param array      $args    Walker args.
 * @param int        $depth   Nesting depth.
 */
function schematic_comment( $comment, $args, $depth ) {
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment' ); ?>>
		<article class="comment__body">
			<header class="comment__meta">
				<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
				<span class="comment__author"><?php comment_author_link(); ?></span>
				<a class="comment__time" href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
					<time datetime="<?php echo esc_attr( get_comment_time( DATE_W3C ) ); ?>">
						<?php echo esc_html( get_comment_date() ); ?>
					</time>
				</a>
			</header>
			<?php if ( '0' === (string) $comment->comment_approved ) : ?>
				<p class="comment__moderation"><?php esc_html_e( 'Your note is waiting for review.', 'schematic' ); ?></p>
			<?php endif; ?>
			<div class="comment__content">
				<?php comment_text(); ?>
			</div>
			<?php
			comment_reply_link(
				array_merge(
					$args,
					array(
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<div class="comment-reply">',
						'after'     => '</div>',
					)
				)
			);
			?>
		</article>
	<?php
}

/**
 * Posts-per-page for the current query, with a safe fallback.
 *
 * @return int
 */
function schematic_per_page() {
	$per = (int) get_query_var( 'posts_per_page' );
	if ( $per < 1 ) {
		$per = (int) get_option( 'posts_per_page' );
	}

	return max( 1, $per );
}
