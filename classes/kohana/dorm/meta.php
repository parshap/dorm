<?php defined('SYSPATH') or die('No direct script access.');

/**
 * An instance of this class is created per model class definition to hold
 * meta inforamtion about the model.
 *
 * Ideas:
 *  * no accessors - just public properties for simplicity
 */
class Kohana_DORM_Meta {

	protected $_model_name;

	protected $_group;

	protected $_query_class = 'DORM_Query';

	protected $_scopes = array();

	public $fields = array();

	public function __construct($model_name)
	{
		$this->_model_name = $model_name;
	}

	public function finalize()
	{
		if ( ! $this->_group)
		{
			$this->_group = Inflector::plural($this->_model_name);
		}

		$this->initialized = TRUE;
	}

	/**
	 * Returns the specified field. Throws an exception if the field does not
	 * exist.
	 */
	public function field($name, $type = NULL, $options = array())
	{
		// Getter
		if ($type === NULL)
		{
			return Arr::get($this->fields, $name);
		}

		// Setter
		else
		{
			$this->fields[$name] = Dorm_Field::factory($type, $name, $options);

			return $this;
		}
	}

	public function group($group = NULL)
	{
		// Getter
		if ($group === NULL)
		{
			return $this->_group;
		}

		// Setter
		$this->_group = $group;

		return $this;
	}

	public function query_class($class = NULL)
	{
		// Getter
		if ($class === NULL)
		{
			return $this->_query_class;
		}

		// Setter
		else
		{
			$this->_query_class = $class;
		}

		return $this;
	}

	public function scope($name, $callback = NULL)
	{
		// Getter
		if ($callback === NULL)
		{
			return Arr::get($this->_scopes, $name);
		}

		// Setter
		else
		{
			$this->_scopes[$name] = $callback;
		}

		return $this;
	}

	public function query($type)
	{
		return new $this->_query_class($this, $type);
	}

	public function model_name()
	{
		return $this->_model_name;
	}
}
