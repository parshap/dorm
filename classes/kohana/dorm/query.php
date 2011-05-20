<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_DORM_Query {

	protected $_query;

	protected $_meta;

	protected $_type;

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

	public function limit($number)
	{
		$this->_query->limit($number);

		$this->_is_single = $number === 1;

		return $this;
	}

	public function execute()
	{
		$result = $this->_query->execute();

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
}
