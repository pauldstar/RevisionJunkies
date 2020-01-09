<?php namespace Config;

/**
 * Database Configuration
 *
 * @package Config
 */
class Database extends \CodeIgniter\Database\Config
{
	/**
	 * The directory that holds the Migrations
	 * and Seeds directories.
	 *
	 * @var string
	 */
	public $filesPath = APPPATH . 'Database/';

	/**
	 * Lets you choose which connection group to
	 * use if no other is specified.
	 *
	 * @var string
	 */
	public $defaultGroup;

	/**
	 * Database for development.
	 *
	 * @var array
	 */
	public $development = [
		'DSN' => '',
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'quepenny',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => '',
		'pConnect' => false,
		'DBDebug' => (ENVIRONMENT !== 'production'),
		'cacheOn' => false,
		'cacheDir' => '',
		'charset' => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre' => '',
		'encrypt' => false,
		'compress' => false,
		'strictOn' => false,
		'port' => 3306,
		'failover' => []
	];

	/**
	 * Database in production
	 *
	 * @var array
	 */
	public $production = [
		'DSN' => '',
		'hostname' => '/cloudsql/quepenny:europe-west2:qp-app',
		'username' => 'qp-prod',
		'password' => 'wSPr@bktNDl#',
		'database' => 'quepenny',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => '',
		'pConnect' => false,
		'DBDebug' => (ENVIRONMENT !== 'production'),
		'cacheOn' => false,
		'cacheDir' => '',
		'charset' => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre' => '',
		'encrypt' => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port' => 3306,
	];

	/**
	 * This database connection is used when
	 * running PHPUnit database tests.
	 *
	 * @var array
	 */
	public $tests = [
		'DSN' => '',
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'quepenny',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE.
		'pConnect' => false,
		'DBDebug' => (ENVIRONMENT !== 'production'),
		'cacheOn' => false,
		'cacheDir' => '',
		'charset' => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre' => '',
		'encrypt' => false,
		'compress' => false,
		'strictOn' => false,
		'port' => 3306,
		'failover' => []
	];

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->defaultGroup = ENVIRONMENT === 'testing' ? 'tests' : ENVIRONMENT;

		// Ensure that we always set the database group to 'tests' if
		// we are currently running an automated test suite, so that
		// we don't overwrite live data on accident.
		if (ENVIRONMENT === 'testing')
		{
			// Under Travis-CI, we can set an ENV var named 'DB_GROUP'
			// so that we can test against multiple databases.
			if ($group = getenv('DB'))
			{
				if (is_file(TESTPATH . 'travis/Database.php'))
				{
					require TESTPATH . 'travis/Database.php';

					if (!empty($dbconfig) && array_key_exists($group, $dbconfig))
					{
						$this->tests = $dbconfig[$group];
					}
				}
			}
		}
	}

	//--------------------------------------------------------------------
}
