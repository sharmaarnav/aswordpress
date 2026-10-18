<?php
/**
 * Block patterns for technical notes.
 *
 * @package Schematic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register patterns.
 */
function schematic_register_patterns() {
	register_block_pattern_category(
		'schematic',
		array(
			'label' => __( 'Schematic', 'schematic' ),
		)
	);

	register_block_pattern(
		'schematic/note',
		array(
			'title'      => __( 'Note', 'schematic' ),
			'categories' => array( 'schematic' ),
			'content'    => '<!-- wp:group {"className":"is-style-schematic-note"} -->
<div class="wp-block-group is-style-schematic-note"><!-- wp:paragraph -->
<p><strong>' . esc_html__( 'Note.', 'schematic' ) . '</strong> ' . esc_html__( 'An assumption the rest of the piece relies on.', 'schematic' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'schematic/warning',
		array(
			'title'      => __( 'Warning', 'schematic' ),
			'categories' => array( 'schematic' ),
			'content'    => '<!-- wp:group {"className":"is-style-schematic-warning"} -->
<div class="wp-block-group is-style-schematic-warning"><!-- wp:paragraph -->
<p><strong>' . esc_html__( 'Warning.', 'schematic' ) . '</strong> ' . esc_html__( 'A failure mode worth naming before the reader copies this.', 'schematic' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'schematic/pull-quote',
		array(
			'title'      => __( 'Pull quote', 'schematic' ),
			'categories' => array( 'schematic' ),
			'content'    => '<!-- wp:quote {"className":"is-style-schematic-pull"} -->
<blockquote class="wp-block-quote is-style-schematic-pull"><!-- wp:paragraph -->
<p>' . esc_html__( 'The interface is a contract. Contracts that change every quarter are how systems rot.', 'schematic' ) . '</p>
<!-- /wp:paragraph --></blockquote>
<!-- /wp:quote -->',
		)
	);

	register_block_pattern(
		'schematic/definition',
		array(
			'title'      => __( 'Definition', 'schematic' ),
			'categories' => array( 'schematic' ),
			'content'    => '<!-- wp:columns {"className":"schematic-def"} -->
<div class="wp-block-columns schematic-def"><!-- wp:column {"width":"32%"} -->
<div class="wp-block-column" style="flex-basis:32%"><!-- wp:paragraph -->
<p><strong>' . esc_html__( 'Backpressure', 'schematic' ) . '</strong></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"68%"} -->
<div class="wp-block-column" style="flex-basis:68%"><!-- wp:paragraph -->
<p>' . esc_html__( 'A signal from a slow consumer that asks producers to wait, rather than buffering without bound.', 'schematic' ) . '</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
		)
	);
}
add_action( 'init', 'schematic_register_patterns' );
