<?php namespace Config;

use App\Libraries\MigrationRunner;
use CodeIgniter\Config\Services as CoreServices;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Database\ConnectionInterface;

require_once SYSTEMPATH . 'Config/Services.php';

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends CoreServices
{
	/**
	 * Return the appropriate migration runner.
	 *
	 * @param BaseConfig $config
	 * @param ConnectionInterface $db
	 * @param boolean	$getShared
	 *
	 * @return MigrationRunner
	 */
	public static function migrations(BaseConfig $config = null,
																		ConnectionInterface $db = null,
																		bool $getShared = true)
	{
		if ($getShared)
		{
			return static::getSharedInstance('migrations', $config, $db);
		}

		$config = empty($config) ? new Migrations() : $config;

		return new MigrationRunner($config, $db);
	}

	//--------------------------------------------------------------------
}
