<?php
/**
 * Tests for RFGF_Assets.
 *
 * @package Read_Only_For_GravityForms
 */

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Asset loading tests.
 */
class Test_RFGF_Assets extends TestCase {

	/**
	 * Frontend style loads only for forms that have a read-only field.
	 *
	 * @return void
	 */
	public function test_frontend_style_only_for_forms_with_readonly_field() {
		$assets = new RFGF_Assets();

		$GLOBALS['rfgf_enqueued_styles'] = array();
		$assets->enqueue_frontend_style( array( 'fields' => array( new RFGF_Test_Field( 'text', false ) ) ) );
		$assets->enqueue_frontend_style( array() );
		$this->assertSame( array(), $GLOBALS['rfgf_enqueued_styles'] );

		$assets->enqueue_frontend_style( array( 'fields' => array( new RFGF_Test_Field( 'text', false ), new RFGF_Test_Field( 'text' ) ) ) );
		$this->assertSame( array( 'readonly-for-gravityforms-style' ), $GLOBALS['rfgf_enqueued_styles'] );
	}
}
