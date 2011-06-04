<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Represents the data access and mapping layers for this model.
 *
 * Ideas:
 *  * `field` method as read-only shortcut to $this->_meta->field
 *  * better separation of data mapping and data access layers.
 */
class Kohana_DORM_Model extends Model {

	protected $_values = array();

	protected $_changed = array();

	protected $_changes = array();

	protected $_meta;

	public function __construct($id = NULL)
	{
		$this->_meta = DORM::meta($this);

		if ($id !== NULL)
		{
			// DORM::retrieve($this)
			//	->where('_id', '=', $id)
			// @todo load with $id
			// How can we get the values without creating another model object?
		}
	}

	public function __get($name)
	{
		// Must be a defined field of the model to be accessed as a property
		if ($field = $this->_meta->field($name))
		{
			return $this->get($name);
		}
	}

	public function __set($name, $value)
	{
		// Must be a defined field of the model to be set as property
		if ($field = $this->_meta->field($name))
		{
			$this->set(array($name => $value));
		}
	}

	public function __isset($name)
	{
		if ($field = $this->_meta->field($name))
		{
			return $this->is_set($name);
		}

		return FALSE;
	}

	public function __unset($name)
	{
		if ($field = $this->_meta->field($name))
		{
			$this->un_set($name);
		}
	}

	/**
	 * Returns the primary key of the model. If no primary key field exists
	 * or it does not have a value (e.g., new unsaved model), NULL is
	 * returned.
	 *
	 * @return mixed Model's primary key
	 */
	public function id()
	{
		return $this->get('_id');
	}

	public function get($field_name)
	{
		// Get the value of the field
		$value = Arr::get($this->_values, $field_name);

		// Get the field object (if it is defined)
		$field = $this->_meta->field($field_name);

		// The field's value is not set
		if ($value === NULL)
		{
			// If the field is defined, return its default value

			// @todo: getting defaults this way makes it impossible to "unset"
			// the field (i.e., set it to NULL) and difficult to implement
			// isset and unset.
			if ($field)
			{
				return $field->default;
			}

			return NULL;
		}

		if ($field = $this->_meta->field($field_name))
		{
			// If the field is defined, process the value using the field
			// object's `get` method.
			$value = $field->get($value);
		}

		return $value;
	}

	public function set(array $values)
	{
		// Save the change history
		foreach ($values as $field => $value)
		{
			$this->_changed[$field] = TRUE;
			$this->_changes[$field][] = $value;
		}

		// Process values using the field's set method
		foreach ($values as $field_name => $value)
		{
			if ($value === NULL)
			{
				// All fields can simply be set to NULL
				continue;
			}

			if ($field = $this->_meta->field($field_name))
			{
				$values[$field_name] = $field->set($value);
			}
		}

		$this->_values = $values + $this->_values;

		return $this;
	}

	public function is_set($name)
	{
		return isset($this->_values[$name]);
	}

	public function un_set($name)
	{
		unset($this->_values[$name]);
	}

	public function changed($field = NULL)
	{
		if ($field === NULL)
		{
			return ! empty($this->_changed);
		}

		return isset($this->_changed[$field]);
	}

	/**
	 * Resets the state of this model
	 */
	public function reset()
	{
		$this->_values =
		$this->_changed =
		$this->_changes = array();

		return $this;
	}

	/**
	 * Loads values from the datastore layer into the model. The model's state
	 * is reset and values are processed with the field's load method.
	 *
	 * This method should only be used to set raw values as they are loaded
	 * from the datastore. For normal setting of values, the set method should
	 * be used.
	 */
	public function load_values(array $values)
	{
		foreach ($values as $field_name => $value)
		{
			if ($field = $this->_meta->field($field_name))
			{
				$values[$field_name] = $field->load($value);
			}
		}

		$this->reset();
		$this->_values = $values;

		return $this;
	}

	public function as_array()
	{
		$values = $this->_values;

		// Process values using the field's set method
		foreach ($values as $field_name => $value)
		{
			if ($field = $this->_meta->field($field_name))
			{
				$values[$field_name] = $field->get($value);
			}
		}

		return $values;
	}

	public function loaded()
	{
		return (bool) $this->id();
	}

	public function save()
	{
		// Perform validation before doing anything
		$this->validate();

		// Use the values that have changed to save.
		// In a new model, this will be all the fields!
		$values = Arr::extract(
			$this->_values,
			array_keys($this->_changed)
		);

		// Process values using its fields' save method to prepare it
		// for saving to the data layer.
		foreach ($values as $field_name => $value)
		{
			if ($field = $this->_meta->field($field_name))
			{
				$values[$field_name] = $field->save($value);
			}
		}

		// Create
		if ( ! $this->loaded())
		{
			// Perform the data layer update.
			$id = DORM::create($this)
				->set($values)
				->execute();

			$this->set(array('_id' => $id));
		}

		// Update
		else
		{
			DORM::update($this)
				->where('_id', '=', $this->id())
				->set($values)
				->execute();
		}

		// Reset changed values since last save.
		$this->_changed = array();
		$this->_changes = array();
	}

	/**
	 * Validates the current state of the model.
	 *
	 * Throws a Validation_Exception on any validation errors.
	 */
	public function validate()
	{
		$data = Validation::factory($this->_values);

		// Add rules
		foreach ($this->_meta->fields as $field_name => $field)
		{
			foreach ($field->rules as $rule)
			{
				$callback = Arr::get($rule, 0);
				$params = Arr::get($rule, 1);

				// @todo: pass field value as first param by default?
				$data->rule($field_name, $callback, $params);
			}
		}

		// Bind :model parameters to this model so the validation callback
		// can have access to the model.
		$data->bind(':model', $this);

		if ( ! $data->check())
		{
			throw new DORM_Validation_Exception($this, $data);
		}
	}

	public function delete()
	{
		DORM::delete($this)
			->where('_id', '=', $this->id())
			->execute();
	}
}
