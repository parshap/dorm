<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Dorm_Field_Array extends DORM_Field {

	public function get($value)
	{
		return (array) $value;
	}

	public function set($value)
	{
		return $this->get($value);
	}
}
