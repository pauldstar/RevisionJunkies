<?php namespace App\Models;

class UserModel extends BaseModel
{
	protected $useTimestamps = true;
	protected $beforeInsert = ['hashPassword'];
	protected $beforeUpdate = ['hashPassword'];
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

	//--------------------------------------------------------------------

	protected function hashPassword(array $data) : array
	{
		if (! isset($data['data']['password'])) return $data;

		$password = $data['data']['password'];
		$data['data']['password'] = password_hash($password, PASSWORD_BCRYPT);

		return $data;
	}
}

