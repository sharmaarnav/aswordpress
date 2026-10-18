<?php
/**
 * Theme supports, menus, and assets.
 *
 * @package Schematic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and menus.
 */
function schematic_setup() {
	load_theme_textdomain( 'schematic', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 80,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary', 'schematic' ),
			'footer'  => __( 'Footer', 'schematic' ),
		)
	);

	add_image_size( 'schematic-lead', 1600, 1000, true );
}
add_action( 'after_setup_theme', 'schematic_setup' );

/**
 * Embedded media width for the reading column.
 */
function schematic_content_width() {
	$GLOBALS['content_width'] = 720;
}
add_action( 'after_setup_theme', 'schematic_content_width', 0 );

/**
 * Footer widget region for a short colophon or links.
 */
function schematic_widgets() {
	register_sidebar(
		array(
			'name'          => __( 'Footer notes', 'schematic' ),
			'id'            => 'footer',
			'description'   => __( 'Optional notes along the bottom of every page.', 'schematic' ),
			'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="footer-widget__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'schematic_widgets' );

/**
 * Series taxonomy so a technical sequence can stay in order.
 */
function schematic_register_series() {
	register_taxonomy(
		'series',
		array( 'post' ),
		array(
			'labels'            => array(
				'name'          => __( 'Series', 'schematic' ),
				'singular_name' => __( 'Series', 'schematic' ),
				'search_items'  => __( 'Search series', 'schematic' ),
				'all_items'     => __( 'All series', 'schematic' ),
				'edit_item'     => __( 'Edit series', 'schematic' ),
				'update_item'   => __( 'Update series', 'schematic' ),
				'add_new_item'  => __( 'Add new series', 'schematic' ),
				'new_item_name' => __( 'New series name', 'schematic' ),
				'menu_name'     => __( 'Series', 'schematic' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'series' ),
		)
	);
}
add_action( 'init', 'schematic_register_series' );

/**
 * Read a series from first note to last.
 *
 * @param WP_Query $query Main query.
 */
function schematic_series_order( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_tax( 'series' ) ) {
		return;
	}

	$query->set( 'orderby', 'date' );
	$query->set( 'order', 'ASC' );
}
add_action( 'pre_get_posts', 'schematic_series_order' );

/**
 * Front-end and editor assets.
 */
function schematic_enqueue() {
	wp_enqueue_style( 'schematic', get_template_directory_uri() . '/style.css', array(), SCHEMATIC_VERSION );

	if ( get_stylesheet() !== get_template() ) {
		wp_enqueue_style( 'schematic-child', get_stylesheet_uri(), array( 'schematic' ), SCHEMATIC_VERSION );
	}

	wp_enqueue_script( 'schematic', get_template_directory_uri() . '/assets/js/theme.js', array(), SCHEMATIC_VERSION, true );
	wp_localize_script(
		'schematic',
		'schematicL10n',
		array(
			'menu'   => __( 'Menu', 'schematic' ),
			'close'  => __( 'Close', 'schematic' ),
			'copy'   => __( 'Copy', 'schematic' ),
			'copied' => __( 'Copied', 'schematic' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'schematic_enqueue' );

/**
 * Block styles used by the note and warning patterns.
 */
function schematic_block_styles() {
	register_block_style(
		'core/group',
		array(
			'name'  => 'schematic-note',
			'label' => __( 'Note', 'schematic' ),
		)
	);
	register_block_style(
		'core/group',
		array(
			'name'  => 'schematic-warning',
			'label' => __( 'Warning', 'schematic' ),
		)
	);
	register_block_style(
		'core/quote',
		array(
			'name'  => 'schematic-pull',
			'label' => __( 'Pull quote', 'schematic' ),
		)
	);
}
add_action( 'init', 'schematic_block_styles' );

/**
 * Editorial title separator.
 *
 * @return string
 */
function schematic_title_separator() {
	return '·';
}
add_filter( 'document_title_separator', 'schematic_title_separator' );

/**
 * Keep index excerpts short enough for the ledger.
 *
 * @return int
 */
function schematic_excerpt_length() {
	return 32;
}
add_filter( 'excerpt_length', 'schematic_excerpt_length' );

/**
 * Ellipsis instead of a bracketed more-link.
 *
 * @return string
 */
function schematic_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'schematic_excerpt_more' );

/**
 * @param string[] $classes Body classes.
 * @return string[]
 */
function schematic_body_classes( $classes ) {
	$classes[] = 'schematic';

	if ( is_singular() ) {
		$classes[] = 'is-singular';
	}

	return $classes;
}
add_filter( 'body_class', 'schematic_body_classes' );

/**
 * Drop legacy discovery tags the journal does not use.
 */
function schematic_cleanup_head() {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'schematic_cleanup_head' );

/**
 * Fallback mark when the site has no Site Icon.
 */
function schematic_favicon() {
	if ( has_site_icon() ) {
		return;
	}

	printf(
		'<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
		esc_url( get_template_directory_uri() . '/assets/images/icon.svg' )
	);
}
add_action( 'wp_head', 'schematic_favicon', 2 );
