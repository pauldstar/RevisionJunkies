<?php namespace App\Models;

class EmailVerifierModel extends BaseModel
{
  protected $primaryKey = 'user_id';

	protected $allowedFields = [
		'user_id',
		'verifier'
	];
}
