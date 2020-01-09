<?php

if (!function_exists('camel_to_snake'))
{
	/**
	 * converts camelCase to snake_case
	 *
	 * @param $input
	 * @return string
	 */
	function camel_to_snake($input)
	{
		if (preg_match('/[A-Z]/', $input) === 0)
		{
			return $input;
		}

		$pattern = '/([a-z])([A-Z])/';

		return strtolower(preg_replace_callback($pattern, function ($a)
		{
			return $a[1] . "_" . strtolower($a[2]);
		}, $input));
	}
}
