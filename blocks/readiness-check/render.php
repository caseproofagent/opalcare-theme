<?php
/**
 * Renders the AI readiness check: a form that view.js sends to Opal Care and shows the report under.
 *
 * @package opal-care
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Where the check runs. Opal Care's free readiness endpoint; it allows calls from opalcarewp.com.
 *
 * @var string $endpoint
 */
$endpoint = apply_filters( 'opalcare_readiness_endpoint', 'https://opalagent.app/api/readiness' );
$field_id = wp_unique_id( 'opal-readiness-' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'opal-readiness', 'data-endpoint' => esc_url( $endpoint ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes the wrapper attributes. ?>>
	<form class="opal-readiness__form" novalidate>
		<label class="opal-readiness__label" for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( 'The address of your site', 'opal-care' ); ?></label>
		<div class="opal-readiness__row">
			<input class="opal-readiness__input" id="<?php echo esc_attr( $field_id ); ?>" name="url" type="text" inputmode="url" autocomplete="url" autocapitalize="none" spellcheck="false" placeholder="<?php esc_attr_e( 'yoursite.com', 'opal-care' ); ?>" required>
			<button class="opal-readiness__button wp-element-button" type="submit"><?php esc_html_e( 'Check my site', 'opal-care' ); ?></button>
		</div>
	</form>
	<p class="opal-readiness__note"><?php esc_html_e( 'Opal Care runs the check. We keep a count of checks, never the address you type.', 'opal-care' ); ?></p>
	<div class="opal-readiness__out" role="region" aria-live="polite" tabindex="-1"></div>
	<noscript><p><?php esc_html_e( 'This check needs JavaScript. You can run it at opalagent.app/care/.', 'opal-care' ); ?></p></noscript>
</div>
