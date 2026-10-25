<?php
/**
 * Opal Care theme setup.
 *
 * @package opalcare
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads style.css on the site and in the editor, and registers the pattern category.
 */
function opalcare_setup() {
	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', 'opalcare_setup' );

/**
 * Enqueues style.css on the front end.
 */
function opalcare_enqueue_styles() {
	wp_enqueue_style( 'opalcare-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'opalcare_enqueue_styles' );

/**
 * Groups the theme's patterns under one heading in the inserter.
 */
function opalcare_register_pattern_category() {
	register_block_pattern_category(
		'opalcare',
		array( 'label' => __( 'Opal Care', 'opalcare' ) )
	);
}
add_action( 'init', 'opalcare_register_pattern_category' );
