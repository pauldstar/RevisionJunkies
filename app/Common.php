<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */

use App\Libraries\Optional;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\ConnectionInterface;
use Config\Database;

if (!function_exists('optional'))
{
	/**
	 * Allow arrow-syntax access of optional objects by using a higher-order
	 * proxy object. The eliminates some need of ternary and null coalesce
	 * operators in views.
	 *
	 * @param mixed|null $value
	 * @return Optional
	 */
	function optional($value)
	{
		return new Optional($value);
	}
}

//--------------------------------------------------------------------

if (! function_exists('log_message'))
{
	/**
	 * A convenience/compatibility method for logging events through
	 * the Log system.
	 *
	 * Allowed log levels are:
	 *  - emergency
	 *  - alert
	 *  - critical
	 *  - error
	 *  - warning
	 *  - notice
	 *  - info
	 *  - debug
	 *
	 * @param string     $level
	 * @param string     $message
	 * @param array|null $context
	 *
	 * @return void|mixed
	 */
	function log_message(string $level, string $message, array $context = [])
	{
		switch (ENVIRONMENT)
		{
			case 'testing':
				// When running tests, we want to always ensure that the
				// TestLogger is running, which provides utilities for
				// for asserting that logs were called in the test code.
				$logger = new TestLogger(new Logger());
				return $logger->log($level, $message, $context);

			case 'production':
				$level === 'error' AND error_log($message);
				return;
		}

		// @codeCoverageIgnoreStart
		return Services::logger(true)
			->log($level, $message, $context);
		// @codeCoverageIgnoreEnd
	}
}
