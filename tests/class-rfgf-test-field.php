<?php
/**
 * Gravity Forms field test double.
 *
 * @package Read_Only_For_GravityForms
 */

/**
 * Minimal stand-in for GF_Field: object properties plus array access, like the real class.
 */
class RFGF_Test_Field implements ArrayAccess {

	/**
	 * Field type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Read-only setting.
	 *
	 * @var bool
	 */
	public $gf_readonly_enable;

	/**
	 * Constructor.
	 *
	 * @param string $type    Field type.
	 * @param bool   $enabled Read-only setting.
	 */
	public function __construct( $type, $enabled = true ) {
		$this->type               = $type;
		$this->gf_readonly_enable = $enabled;
	}

	/**
	 * Whether the property exists.
	 *
	 * @param mixed $offset Property name.
	 *
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return isset( $this->$offset );
	}

	/**
	 * Get a property.
	 *
	 * @param mixed $offset Property name.
	 *
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->$offset;
	}

	/**
	 * Set a property.
	 *
	 * @param mixed $offset Property name.
	 * @param mixed $value  Value.
	 *
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		$this->$offset = $value;
	}

	/**
	 * Unset a property.
	 *
	 * @param mixed $offset Property name.
	 *
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		unset( $this->$offset );
	}
}
