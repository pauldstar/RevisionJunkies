<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * QP Common Functions
 *
 * @category	Common Functions
 * @author		QuePenny
 */

// ------------------------------------------------------------------------

/**
 * Error Logging Interface
 * Overrides log_message() in system/core/Commons.php
 *
 * We use this as a simple mechanism to access the logging
 * class and send messages to be logged.
 *
 * @param	string	the error level: 'error', 'debug' or 'info'
 * @param	string	the error message
 * @return	void
 */
function log_message($level, $message)
{
		if (ENVIRONMENT === 'production' && $level === 'error')
		{
				error_log($message);
				return;
		}

		static $_log;

		if ($_log === NULL)
		{
				// references cannot be directly assigned to static variables, so we use an array
				$_log[0] =& load_class('Log', 'core');
		}

		$_log[0]->write_log($level, $message);
}

// ------------------------------------------------------------------------

if ( ! function_exists('dd'))
{
		/**
		 * Dump and Die
		 *
		 * Output a parameter and die. Useful for debugging
		 *
		 * @param $val
		 * @return  void
		 */
	function dd($val)
	{
		echo '<pre>';
    print_r($val);
    echo  '</pre>';
		die();
	}
}
