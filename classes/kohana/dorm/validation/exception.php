<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_DORM_Validation_Exception extends Validation_Exception {

	protected $_model;

	public function __construct(
		DORM_Model $model,
		Validation $array,
		$message = 'Failed to validate array',
		array $variables = NULL
	)
	{
		$this->_model = $model;

		parent::__construct($array, $message, $variables);
	}
	public function errors($directory = NULL, $translate = TRUE)
	{
		if ($directory !== NULL)
		{
			$directory .= '/'.DORM::model_name($this->_model);
		}

		return $this->array->errors($directory, $translate);
	}
}
