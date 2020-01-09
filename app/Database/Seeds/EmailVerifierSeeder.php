<?php namespace App\Database\Seeds;

use App\Models\EmailVerifier;
use App\Models\User;
use CodeIgniter\Database\Seeder;

class EmailVerifierSeeder extends Seeder
{
	public function run()
	{
		$users = (new User())->findColumn('id');

		foreach ($users as $id)
		{
			$data = [
				'user_id' => $id,
				'verifier' => random_string('alnum', 10),
			];

			$verifier = new EmailVerifier();
			$verifier->save($data);
		}
	}
}
