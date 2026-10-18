<?php
/**
 * Heading outline for long notes.
 *
 * @package Schematic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Store or fetch the outline generated for a post.
 *
 * @param int        $post_id Post ID.
 * @param array|null $set     Outline to store.
 * @return array<int,array{level:int,id:string,text:string}>
 */
function schematic_outline( $post_id = 0, $set = null ) {
	static $store = array();

	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

	if ( is_array( $set ) ) {
		$store[ $post_id ] = $set;
	}

	return $store[ $post_id ] ?? array();
}

/**
 * Add stable ids to h2 and h3 elements and collect an outline.
 *
 * Headings inside `pre` blocks are left alone.
 *
 * @param string $html    Rendered content.
 * @param array  $outline Collected headings, passed by reference.
 * @return string
 */
function schematic_annotate_headings( $html, array &$outline ) {
	$parts = preg_split( '/(<pre\b[^>]*>.*?<\/pre>)/is', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( ! is_array( $parts ) ) {
		return $html;
	}

	$used = array();

	foreach ( $parts as $index => $part ) {
		if ( preg_match( '/^<pre\b/i', $part ) ) {
			continue;
		}

		$parts[ $index ] = preg_replace_callback(
			'/<h([23])([^>]*)>(.*?)<\/h\1>/is',
			function ( $matches ) use ( &$outline, &$used ) {
				$level = (int) $matches[1];
				$attrs = $matches[2];
				$inner = $matches[3];
				$text  = trim( html_entity_decode( wp_strip_all_tags( $inner ), ENT_QUOTES, 'UTF-8' ) );

				if ( '' === $text ) {
					return $matches[0];
				}

				if ( preg_match( '/\sid=(["\'])([^"\']+)\1/i', $attrs, $id_match ) ) {
					$id = $id_match[2];
				} else {
					$base = sanitize_title( $text );
					if ( '' === $base ) {
						$base = 'section';
					}
					$id = $base;
					$n  = 2;
					while ( isset( $used[ $id ] ) ) {
						$id = $base . '-' . $n;
						++$n;
					}
					$attrs .= ' id="' . esc_attr( $id ) . '"';
				}

				if ( ! preg_match( '/\stabindex=/i', $attrs ) ) {
					$attrs .= ' tabindex="-1"';
				}

				$used[ $id ] = true;
				$outline[]   = array(
					'level' => $level,
					'id'    => $id,
					'text'  => $text,
				);

				return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
			},
			$part
		);
	}

	return implode( '', $parts );
}

/**
 * Annotate the main post content once headings exist as HTML.
 *
 * @param string $content Content HTML.
 * @return string
 */
function schematic_content_headings( $content ) {
	if ( ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$post_id = (int) get_the_ID();
	static $done = array();

	if ( isset( $done[ $post_id ] ) ) {
		return $content;
	}

	$done[ $post_id ] = true;
	$outline          = array();
	$content          = schematic_annotate_headings( $content, $outline );
	schematic_outline( $post_id, $outline );

	return $content;
}
add_filter( 'the_content', 'schematic_content_headings', 12 );

/**
 * Render the outline nav.
 *
 * @param array<int,array{level:int,id:string,text:string}> $outline Outline.
 */
function schematic_render_toc( $outline ) {
	if ( count( $outline ) < 2 ) {
		return;
	}

	echo '<nav class="toc" aria-label="' . esc_attr__( 'On this page', 'schematic' ) . '">';
	echo '<details class="toc__details" open>';
	echo '<summary class="toc__label">' . esc_html__( 'On this page', 'schematic' ) . '</summary>';
	echo '<ol class="toc__list">';

	foreach ( $outline as $item ) {
		$classes = 'toc__item toc__item--h' . (int) $item['level'];
		printf(
			'<li class="%1$s"><a href="#%2$s">%3$s</a></li>',
			esc_attr( $classes ),
			esc_attr( $item['id'] ),
			esc_html( $item['text'] )
		);
	}

	echo '</ol></details></nav>';
}
