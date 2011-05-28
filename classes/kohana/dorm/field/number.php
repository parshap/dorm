<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Dorm_Field_Number extends Dorm_Field {

	public function get($value)
	{
		// Just use doubles and let the data store type juggle
		return (double) $value;
	}

	public function set($value)
	{
		return $this->get($value);
	}
}
