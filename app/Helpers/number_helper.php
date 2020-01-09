<?php

if (! function_exists('number_to_abbr_amount'))
{
	/**
	 * Converts numbers to a more readable representation
	 * when dealing with very large numbers (in the thousands or above),
	 * up to the quadrillions, because you won't often deal with numbers
	 * larger than that.
	 *
	 * It uses the "short form" numbering system as this is most commonly
	 * used within most English-speaking countries today.
	 *
	 * @see https://simple.wikipedia.org/wiki/Names_for_large_numbers
	 *
	 * @param string      $num
	 * @param integer     $precision
	 * @param string|null $locale
	 *
	 * @return boolean|string
	 */
	function number_to_abbr_amount($num, int $precision = 0, string $locale = null)
	{
		// Strip any formatting & ensure numeric input
		try
		{
			$num = 0 + str_replace(',', '', $num);
		}
		catch (\ErrorException $ee)
		{
			return false;
		}

		$suffix = '';

		// ignore sub part
		$generalLocale = $locale;
		if (! empty($locale) && ( $underscorePos = strpos($locale, '_')))
		{
			$generalLocale = substr($locale, 0, $underscorePos);
		}

		if ($num > 1000000000000000)
		{
			$suffix = 'Q';
			$num    = round(($num / 1000000000000000), $precision);
		}
		elseif ($num > 1000000000000)
		{
			$suffix = 'T';
			$num    = round(($num / 1000000000000), $precision);
		}
		else if ($num > 1000000000)
		{
			$suffix = 'B';
			$num    = round(($num / 1000000000), $precision);
		}
		else if ($num > 1000000)
		{
			$suffix = 'M';
			$num    = round(($num / 1000000), $precision);
		}
		else if ($num > 1000)
		{
			$suffix = 'K';
			$num    = round(($num / 1000), $precision);
		}

		return format_number($num, $precision, $locale, ['after' => $suffix]);
	}
}

//--------------------------------------------------------------------