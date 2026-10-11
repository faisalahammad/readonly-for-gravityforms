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
	 * Constructor. Hooks into Gravity Forms.
	 *
	 * @return void
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
		$tooltips['form_field_readonly'] = sprintf(
			'<strong>%1$s</strong> %2$s <br><b>%3$s</b> %4$s',
			__( 'Read Only', 'readonly-for-gravityforms' ),
			__( 'When enabled, this field will be read-only on the frontend.', 'readonly-for-gravityforms' ),
			__( 'Recommendation:', 'readonly-for-gravityforms' ),
			__( 'Do not make this field required or use merge tags as the default value, as this can cause required field errors.', 'readonly-for-gravityforms' )
		);

		return $tooltips;
	}
}
