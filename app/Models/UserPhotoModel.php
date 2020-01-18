<?php namespace App\Models;

class UserPhotoModel extends BaseModel
{
  protected $primaryKey = 'user_id';

	protected $allowedFields = [
		'user_id',
		'file_name'
	];
}
