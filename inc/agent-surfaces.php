<?php
/**
 * What an AI assistant reads: JSON-LD on the home page, and a markdown copy of each page.
 *
 * The markdown copy is served at the page's address with .md on the end (the home page is /index.md),
 * and on the page's own address when the request says Accept: text/markdown.
 *
 * @package opal-care
 */

/**
 * Prints the JSON-LD for the home page: the organization, the website, and the Opal Connect plugin.
 * Names and descriptions come from Settings, so Opal changes them there.
 */
function opalcare_json_ld() {
	if ( ! is_front_page() ) {
		return;
	}
	$name = get_bloginfo( 'name' );
	$home = home_url( '/' );
	$desc = get_bloginfo( 'description' );
	$org  = array(
		'@type' => 'Organization',
		'@id'   => $home . '#organization',
		'name'  => $name,
		'url'   => $home,
	);
	$icon = get_site_icon_url( 512 );
	if ( $icon ) {
		$org['logo'] = $icon;
	}
	$same_as = opalcare_same_as_links();
	if ( $same_as ) {
		$org['sameAs'] = $same_as;
	}
	$graph = array(
		$org,
		array(
			'@type'       => 'WebSite',
			'@id'         => $home . '#website',
			'url'         => $home,
			'name'        => $name,
			'description' => $desc,
			'inLanguage'  => get_bloginfo( 'language' ),
			'publisher'   => array( '@id' => $home . '#organization' ),
		),
		array(
			'@type'               => 'SoftwareApplication',
			'name'                => 'Opal Connect',
			'description'         => 'The WordPress plugin that connects a site to Opal, who checks it every day and tells the owner what she found in plain words.',
			'applicationCategory' => 'BusinessApplication',
			'operatingSystem'     => 'WordPress',
			'url'                 => 'https://myopal.dev/',
			'publisher'           => array( '@id' => $home . '#organization' ),
		),
	);
	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
	);
	echo '<script type="application/ld+json">' . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode with JSON_HEX_TAG.
}
add_action( 'wp_head', 'opalcare_json_ld', 2 );

/**
 * The profile links in the footer's social-links block (the Gravatar rel="me" link), for sameAs.
 *
 * @return string[]
 */
function opalcare_same_as_links() {
	$footer = get_block_template( get_stylesheet() . '//footer', 'wp_template_part' );
	if ( ! $footer || ! preg_match_all( '/"url":"([^"]+)","service":"gravatar"/', $footer->content, $m ) ) {
		return array();
	}
	return array_values( array_unique( array_map( 'esc_url_raw', $m[1] ) ) );
}

/**
 * The page a markdown request is for, or null.
 *
 * @return WP_Post|null
 */
function opalcare_markdown_page() {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home && 0 === strpos( $path, $home ) ) {
		$path = trim( substr( $path, strlen( $home ) ), '/' );
	}
	if ( preg_match( '/\.md$/', $path ) ) {
		$slug = preg_replace( '/\.md$/', '', $path );
		if ( 'index' === $slug ) {
			$front = (int) get_option( 'page_on_front' );
			return $front ? get_post( $front ) : null;
		}
		$page = get_page_by_path( $slug, OBJECT, array( 'page', 'post' ) );
		return $page && 'publish' === $page->post_status ? $page : null;
	}
	$accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ) : '';
	if ( false !== stripos( $accept, 'text/markdown' ) && is_singular() ) {
		return get_queried_object();
	}
	if ( false !== stripos( $accept, 'text/markdown' ) && is_front_page() ) {
		$front = (int) get_option( 'page_on_front' );
		return $front ? get_post( $front ) : null;
	}
	return null;
}

/**
 * Serves the markdown copy.
 */
function opalcare_serve_markdown() {
	$page = opalcare_markdown_page();
	if ( ! $page || post_password_required( $page ) ) {
		return;
	}
	$body = opalcare_page_markdown( $page );
	status_header( 200 );
	header( 'Content-Type: text/markdown; charset=utf-8' );
	header( 'Vary: Accept' );
	header( 'Cache-Control: public, max-age=300' );
	header( 'Link: <' . get_permalink( $page ) . '>; rel="canonical"' );
	header( 'X-Robots-Tag: noindex' );
	echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain text, not HTML.
	exit;
}
add_action( 'template_redirect', 'opalcare_serve_markdown', 0 );

/**
 * Tells a page where its markdown copy is.
 */
function opalcare_markdown_link() {
	if ( is_front_page() ) {
		printf( '<link rel="alternate" type="text/markdown" href="%s">' . "\n", esc_url( home_url( '/index.md' ) ) );
	} elseif ( is_singular() ) {
		printf( '<link rel="alternate" type="text/markdown" href="%s">' . "\n", esc_url( untrailingslashit( get_permalink() ) . '.md' ) );
	}
}
add_action( 'wp_head', 'opalcare_markdown_link', 2 );

/**
 * A page's content as markdown: its blocks as they print, then the HTML as plain markdown.
 *
 * @param WP_Post $page The page.
 * @return string
 */
function opalcare_page_markdown( $page ) {
	$html = do_blocks( $page->post_content );
	$html = preg_replace( '#<(script|style|form|noscript)\b.*?</\1>#is', '', $html );
	$doc  = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><body>' . $html . '</body>' );
	libxml_clear_errors();
	$body = $doc->getElementsByTagName( 'body' )->item( 0 );
	$text = $body ? opalcare_md_children( $body ) : '';
	$text = preg_replace( '/ {2,}/', ' ', $text );
	$text = preg_replace( '/[ \t]+$/m', '', $text );
	$text = preg_replace( '/^ +/m', '', $text );
	$text = trim( preg_replace( "/\n{3,}/", "\n\n", $text ) );
	return $text . "\n";
}

/**
 * Markdown for a node's children.
 *
 * @param DOMNode $node Parent.
 * @return string
 */
function opalcare_md_children( $node ) {
	$out = '';
	foreach ( $node->childNodes as $child ) {
		$out .= opalcare_md_node( $child );
	}
	return $out;
}

/**
 * Markdown for one node.
 *
 * @param DOMNode $n Node.
 * @return string
 */
function opalcare_md_node( $n ) {
	if ( XML_TEXT_NODE === $n->nodeType ) {
		return preg_replace( '/\s+/', ' ', $n->textContent );
	}
	if ( XML_ELEMENT_NODE !== $n->nodeType ) {
		return '';
	}
	$tag = strtolower( $n->nodeName );
	$in  = static function () use ( $n ) {
		return trim( opalcare_md_children( $n ) );
	};
	if ( preg_match( '/^h([1-6])$/', $tag, $m ) ) {
		return "\n\n" . str_repeat( '#', (int) $m[1] ) . ' ' . $in() . "\n\n";
	}
	switch ( $tag ) {
		case 'p':
			if ( false !== strpos( ' ' . $n->getAttribute( 'class' ) . ' ', ' opal-step ' ) ) {
				return '';
			}
			$text = $in();
			return '' === $text ? '' : "\n\n" . $text . "\n\n";
		case 'br':
			return "\n";
		case 'strong':
		case 'b':
			$text = $in();
			return '' === $text ? '' : '**' . $text . '** ';
		case 'em':
		case 'i':
			$text = $in();
			return '' === $text ? '' : '*' . $text . '*';
		case 'a':
			$text = $in();
			$href = $n->getAttribute( 'href' );
			return ( '' === $text || '' === $href ) ? $text : '[' . $text . '](' . $href . ')';
		case 'ul':
		case 'ol':
			$out = "\n\n";
			$i   = 1;
			foreach ( $n->childNodes as $li ) {
				if ( XML_ELEMENT_NODE === $li->nodeType && 'li' === strtolower( $li->nodeName ) ) {
					$out .= ( 'ol' === $tag ? $i++ . '. ' : '- ' ) . trim( opalcare_md_children( $li ) ) . "\n";
				}
			}
			return $out . "\n";
		case 'img':
		case 'svg':
		case 'script':
		case 'style':
		case 'button':
		case 'input':
			return '';
		case 'small':
			$text = $in();
			return '' === $text ? '' : ' ' . $text;
		case 'figcaption':
		case 'div':
			$text = trim( opalcare_md_children( $n ) );
			return '' === $text ? '' : "\n\n" . $text . "\n\n";
		default:
			return opalcare_md_children( $n );
	}
}
