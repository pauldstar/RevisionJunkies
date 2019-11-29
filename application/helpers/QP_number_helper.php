<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('metric_format'))
{
	/**
	 * Returns with number with its abbreviated metric suffix
	 *
	 * @param mixed $num will be cast as int
	 * @param int $precision
	 * @return string
	 */
	function metric_format($num, $precision = 1)
	{
		if ($num >= 1000000000)
		{
			$num = round($num / 1000000000, $precision);
			$unit = 'B';
		}
		elseif ($num >= 1000000)
		{
			$num = round($num / 1048576, $precision);
			$unit = 'M';
		}
		elseif ($num >= 1000)
		{
			$num = round($num / 1024, $precision);
			$unit = 'K';
		}
		else return $num;

		return number_format($num, $precision).' '.$unit;
	}
}
