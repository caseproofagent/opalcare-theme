<?php
/**
 * Opal Care theme setup.
 *
 * @package opal-care
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
		'opal-care',
		array( 'label' => __( 'Opal Care', 'opal-care' ) )
	);
}
add_action( 'init', 'opalcare_register_pattern_category' );

/**
 * Preloads the two body fonts so the first paint is not waiting on them.
 */
function opalcare_preload_fonts() {
	foreach ( array( 'fraunces', 'dm-sans' ) as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( "assets/fonts/{$font}.woff2" ) )
		);
	}
}
add_action( 'wp_head', 'opalcare_preload_fonts', 1 );

/**
 * Prints a meta description: the tagline on the home page, the excerpt on a page or post.
 * Opal changes the tagline in Settings, or a page's excerpt in the editor.
 */
function opalcare_meta_description() {
	$text = '';
	if ( is_front_page() ) {
		$text = get_bloginfo( 'description' );
	} elseif ( is_singular() ) {
		$text = get_the_excerpt();
	}
	$text = trim( wp_strip_all_tags( $text ) );
	if ( '' !== $text ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_trim_words( $text, 30, '' ) ) );
	}
}
add_action( 'wp_head', 'opalcare_meta_description', 1 );

/**
 * Draws the animated opal in place of the Site Logo image, but only while the Site Logo is the theme's own default.
 * If Opal (or anyone) picks a different logo in the editor, that image is shown instead.
 *
 * @param string $content The Site Logo block's HTML.
 * @return string
 */
function opalcare_animated_logo( $content ) {
	$logo_id = (int) get_option( 'site_logo' );
	if ( ! $logo_id || ! get_post_meta( $logo_id, '_opalcare_default_logo', true ) ) {
		return $content;
	}
	$svg = file_get_contents( get_theme_file_path( 'assets/images/opal-stone-c.svg' ) );
	if ( false === $svg ) {
		return $content;
	}
	return preg_replace( '/<img\b[^>]*>/', $svg, $content, 1 );
}
add_filter( 'render_block_core/site-logo', 'opalcare_animated_logo' );

/**
 * Registers the AI readiness check block (blocks/readiness-check). It needs no build step.
 */
function opalcare_register_blocks() {
	register_block_type( get_theme_file_path( 'blocks/readiness-check' ) );
}
add_action( 'init', 'opalcare_register_blocks' );

require get_theme_file_path( 'inc/agent-surfaces.php' );
require get_theme_file_path( 'inc/security-headers.php' );
