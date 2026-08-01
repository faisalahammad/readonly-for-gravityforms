<?php
/**
 * Tooltip text registration for Gravity Forms.
 *
 * @package Read_Only_For_GravityForms
 * @since   1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the tooltip text for the readonly setting.
 */
class RFGF_Tooltips {

	/**
	 * Constructor — hooks into Gravity Forms.
	 */
	public function __construct() {
		add_filter( 'gform_tooltips', array( $this, 'register_tooltip' ) );
	}

	/**
	 * Add the readonly tooltip to the tooltips array.
	 *
	 * @param array $tooltips Existing tooltips.
	 *
	 * @return array Modified tooltips.
	 */
	public function register_tooltip( $tooltips ) {
		$tooltips['form_field_readonly'] = '<strong>Read Only</strong> When enabled, this field will be read-only on the frontend. <br><b>Recommendation:</b> Do not make this field required or use merge tags as the default value, as this can cause required field errors.';

		return $tooltips;
	}
}
