<?php
/**
 * Frontend field rendering. Readonly enforcement.
 *
 * @package Read_Only_For_GravityForms
 * @since   1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Applies readonly/disabled attributes to supported field types on the frontend.
 */
class RFGF_Field_Render {

	/**
	 * Supported field types for read-only.
	 *
	 * @var array
	 */
	private $supported_field_types = array(
		'text',
		'textarea',
		'select',
		'checkbox',
		'radio',
		'number',
		'email',
		'website',
		'date',
		'time',
		'phone',
		'address',
		'name',
		'list',
		'multiselect',
		'password',
		'hidden',
	);

	/**
	 * Constructor. Hooks into Gravity Forms.
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'gform_field_content', array( $this, 'enforce_readonly' ), 10, 5 );
	}

	/**
	 * Add readonly/disabled attributes and class to the first input/select/textarea in the field content.
	 *
	 * @param string $content  The field content HTML.
	 * @param object $field    The field object.
	 * @param mixed  $value    The field value (unused, required by GF hook signature).
	 * @param int    $lead_id  The entry ID (unused, required by GF hook signature).
	 * @param int    $form_id  The form ID (unused, required by GF hook signature).
	 *
	 * @return string Modified content.
	 */
	public function enforce_readonly( $content, $field, $value, $lead_id, $form_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( ! rgar( $field, 'gf_readonly_enable' ) ) {
			return $content;
		}

		if ( ! in_array( $field->type, $this->supported_field_types, true ) ) {
			return $content;
		}

		// Don't apply to HTML, Section, Hidden.
		if ( in_array( $field->type, array( 'html', 'section', 'hidden' ), true ) ) {
			return $content;
		}

		// Add readonly/disabled and class to the input/select/textarea in the field content.
		$content = $this->process_input_types( $content );
		$content = $this->process_textarea( $content );
		$content = $this->process_select( $content );
		$content = $this->process_checkbox_radio( $content );

		return $content;
	}

	/**
	 * Process text, email, url, number, tel, date, time, password inputs.
	 *
	 * @param string $content The field content.
	 *
	 * @return string Modified content.
	 */
	private function process_input_types( $content ) {
		return preg_replace_callback(
			'/(<input[^>]*type=["\']?(text|email|url|number|tel|date|time|password)[^>]*>)/i',
			array( $this, 'modify_input_markup' ),
			$content
		);
	}

	/**
	 * Process textarea elements.
	 *
	 * @param string $content The field content.
	 *
	 * @return string Modified content.
	 */
	private function process_textarea( $content ) {
		return preg_replace_callback(
			'/(<textarea[^>]*>)/i',
			array( $this, 'modify_textarea_markup' ),
			$content
		);
	}

	/**
	 * Process select elements.
	 *
	 * @param string $content The field content.
	 *
	 * @return string Modified content.
	 */
	private function process_select( $content ) {
		return preg_replace_callback(
			'/(<select[^>]*>)/i',
			array( $this, 'modify_select_markup' ),
			$content
		);
	}

	/**
	 * Process checkbox and radio inputs.
	 *
	 * @param string $content The field content.
	 *
	 * @return string Modified content.
	 */
	private function process_checkbox_radio( $content ) {
		return preg_replace_callback(
			'/(<input[^>]*type=["\']?(checkbox|radio)[^>]*>)/i',
			array( $this, 'modify_checkbox_radio_markup' ),
			$content
		);
	}

	/**
	 * Modify an input element to add class, readonly, and disabled attributes.
	 *
	 * @param array $matches Regex match.
	 *
	 * @return string Modified input markup.
	 */
	private function modify_input_markup( $matches ) {
		$input = $matches[1];

		$input = $this->add_class( $input, 'input' );

		if ( false === strpos( $input, 'readonly' ) ) {
			$input = str_replace( '<input', '<input readonly', $input );
		}

		if ( false === strpos( $input, 'disabled' ) ) {
			$input = str_replace( '<input', '<input disabled', $input );
		}

		return $input;
	}

	/**
	 * Modify a textarea element to add class, readonly, and disabled attributes.
	 *
	 * @param array $matches Regex match.
	 *
	 * @return string Modified textarea markup.
	 */
	private function modify_textarea_markup( $matches ) {
		$input = $matches[1];

		$input = $this->add_class( $input, 'textarea' );

		if ( false === strpos( $input, 'readonly' ) ) {
			$input = str_replace( '<textarea', '<textarea readonly', $input );
		}

		if ( false === strpos( $input, 'disabled' ) ) {
			$input = str_replace( '<textarea', '<textarea disabled', $input );
		}

		return $input;
	}

	/**
	 * Modify a select element to add class and disabled attribute.
	 *
	 * @param array $matches Regex match.
	 *
	 * @return string Modified select markup.
	 */
	private function modify_select_markup( $matches ) {
		$input = $matches[1];

		$input = $this->add_class( $input, 'select' );

		if ( false === strpos( $input, 'disabled' ) ) {
			$input = str_replace( '<select', '<select disabled', $input );
		}

		return $input;
	}

	/**
	 * Modify a checkbox/radio input to add class and disabled attribute.
	 *
	 * @param array $matches Regex match.
	 *
	 * @return string Modified input markup.
	 */
	private function modify_checkbox_radio_markup( $matches ) {
		$input = $matches[1];

		$input = $this->add_class( $input, 'input' );

		if ( false === strpos( $input, 'disabled' ) ) {
			$input = str_replace( '<input', '<input disabled', $input );
		}

		return $input;
	}

	/**
	 * Add the gf_readonly class to an element.
	 *
	 * @param string $input The element HTML.
	 * @param string $tag   The tag type (unused, kept for signature consistency).
	 *
	 * @return string Modified element with class added.
	 */
	private function add_class( $input, $tag ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$class_count = 0;

		$input = preg_replace(
			'/class=["\']([^"\']*)["\']/',
			'class="$1 gf_readonly"',
			$input,
			1,
			$class_count
		);

		if ( ! $class_count ) {
			$input = preg_replace( '/(<(input|textarea|select))/', '$1 class="gf_readonly"', $input, 1 );
		}

		return $input;
	}
}
