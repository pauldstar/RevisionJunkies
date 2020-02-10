<?php namespace App\Database\Seeds;

use App\Entities\UserEntity;
use App\Models\Facades\UserFacade;
use App\Models\LeagueModel;
use App\Models\UserModel;
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

		$user = new UserEntity($data);
		UserFacade::save($user);

		if (ENVIRONMENT !== 'production')
		{
			$faker = Factory::create();
			$leagues = (new LeagueModel())->findColumn('id');

			for ($c = 1; $c <= 9; $c++)
			{
				$data = [
					'username' => str_replace('.', '', $faker->unique()->userName),
					'password' => $faker->password(8),
					'email' => $faker->unique()->email,
					'firstname' => $faker->firstName,
					'lastname' => $faker->lastName,
					'hi_score' => $faker->numberBetween(0, 9999),
					'total_qp' => $faker->numberBetween(0, 2000000),
					'league_id' => $faker->randomElement($leagues)
				];

        $user = new UserEntity($data);
        UserFacade::save($user);
			}
		}
	}
}
