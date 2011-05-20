<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A model's field.
 *
 * Ideas:
 *  * required options as shortcut to not_empty validation rule
 *  * every method use ->get() by default?
 */
abstract class Kohana_DORM_Field {

	protected $_type;

	public $rules = array();

	public function __construct($type, $options)
	{
		$this->_type = $type;

		foreach ($options as $field => $value)
		{
			$this->$field = $value;
		}
	}

	public function type()
	{
		return $_type;
	}

	/**
	 * Called when retrieving the value of this field (e.g., from a model).
	 */
	public function get($value)
	{
		return $value;
	}

	/**
	 * Called when setting the value of this field (in the context of a model).
	 */
	public function set($value)
	{
		return $value;
	}

	/**
	 * Called when loading the value from a persisted result.
	 */
	public function load($value)
	{
		// By default, we can just use the field's set method
		return $this->set($value);
	}

	/**
	 * Called when persisting the field.
	 */
	public function save($value)
	{
		return $value;
	}
}
