<?php
/**
 * Tests for RFGF_Field_Render.
 *
 * @package Read_Only_For_GravityForms
 */

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Field render tests.
 */
class Test_RFGF_Field_Render extends TestCase {

	/**
	 * Instance under test.
	 *
	 * @var RFGF_Field_Render
	 */
	private $render;

	/**
	 * Set up.
	 *
	 * @return void
	 */
	protected function set_up() {
		parent::set_up();
		$this->render = new RFGF_Field_Render();
	}

	/**
	 * Run the filter callback.
	 *
	 * @param string          $content Field HTML.
	 * @param RFGF_Test_Field $field   Field.
	 *
	 * @return string
	 */
	private function render( $content, $field ) {
		return $this->render->enforce_readonly( $content, $field, '', 0, 1 );
	}

	/**
	 * Setting off leaves markup untouched.
	 *
	 * @return void
	 */
	public function test_disabled_setting_returns_content_unchanged() {
		$html = '<input type="text" class="large" value="a">';
		$this->assertSame( $html, $this->render( $html, new RFGF_Test_Field( 'text', false ) ) );
	}

	/**
	 * Unsupported and excluded types are left alone.
	 *
	 * @return void
	 */
	public function test_unsupported_types_are_unchanged() {
		$html = '<input type="text" class="large" value="a">';
		foreach ( array( 'hidden', 'html', 'section', 'fileupload', 'captcha' ) as $type ) {
			$this->assertSame( $html, $this->render( $html, new RFGF_Test_Field( $type ) ), $type );
		}
	}

	/**
	 * Text input gets readonly, disabled, the class, and a hidden copy of its value.
	 *
	 * @return void
	 */
	public function test_text_input_is_locked() {
		$out = $this->render( '<input name="input_1" type="text" class="large" value="a">', new RFGF_Test_Field( 'text' ) );
		$this->assertSame( '<input disabled readonly name="input_1" type="text" class="large gf_readonly" value="a"><input type="hidden" name="input_1" value="a">', $out );
	}

	/**
	 * Input without a class attribute gets one.
	 *
	 * @return void
	 */
	public function test_input_without_class_gets_class() {
		$out = $this->render( '<input type="email" value="a@b.c">', new RFGF_Test_Field( 'email' ) );
		$this->assertSame( '<input disabled readonly class="gf_readonly" type="email" value="a@b.c">', $out );
	}

	/**
	 * Textarea gets readonly, disabled, and a hidden copy of its content.
	 *
	 * @return void
	 */
	public function test_textarea_is_locked() {
		$out = $this->render( '<textarea name="input_2" class="textarea medium">hi</textarea>', new RFGF_Test_Field( 'textarea' ) );
		$this->assertSame( '<textarea disabled readonly name="input_2" class="textarea medium gf_readonly">hi</textarea><input type="hidden" name="input_2" value="hi">', $out );
	}

	/**
	 * Select gets disabled; with nothing selected the first option is submitted, like a browser does.
	 *
	 * @return void
	 */
	public function test_select_is_disabled() {
		$out = $this->render( '<select name="input_3" class="medium"><option>x</option></select>', new RFGF_Test_Field( 'select' ) );
		$this->assertSame( '<select disabled name="input_3" class="medium gf_readonly"><option>x</option></select><input type="hidden" name="input_3" value="x">', $out );
	}

	/**
	 * Checkbox and radio inputs get disabled only.
	 *
	 * @return void
	 */
	public function test_checkbox_and_radio_are_disabled() {
		$out = $this->render( '<input type="checkbox" class="c" value="1"><input type="radio" value="2">', new RFGF_Test_Field( 'checkbox' ) );
		$this->assertSame( '<input disabled type="checkbox" class="c gf_readonly" value="1"><input disabled class="gf_readonly" type="radio" value="2">', $out );
	}

	/**
	 * Existing attributes are not duplicated.
	 *
	 * @return void
	 */
	public function test_existing_attributes_are_not_duplicated() {
		$out = $this->render( '<input type="text" readonly disabled value="a">', new RFGF_Test_Field( 'text' ) );
		$this->assertSame( '<input class="gf_readonly" type="text" readonly disabled value="a">', $out );
	}

	/**
	 * The words "readonly" or "disabled" inside an attribute value must not stop locking.
	 *
	 * @return void
	 */
	public function test_attribute_names_in_values_do_not_block_locking() {
		$out = $this->render( '<input type="text" placeholder="Not disabled, not readonly" value="a">', new RFGF_Test_Field( 'text' ) );
		$this->assertStringContainsString( '<input disabled readonly ', $out );

		$out = $this->render( '<textarea class="readonly-ish disabled-look">x</textarea>', new RFGF_Test_Field( 'textarea' ) );
		$this->assertStringContainsString( '<textarea disabled readonly ', $out );

		$out = $this->render( '<select class="not-disabled"><option>x</option></select>', new RFGF_Test_Field( 'select' ) );
		$this->assertStringContainsString( '<select disabled ', $out );
	}

	/**
	 * Only checked checkboxes and radios are copied to hidden inputs, keeping GF's names.
	 *
	 * @return void
	 */
	public function test_checked_choices_are_submitted() {
		$html = "<input name='input_4.1' type='checkbox' value='One' checked='checked'><input name='input_4.2' type='checkbox' value='Two'>";
		$out  = $this->render( $html, new RFGF_Test_Field( 'checkbox' ) );

		$this->assertStringContainsString( '<input type="hidden" name="input_4.1" value="One">', $out );
		$this->assertStringNotContainsString( 'name="input_4.2"', $out );
	}

	/**
	 * Selected options are copied, one hidden input each for a multiselect.
	 *
	 * @return void
	 */
	public function test_selected_options_are_submitted() {
		$html = "<select name='input_5[]' multiple='multiple'><option value='a' selected='selected'>A</option><option value='b'>B</option><option value='c' selected='selected'>C</option></select>";
		$out  = $this->render( $html, new RFGF_Test_Field( 'multiselect' ) );

		$this->assertStringEndsWith( '</select><input type="hidden" name="input_5[]" value="a"><input type="hidden" name="input_5[]" value="c">', $out );

		$html = "<select name='input_6' multiple='multiple'><option value='a'>A</option></select>";
		$this->assertStringEndsWith( '</select>', $this->render( $html, new RFGF_Test_Field( 'multiselect' ) ) );

		$html = "<select name='input_10'><option value='x' disabled='disabled'>X</option><option value='y'>Y</option></select>";
		$this->assertStringEndsWith( '</select><input type="hidden" name="input_10" value="y">', $this->render( $html, new RFGF_Test_Field( 'select' ) ) );
	}

	/**
	 * Escaped values stay escaped once and quotes cannot break out of the hidden input.
	 *
	 * @return void
	 */
	public function test_hidden_values_are_escaped() {
		$out = $this->render( "<input name='input_7' type='text' value='Tom &amp; \"Jerry\"'>", new RFGF_Test_Field( 'text' ) );
		$this->assertStringEndsWith( '<input type="hidden" name="input_7" value="Tom &amp; &quot;Jerry&quot;">', $out );

		$out = $this->render( '<textarea name="input_8">a &lt;b&gt; "c"</textarea>', new RFGF_Test_Field( 'textarea' ) );
		$this->assertStringEndsWith( '<input type="hidden" name="input_8" value="a &lt;b&gt; &quot;c&quot;">', $out );
	}

	/**
	 * Elements that were already disabled, or have no name, get no hidden copy.
	 *
	 * @return void
	 */
	public function test_no_hidden_copy_without_name_or_when_already_disabled() {
		$out = $this->render( '<input name="input_9" type="text" disabled value="a">', new RFGF_Test_Field( 'text' ) );
		$this->assertStringNotContainsString( 'type="hidden"', $out );

		$out = $this->render( '<input type="text" value="a">', new RFGF_Test_Field( 'text' ) );
		$this->assertStringNotContainsString( 'type="hidden"', $out );
	}
}
