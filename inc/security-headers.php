<?php
/**
 * The security headers Opal's own readiness check looks for, on the public pages.
 *
 * Opal changes any of them with the opalcare_security_headers filter.
 *
 * @package opal-care
 */

/**
 * Sends Strict-Transport-Security and Content-Security-Policy. Referrer-Policy, X-Frame-Options and
 * X-Content-Type-Options already come from the host.
 */
function opalcare_security_headers() {
	if ( is_admin() || is_customize_preview() || ! is_ssl() ) {
		return;
	}
	$endpoint = apply_filters( 'opalcare_readiness_endpoint', 'https://opalagent.app/api/readiness' );
	$origin   = wp_parse_url( $endpoint, PHP_URL_SCHEME ) . '://' . wp_parse_url( $endpoint, PHP_URL_HOST );
	$csp      = array(
		"default-src 'self'",
		"script-src 'self' 'unsafe-inline' https://static.cloudflareinsights.com",
		"worker-src 'self' blob:",
		"style-src 'self' 'unsafe-inline'",
		"img-src 'self' data: https:",
		"font-src 'self' data:",
		"connect-src 'self' https://cloudflareinsights.com " . $origin,
		"form-action 'self'",
		"base-uri 'self'",
		"frame-ancestors 'self'",
		"object-src 'none'",
	);
	$headers  = apply_filters(
		'opalcare_security_headers',
		array(
			'Strict-Transport-Security' => 'max-age=31536000',
			'Content-Security-Policy'   => implode( '; ', $csp ),
		)
	);
	foreach ( $headers as $name => $value ) {
		header( $name . ': ' . $value );
	}
}
add_action( 'send_headers', 'opalcare_security_headers' );
