<?php namespace App\Models;

class UserPhoto extends BaseModel
{
  protected $primaryKey = 'user_id';

	protected $allowedFields = [
		'user_id',
		'file_name'
	];
}
