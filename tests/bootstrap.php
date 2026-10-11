<?php
/**
 * PHPUnit bootstrap. Stubs the few WordPress and Gravity Forms functions the plugin
 * classes touch so the rendering logic can be unit tested without a database.
 *
 * @package Read_Only_For_GravityForms
 */

define( 'ABSPATH', __DIR__ . '/' );

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

/**
 * Stub of WordPress add_filter().
 *
 * @return bool
 */
function add_filter() {
	return true;
}

/**
 * Stub of WordPress add_action().
 *
 * @return bool
 */
function add_action() {
	return true;
}

/**
 * Stub of WordPress __().
 *
 * @param string $text Text to translate.
 *
 * @return string
 */
function __( $text ) {
	return $text;
}

/**
 * Stub of Gravity Forms rgar().
 *
 * @param array|ArrayAccess $data Source.
 * @param string            $name Key.
 *
 * @return mixed
 */
function rgar( $data, $name ) {
	return isset( $data[ $name ] ) ? $data[ $name ] : '';
}

/**
 * Stub of WordPress esc_attr(). Like core, it does not double-encode existing entities.
 *
 * @param string $text Text to escape.
 *
 * @return string
 */
function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8', false );
}

/**
 * Stub of WordPress plugins_url().
 *
 * @param string $path Path relative to the plugin.
 *
 * @return string
 */
function plugins_url( $path ) {
	return 'https://example.test/' . $path;
}

/**
 * Stub of WordPress wp_style_is().
 *
 * @return bool
 */
function wp_style_is() {
	return true;
}

/**
 * Stub of WordPress wp_register_style().
 *
 * @return bool
 */
function wp_register_style() {
	return true;
}

/**
 * Stub of WordPress wp_enqueue_style(). Records the handle for assertions.
 *
 * @param string $handle Style handle.
 *
 * @return void
 */
function wp_enqueue_style( $handle ) {
	$GLOBALS['rfgf_enqueued_styles'][] = $handle;
}

define( 'READONLY_FOR_GRAVITYFORMS_VERSION', 'test' );

require_once dirname( __DIR__ ) . '/includes/class-rfgf-field-render.php';
require_once dirname( __DIR__ ) . '/includes/class-rfgf-assets.php';
require_once dirname( __DIR__ ) . '/includes/class-rfgf-tooltips.php';
require_once __DIR__ . '/class-rfgf-test-field.php';
