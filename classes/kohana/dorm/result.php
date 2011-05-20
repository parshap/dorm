<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_DORM_Result
	implements Countable, Iterator, SeekableIterator, ArrayAccess {
	
	// Datastore_Result object
	protected $_result;

	protected $_meta;

	public function __construct(Datastore_Result $result, DORM_Meta $meta)
	{
		$this->_result = $result;
		$this->_meta = $meta;
	}

	public function as_array($key = NULL, $value = NULL)
	{
		// @todo run through model field
		return $this->_result->as_array();
	}

	public function get($field, $default = NULL)
	{
		// @todo: run through model field
		return $this->_result->get($field, $default);
	}

	/* Countable methods */
	public function count()
	{
		return $this->_result->count();
	}

	/* Iterator methods */
	public function current()
	{
		if ($current = $this->_result->current())
		{
			return Model::factory($this->_meta->model_name())
				->load_values($current);
		}

		return FALSE;
	}

	public function key()
	{
		return $this->_result->key();
	}

	public function next()
	{
		return $this->_result->next();
	}

	public function rewind()
	{
		return $this->_result->rewind();
	}

	public function valid()
	{
		return $this->_result->valid();
	}

	/* SeekableIterator methods */
	public function seek($offset)
	{
		return $this->_result->seek($offset);
	}

	/* ArrayAccess methods */
	public function offsetExists($offset)
	{
		return $this->_result->offsetExists($offset);
	}

	public function offsetGet($offset)
	{
		// @todo
		return $this->_result->offsetGet($offset);
	}

	final public function offsetSet($offset, $value)
	{
		throw new Kohana_Exception('DORM results are read-only');
	}

	final public function offsetUnset($offset)
	{
		throw new Kohana_Exception('DORM results are read-only');
	}
}
