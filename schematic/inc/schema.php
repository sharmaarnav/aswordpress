<?php
/**
 * Article metadata for readers and crawlers.
 *
 * Skipped when a dedicated SEO plugin is already printing tags.
 *
 * @package Schematic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether another plugin owns social and schema tags.
 *
 * @return bool
 */
function schematic_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Plain summary for the current singular item.
 *
 * @return string
 */
function schematic_summary() {
	if ( has_excerpt() ) {
		return wp_strip_all_tags( get_the_excerpt() );
	}

	return wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ), 36, '…' );
}

/**
 * Open Graph and a meta description.
 */
function schematic_social_meta() {
	if ( schematic_seo_plugin_active() || ! is_singular() ) {
		return;
	}

	$summary = schematic_summary();
	if ( $summary ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $summary ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $summary ) );
	}

	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( get_the_title() ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( get_permalink() ) );
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";

	if ( has_post_thumbnail() ) {
		$image = wp_get_attachment_image_url( get_post_thumbnail_id(), 'large' );
		if ( $image ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		}
	}
}
add_action( 'wp_head', 'schematic_social_meta', 4 );

/**
 * BlogPosting JSON-LD on single notes.
 */
function schematic_json_ld() {
	if ( schematic_seo_plugin_active() || ! is_singular( 'post' ) ) {
		return;
	}

	$stats = schematic_reading_stats();
	$data  = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'headline'         => get_the_title(),
		'datePublished'    => get_the_date( DATE_W3C ),
		'dateModified'     => get_the_modified_date( DATE_W3C ),
		'wordCount'        => $stats['words'],
		'description'      => schematic_summary(),
		'mainEntityOfPage' => get_permalink(),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author(),
			'url'   => get_author_posts_url( (int) get_the_author_meta( 'ID' ) ),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
		),
	);

	$image = has_post_thumbnail() ? wp_get_attachment_image_url( get_post_thumbnail_id(), 'large' ) : '';
	if ( $image ) {
		$data['image'] = array( $image );
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'schematic_json_ld', 5 );
