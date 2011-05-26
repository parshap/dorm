<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Dorm_Field_Enum extends Dorm_Field {

	/**
	 * The valid string values for this field.
	 */
	public $values = array();

	public function __construct($options)
	{
		parent::__construct($options);

		$this->rules[] = array('in_array', array(':value', $this->values));
	}

	public function get($value)
	{
		// Only allow valid values.
		if ( ! in_array($value, $this->values))
		{
			// @todo: Should we just return NULL here, or do otherwise
			// (e.g., throw an exception)?
			return NULL;
		}

		return $value;
	}

	public function set($value)
	{
		// @todo: By never allowing any invalid values to be we are
		// rendering the in_array validation rule useless. Setting the
		// field to an invalid value simply silently sets it to NULL.
		return $this->get($value);
	}
}
