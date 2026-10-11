<?php
/**
 * Tests for RFGF_Tooltips.
 *
 * @package Read_Only_For_GravityForms
 */

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Tooltip tests.
 */
class Test_RFGF_Tooltips extends TestCase {

	/**
	 * Tooltip is added and existing tooltips are kept.
	 *
	 * @return void
	 */
	public function test_register_tooltip() {
		$tooltips = ( new RFGF_Tooltips() )->register_tooltip( array( 'other' => 'x' ) );

		$this->assertSame( 'x', $tooltips['other'] );
		$this->assertStringContainsString( '<strong>Read Only</strong>', $tooltips['form_field_readonly'] );
	}
}
