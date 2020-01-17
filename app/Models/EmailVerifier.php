<?php namespace App\Models;

class EmailVerifier extends BaseModel
{
  protected $primaryKey = 'user_id';

	protected $allowedFields = [
		'user_id',
		'verifier'
	];
}
