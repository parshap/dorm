<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_DORM_Query {

	protected $_query;

	protected $_meta;

	protected $_type;

	protected $_scopes = array();

	protected $_is_single = FALSE;

	public function __construct(DORM_Meta $meta, $type)
	{
		$this->_meta = $meta;
		$this->_type = $type;

		$d = Datastore::instance();
		$group = $meta->group();

		switch ($type)
		{
			case Datastore::CREATE:
				$this->_query = $d->create($group);
				break;

			case Datastore::RETRIEVE:
				$this->_query = $d->retrieve($group);
				break;

			case Datastore::UPDATE:
				$this->_query = $d->update($group);
				break;

			case Datastore::DELETE:
				$this->_query = $d->delete($group);
				break;

			default:
				throw new Kohana_Exception('Unknown query type');
				break;
		}
	}

	public function __call($name, $arguments)
	{
		call_user_func_array(array($this->_query, $name), $arguments);

		return $this;
	}

	/**
	 * Returns the meta object of the model we are querying
	 */
	public function meta()
	{
		return $this->_meta;
	}

	public function scope($name)
	{
		if ( ! $this->_query instanceof Datastore_Query_Where)
		{
			throw new Kohana_Exception(
				'This type of query cannot be scoped'
			);
		}

		$this->_scopes[$name] = TRUE;
	}

	public function unscope($name = NULL)
	{
		// Remove all scopes
		if ($name === NULL)
		{
			$this->_scopes = array();
		}

		// Remove a single named scope
		else
		{
			unset($this->_scopes[$name]);
		}

		return $this;
	}

	// @TODO: Wish limit was a getter as well - we need a way to get query
	// object state
	public function limit($number)
	{
		$this->_query->limit($number);

		$this->_is_single = $number === 1;

		return $this;
	}

	public function where($field_name, $operator, $operand = NULL)
	{
		return $this->and_where($field_name, $operator, $operand);
	}

	public function and_where($field_name, $operator, $operand = NULL)
	{
		// @TODO: What other cases do we need to call put the value through
		// save()? !=
		if ($operator === '=')
		{
			// Apply the model's field's ->save() method to compare with the
			// value that would be saved into the data store.
			if ($field = $this->_meta->field($field_name))
			{
				$operand = $field->save($operand);
			}
		}

		$this->_query->where($field_name, $operator, $operand);

		return $this;
	}

	public function execute()
	{
		// Apply any scopes
		foreach ($this->_scopes as $scope => $active)
		{
			if ($active)
			{
				$this->_apply_scope($scope);
			}
		}

		// Execute the query
		$result = $this->_query->execute();

		// Return the results
		switch ($this->_type)
		{
			case Datastore::RETRIEVE:
				$result = new DORM_Result($result, $this->_meta);

				if ($this->_is_single)
				{
					if ( ! $result = $result->current())
					{
						// The result was empty, so return a new model.
						$result = Model::factory($this->_meta->model_name());
					}
				}

				return $result;
				break;

			default:
				return $result;
		}
	}

	protected function _apply_scope($name)
	{
		if ($callback = $this->_meta->scope($name))
		{
			// Pass along any parameters, except replacing the first parameter
			// (the scope's name) with the query object the scope must modify
			$params = func_get_args();
			$params[0] = $this;

			call_user_func_array($callback, $params);
		}

		return $this;
	}
}
