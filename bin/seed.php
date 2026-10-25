<?php
/**
 * Seeds Opal Care's starter content. Run on the site:
 *   wp eval-file bin/seed.php
 * Safe to run again: pages are found by slug and updated, nothing is duplicated.
 * The Home page is the four home patterns joined; the other pages come from content/*.html.
 *
 * @package opal-care
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

if ( 'opal-care' !== get_stylesheet() ) {
	WP_CLI::error( 'Activate the opalcare theme first.' );
}

/**
 * Returns a registered pattern's block markup.
 *
 * @param string $slug Pattern slug.
 * @return string
 */
function opalcare_seed_pattern( $slug ) {
	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );
	if ( ! $pattern ) {
		WP_CLI::error( "Pattern $slug is not registered." );
	}
	// WP-CLI does not see the proxy's https, so give the theme's URLs the scheme the site is served on.
	$root = get_theme_root_uri();
	return str_replace( $root, set_url_scheme( $root, wp_parse_url( home_url(), PHP_URL_SCHEME ) ), $pattern['content'] );
}

/**
 * Creates or updates a page by slug and returns its ID.
 *
 * @param string $slug     Page slug.
 * @param string $title    Page title.
 * @param string $content  Block markup.
 * @param string $template Page template slug, or empty.
 * @return int
 */
function opalcare_seed_page( $slug, $title, $content, $template = '' ) {
	$existing = get_page_by_path( $slug );
	$args     = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => $content,
	);
	if ( $existing ) {
		$args['ID'] = $existing->ID;
		$id         = wp_update_post( $args, true );
	} else {
		$id = wp_insert_post( $args, true );
	}
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	if ( $template ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	return $id;
}

$dir  = dirname( __DIR__ ) . '/content/';
$home = implode( "\n\n", array_map( 'opalcare_seed_pattern', array( 'opalcare/hero', 'opalcare/check', 'opalcare/questions', 'opalcare/close' ) ) );

$home_id = opalcare_seed_page( 'home', 'Home', $home, 'page-landing' );
opalcare_seed_page( 'how-it-works', 'How it works', file_get_contents( $dir . 'how-it-works.html' ) );
opalcare_seed_page( 'pricing', 'Pricing', file_get_contents( $dir . 'pricing.html' ) );
$privacy_id = opalcare_seed_page( 'privacy', 'Privacy', file_get_contents( $dir . 'privacy.html' ) );
opalcare_seed_page( 'terms', 'Terms', file_get_contents( $dir . 'terms.html' ) );
opalcare_seed_page( 'contact', 'Contact', file_get_contents( $dir . 'contact.html' ) );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
update_option( 'wp_page_for_privacy_policy', $privacy_id );
update_option( 'blogdescription', 'Opal looks after your WordPress site every day and tells you in plain words what she found.' );

$sample = get_page_by_path( 'sample-page' );
if ( $sample ) {
	wp_delete_post( $sample->ID, true );
}

// The menu: a Navigation post the header's Navigation block picks up. Opal edits it like any menu.
$nav = static function ( $label, $url, $class = '' ) {
	$attrs = array(
		'label' => $label,
		'url'   => $url,
		'kind'  => 'custom',
	);
	if ( $class ) {
		$attrs['className'] = $class;
	}
	return '<!-- wp:navigation-link ' . wp_json_encode( $attrs ) . ' /-->';
};
$menu = implode(
	"\n\n",
	array(
		$nav( 'How it works', home_url( '/how-it-works/' ) ),
		$nav( 'Pricing', home_url( '/pricing/' ) ),
		$nav( 'Sign in', 'https://myopal.dev/app/' ),
		$nav( 'Start free', 'https://myopal.dev/app/', 'opal-cta' ),
	)
);

$existing_nav = get_posts(
	array(
		'post_type'   => 'wp_navigation',
		'post_status' => 'any',
		'name'        => 'primary',
		'numberposts' => 1,
	)
);
$nav_args     = array(
	'post_type'    => 'wp_navigation',
	'post_status'  => 'publish',
	'post_name'    => 'primary',
	'post_title'   => 'Primary',
	'post_content' => $menu,
);
if ( $existing_nav ) {
	$nav_args['ID'] = $existing_nav[0]->ID;
	wp_update_post( $nav_args );
} else {
	wp_insert_post( $nav_args );
}

// The logo: the opal stone, imported once from the theme and set as both the Site Logo (the header) and the Site Icon
// (the favicon), so Opal can swap either from the editor later.
if ( ! get_option( 'site_logo' ) ) {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$logo_id = media_sideload_image( get_theme_file_uri( 'assets/images/opal-stone-512.png' ), 0, 'Opal Care', 'id' );
	if ( is_wp_error( $logo_id ) ) {
		WP_CLI::warning( 'Logo not imported: ' . $logo_id->get_error_message() );
	} else {
		update_post_meta( $logo_id, '_wp_attachment_image_alt', 'Opal Care' );
		update_option( 'site_logo', $logo_id );
		update_option( 'site_icon', $logo_id );
	}
}

WP_CLI::success( 'Opal Care starter content is in place. Home page ID ' . $home_id );
