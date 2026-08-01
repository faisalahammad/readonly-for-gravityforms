<?php
/**
 * Plugin Name:       Read Only Fields for Gravity Forms
 * Description:       Adds a universal 'Read Only' option to all Gravity Forms fields, with a tooltip warning and frontend enforcement for all field types.
 * Version:           1.1.0
 * Author:            Faisal Ahammad
 * Author URI:        https://faisalahammad.com
 * Plugin URI:        https://wordpress.org/plugins/readonly-for-gravityforms
 * Text Domain:       readonly-for-gravityforms
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 6.8
 * Tested up to:      7.0
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! defined( 'READONLY_FOR_GRAVITYFORMS_VERSION' ) ) {
	define( 'READONLY_FOR_GRAVITYFORMS_VERSION', '1.1.0' );
}

require_once __DIR__ . '/includes/class-rfgf-loader.php';

new RFGF_Loader();
