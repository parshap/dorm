<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Dorm_Field_Bool extends Dorm_Field {

	public function get($value)
	{
		// @todo: Should we allow NULL values?
		return (bool) $value;
	}

	public function set($value)
	{
		return $this->get($value);
	}
}
