<?php namespace App\Database\Seeds;

use App\Models\User;
use CodeIgniter\Database\Seeder;
use Faker\Factory;
use ReflectionException;

class UserSeeder extends Seeder
{
	/**
	 * @return mixed|void
	 * @throws ReflectionException
	 */
	public function run()
	{
		$data = [
			'username' => 'qp',
			'password' => 'u$^8}29Wj~PUz^Zq',
			'email' => 'user@quepenny.com',
			'firstname' => 'Paul',
			'lastname' => 'Ogbeiwi'
		];

		$user = new User();
		$user->save($data);

		if (ENVIRONMENT !== 'production')
		{
			$faker = Factory::create();

			for ($c = 1; $c <= 9; $c++)
			{
				$data = [
					'username' => str_replace('.', '', $faker->userName),
					'password' => $faker->password,
					'email' => $faker->email,
					'firstname' => $faker->firstName,
					'lastname' => $faker->lastName,
					'hi_score' => $faker->numberBetween(0, 9999),
					'total_qp' => $faker->numberBetween(0, 2000000),
					'league_id' => $faker->numberBetween(1, 11),
				];

				$user->save($data);
			}
		}
	}
}
