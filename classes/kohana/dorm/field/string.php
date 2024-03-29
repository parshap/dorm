<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_DORM_Field_String extends DORM_Field {

	public function get($value)
	{
		return (string) $value;
	}

	public function set($value)
	{
		return $this->get($value);
	}
}
