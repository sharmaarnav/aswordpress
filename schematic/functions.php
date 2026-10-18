<?php
/**
 * Schematic theme bootstrap.
 *
 * @package Schematic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SCHEMATIC_VERSION', '1.0.0' );

require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/toc.php';
require get_template_directory() . '/inc/block-patterns.php';
require get_template_directory() . '/inc/schema.php';
