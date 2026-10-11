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

require_once dirname( __DIR__ ) . '/includes/class-rfgf-field-render.php';
require_once dirname( __DIR__ ) . '/includes/class-rfgf-tooltips.php';
require_once __DIR__ . '/class-rfgf-test-field.php';
