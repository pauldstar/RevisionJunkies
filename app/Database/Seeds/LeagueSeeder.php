<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\LeagueModel;
use ReflectionException;

class LeagueSeeder extends Seeder
{
  /**
   * @return mixed|void
   * @throws ReflectionException
   */
  public function run()
	{
		$leagues = [
			['id' => '1', 'name' => 'Butterfly', 'color' => '#57B9A8'],
			['id' => '2', 'name' => 'Rabbit', 'color' => '#7AC25B'],
			['id' => '3', 'name' => 'Deer', 'color' => '#F79B3C'],
			['id' => '4', 'name' => 'Zebra', 'color' => '#666666'],
			['id' => '5', 'name' => 'Buffalo', 'color' => '#E06666'],
			['id' => '6', 'name' => 'King Cobra', 'color' => '#A64D79'],
			['id' => '7', 'name' => 'Bald Eagle', 'color' => '#FFD700'],
			['id' => '8', 'name' => 'Grey Wolf', 'color' => '#D9D9D'],
			['id' => '9', 'name' => 'Grizzle Bear', 'color' => '#B45F06'],
			['id' => '10', 'name' => 'Great White Shark', 'color' => '#A4C2F4'],
			['id' => '11', 'name' => 'GOAT', 'color' => '#94AFDA'],
		];

		foreach ($leagues as $data)
		{
			$league = new LeagueModel();
			$league->insert($data);
		}
	}
}
