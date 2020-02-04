<?php namespace App\Models;

use App\Entities\UserEntity;

class UserModel extends BaseModel
{
  protected $returnType = UserEntity::class;
  protected $useTimestamps = true;
	protected $validationRules = 'signup';

	protected $allowedFields = [
		'username',
		'password',
		'email',
		'firstname',
		'lastname',
		'hi_score',
		'total_qp',
		'league_id'
	];
}

