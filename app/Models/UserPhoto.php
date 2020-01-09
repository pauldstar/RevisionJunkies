<?php namespace App\Models;

class UserPhoto extends BaseModel
{
	protected $allowedFields = [
		'user_id',
		'file_name'
	];
}
