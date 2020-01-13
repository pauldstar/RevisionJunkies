<?php namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
	protected $returnType = 'object';

	public function __construct()
	{
		parent::__construct();
		helper('text');
    ENVIRONMENT === 'testing' OR session();
		$this->setTable();
		$this->builder();
	}

	public function setTable(string $table = '')
	{
		$modelName = substr(strrchr(static::class, "\\"), 1);
		$this->table = camel_to_snake($modelName);
	}
}
