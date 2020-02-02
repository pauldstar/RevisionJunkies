<?php namespace App\Models;

use CodeIgniter\Database\MySQLi\Connection;
use CodeIgniter\Model;

/**
 * Class BaseModel
 * @package App\Models
 * @mixin Connection
 */
abstract class BaseModel extends Model
{
	protected $returnType = 'object';

	private static $instance;

	public function __construct()
	{
		parent::__construct();
		$this->setTableName();
	}

  //--------------------------------------------------------------------

  private function setTableName()
  {
		helper('text');
		$className = substr(strrchr(static::class, "\\"), 1);
		$tableName = str_replace('Model', '', $className);
		$this->table = camel_to_snake($tableName);
  }

  //--------------------------------------------------------------------

  /**
   * Returns new instance of the calling model class
   * @return BaseModel
   */
  public static function instance()
  {
    $className = static::class;
    self::$instance || self::$instance = new $className();
    return self::$instance;
  }
}