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
	public function field($field_name, $field_type = NULL, $options = array())
	{
		// Getter
		if ($field_type === NULL)
		{
			return Arr::get($this->fields, $field_name);
		}

		// Setter
		else
		{
			// @todo: move this to Dorm_Field::factory?
			$field_class = Dorm::field_class($field_type);

			$this->fields[$field_name] = new $field_class($options);

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

	public function model_name()
	{
		return $this->_model_name;
	}
}
