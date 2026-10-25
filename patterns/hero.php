<?php
/**
 * Title: Hero
 * Slug: opalcare/hero
 * Categories: opalcare
 * Description: The promise, one button, and Opal.
 *
 * @package opalcare
 */

?>
<!-- wp:group {"align":"full","className":"opal-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained","wideSize":"70rem"}} -->
<div class="wp-block-group alignfull opal-hero" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"align":"wide","verticalAlignment":"center"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%"><!-- wp:paragraph {"className":"opal-eyebrow"} -->
<p class="opal-eyebrow">For people who run a WordPress site</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Your site, <em>looked after.</em></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Opal checks your WordPress site every day, tells you in plain words what she found, and fixes the small things when you say so.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","verticalAlignment":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://myopal.dev/app/">Start free for two weeks</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline opal-link"} -->
<div class="wp-block-button is-style-outline opal-link"><a class="wp-block-button__link wp-element-button" href="#check">See a real check</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"42%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:42%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"opal-plate"} -->
<figure class="wp-block-image size-large opal-plate"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/opal.webp' ) ); ?>" alt="Opal, holding her notebook, looking up with a small smile"/><figcaption class="wp-element-caption">Opal keeps a notebook on every site she looks after.</figcaption></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
