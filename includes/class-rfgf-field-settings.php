<?php
/**
 * Field settings injection for the Gravity Forms editor.
 *
 * @package Read_Only_For_GravityForms
 * @since   1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the "Make this field read-only" checkbox to the field settings panel.
 */
class RFGF_Field_Settings {

	/**
	 * Constructor — hooks into Gravity Forms.
	 */
	public function __construct() {
		add_action( 'gform_field_standard_settings', array( $this, 'render_setting' ), 10, 2 );
	}

	/**
	 * Output the Read Only checkbox at position 20 in the field settings.
	 *
	 * @param int $position The current settings position.
	 * @param int $form_id  The form ID (unused, required by GF hook signature).
	 *
	 * @return void
	 */
	public function render_setting( $position, $form_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( 20 !== $position ) {
			return;
		}
		?>
		<li class="readonly_setting field_setting" style="display:none;">
			<input type="checkbox" id="field_gf_readonly_enable" onclick="SetFieldProperty('gf_readonly_enable', this.checked);">
			<label for="field_gf_readonly_enable" class="inline">Read Only
				<button onclick="return false;" onkeypress="return false;" class="gf_tooltip tooltip tooltip_form_field_readonly" aria-label="<?php esc_attr_e( 'Read Only: When enabled, this field will be read-only on the frontend. Recommendation: Do not make this field required or use merge tags as the default value, as this can cause required field errors.', 'readonly-for-gravityforms' ); ?>">
					<i class="gform-icon gform-icon--question-mark" aria-hidden="true"></i>
				</button>
			</label>
		</li>
		<?php
	}
}
