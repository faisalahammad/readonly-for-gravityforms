<?php
/**
 * Asset enqueueing for frontend and admin.
 *
 * @package Read_Only_For_GravityForms
 * @since   1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles CSS/JS loading, gated to Gravity Forms admin pages for admin assets.
 */
class RFGF_Assets {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->version = READONLY_FOR_GRAVITYFORMS_VERSION;
	}

	/**
	 * Enqueue the shared frontend style when a rendered form has a read-only field.
	 *
	 * @param array $form The form being rendered.
	 *
	 * @return void
	 */
	public function enqueue_frontend_style( $form ) {
		foreach ( (array) rgar( $form, 'fields' ) as $field ) {
			if ( rgar( $field, 'gf_readonly_enable' ) ) {
				$this->enqueue_style();
				return;
			}
		}
	}

	/**
	 * Enqueue admin assets on Gravity Forms admin pages only.
	 *
	 * @return void
	 */
	public function enqueue_admin_assets() {
		if ( ! $this->is_gravityforms_admin_page() ) {
			return;
		}

		$this->enqueue_style();
		$this->enqueue_admin_script();
	}

	/**
	 * Determine whether we are currently on a Gravity Forms admin screen.
	 *
	 * @return bool
	 */
	private function is_gravityforms_admin_page() {
		// Read-only check, no action performed. Sanitized below.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( empty( $_GET['page'] ) ) {
			return false;
		}

		$page = sanitize_key( wp_unslash( $_GET['page'] ) );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return 0 === strpos( $page, 'gf_' );
	}

	/**
	 * Enqueue the shared style used for read-only fields.
	 *
	 * @return void
	 */
	private function enqueue_style() {
		$handle = 'readonly-for-gravityforms-style';
		$src    = plugins_url( 'assets/css/readonly-for-gravityforms.css', dirname( __DIR__ ) . '/readonly-for-gravityforms.php' );

		if ( ! wp_style_is( $handle, 'registered' ) ) {
			wp_register_style( $handle, $src, array(), $this->version );
		}

		wp_enqueue_style( $handle );
	}

	/**
	 * Enqueue the admin script.
	 *
	 * @return void
	 */
	private function enqueue_admin_script() {
		$handle = 'readonly-for-gravityforms-admin';
		$src    = plugins_url( 'assets/js/readonly-for-gravityforms-admin.js', dirname( __DIR__ ) . '/readonly-for-gravityforms.php' );

		if ( ! wp_script_is( $handle, 'registered' ) ) {
			wp_register_script( $handle, $src, array( 'jquery' ), $this->version, true );
		}

		wp_enqueue_script( $handle );
	}
}
