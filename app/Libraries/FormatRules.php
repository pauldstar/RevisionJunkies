<?php namespace App\Libraries;

class FormatRules extends \CodeIgniter\Validation\FormatRules
{
	/**
	 * QP name regex
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function name(string $str = null): bool
	{
		return preg_match(
			'/^[A-Za-z][A-Za-z]*(?:-[A-Za-z]+)*(?:\'[A-Za-z]+)*$/',
			$str
		);
	}

	//--------------------------------------------------------------------

	/**
	 * QP username regex
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function username(string $str = null): bool
	{
		return preg_match(
			'/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/',
			$str
		);
	}

	//--------------------------------------------------------------------
}
