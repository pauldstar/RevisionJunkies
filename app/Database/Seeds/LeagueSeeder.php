<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\League;

class LeagueSeeder extends Seeder
{
	public function run()
	{
		$this->db->table('league')->truncate();

		$leagues = [
			['name' => 'Butterfly', 'color' => '#57B9A8'],
			['name' => 'Rabbit', 'color' => '#7AC25B'],
			['name' => 'Deer', 'color' => '#F79B3C'],
			['name' => 'Zebra', 'color' => '#666666'],
			['name' => 'Buffalo', 'color' => '#E06666'],
			['name' => 'King Cobra', 'color' => '#A64D79'],
			['name' => 'Bald Eagle', 'color' => '#FFD700'],
			['name' => 'Grey Wolf', 'color' => '#D9D9D'],
			['name' => 'Grizzle Bear', 'color' => '#B45F06'],
			['name' => 'Great White Shark', 'color' => '#A4C2F4'],
			['name' => 'GOAT', 'color' => '#94AFDA'],
		];

		foreach ($leagues as $data)
		{
			$league = new League();
			$league->save($data);
		}
	}
}
