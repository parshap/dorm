<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_DORM {

	protected static $_metas = array();

	/**
	 * Returns a new model or fetches an existing one if an id is given and
	 * valid.
	 */
	public static function factory($model_name, $id = NULL)
	{
		if ($id === NULL)
		{
			return Model::factory($model_name);
		}
		else
		{
			return DORM::retrieve($model_name)
				->where('_id', '=', $id)
				->limit(1)
				->execute();
		}
	}

	/**
	 * Returns the class name from a model name.
	 */
	public static function class_name($model_name)
	{
		return strtolower('Model_'.$model_name);
	}

	/**
	 * Returns the model name from a model's object or class name.
	 */
	public static function model_name($model)
	{
		$class_name = $model instanceof DORM_Model
			? strtolower(get_class($model))
			: strtolower($model);

		if (substr($class_name, 0, 6) === 'model_')
		{
			return substr($class_name, 6);
		}

		return $class_name;
	}

	public static function register($model_name)
	{
		$model_name = DORM::model_name($model_name);
		$class_name = DORM::class_name($model_name);

		if ( ! class_exists($class_name))
		{
			return FALSE;
		}

		if ( ! is_subclass_of($class_name, 'DORM_Model'))
		{
			return FALSE;
		}

		DORM::$_metas[$model_name] = $meta = new DORM_Meta($model_name);

		call_user_func(array($class_name, 'initialize'), $meta);

		$meta->finalize();

		return TRUE;
	}

	public static function meta($model)
	{
		$model_name = DORM::model_name($model);

		if ( ! isset(DORM::$_metas[$model_name]))
		{
			DORM::register($model_name);
		}

		return Arr::get(DORM::$_metas, $model_name);
	}

	protected static function _query($model, $type)
	{
		$meta = DORM::meta($model);
		return new DORM_Query($meta, $type);
	}

	public static function create($model)
	{
		return DORM::_query($model, Datastore::CREATE);
	}

	public static function retrieve($model)
	{
		return DORM::_query($model, Datastore::RETRIEVE);
	}

	public static function update($model)
	{
		return DORM::_query($model, Datastore::UPDATE);
	}

	public static function delete($model)
	{
		return DORM::_query($model, Datastore::DELETE);
	}
}
