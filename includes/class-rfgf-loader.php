<?php
/**
 * Register all hooks for the plugin.
 *
 * @package Read_Only_For_GravityForms
 * @since   1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central hook registration.
 */
class RFGF_Loader {

	/**
	 * Constructor — registers all hooks.
	 */
	public function __construct() {
		$this->load_classes();
		$this->register_hooks();
	}

	/**
	 * Load required class files.
	 *
	 * @return void
	 */
	private function load_classes() {
		require_once __DIR__ . '/class-rfgf-field-settings.php';
		require_once __DIR__ . '/class-rfgf-tooltips.php';
		require_once __DIR__ . '/class-rfgf-field-render.php';
		require_once __DIR__ . '/class-rfgf-assets.php';
	}

	/**
	 * Register actions and filters.
	 *
	 * @return void
	 */
	private function register_hooks() {
		new RFGF_Field_Settings();
		new RFGF_Tooltips();
		new RFGF_Field_Render();

		$assets = new RFGF_Assets();
		add_action( 'wp_enqueue_scripts', array( $assets, 'enqueue_frontend_style' ) );
		add_action( 'admin_enqueue_scripts', array( $assets, 'enqueue_admin_assets' ) );
	}
}
