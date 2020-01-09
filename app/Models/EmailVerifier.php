<?php namespace App\Models;

class EmailVerifier extends BaseModel
{
	protected $allowedFields = [
		'user_id',
		'verifier'
	];
}
