<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Dorm_Field_Timestamp extends Dorm_Field {

	/**
	 * Accepted values:
	 *  * Integer unix timestamp
	 *  * DateTime object
	 *  * string processed with strtotime
	 *
	 *  @return int Unix timestamp
	 */
	public function get($value)
	{
		if (is_int($value))
		{
			// Already an int timestamp
			return $value;
		}

		else if (is_string($value))
		{
			return strtotime($value);
		}

		else if ($value instanceof DateTime)
		{
			return $value->getTimestamp();
		}

		// Invalid input
		return NULL;
	}

	public function set($value)
	{
		return $this->get($value);
	}
}
