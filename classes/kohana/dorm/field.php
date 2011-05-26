<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A model's field.
 *
 * Ideas:
 *  * required options as shortcut to not_empty validation rule
 *  * every method use ->get() by default?
 *  * should behave like static typed language - we want that level of
 *    control for databases
 */
abstract class Kohana_DORM_Field {

	protected $_type;

	public $rules = array();

	public function __construct($options)
	{
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
	 * Called when retrieving the value of a model's field.
	 */
	public function get($value)
	{
		return $value;
	}

	/**
	 * Called when setting the value of a model's field.
	 */
	public function set($value)
	{
		return $value;
	}

	/**
	 * Called to get the value of the field when loading from a persisted data
	 * store result.
	 */
	public function load($value)
	{
		// By default, we can just use the field's set method (to "set" the
		// value to the data store result).
		return $this->set($value);
	}

	/**
	 * Called to get the value to persist into the data store.
	 */
	public function save($value)
	{
		// By default we can just use the field's get method (to "get" the
		// value that should be saved to the data store).
		return $this->get($value);
	}
}
