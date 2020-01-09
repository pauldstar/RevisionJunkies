<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaseSeeder extends Seeder
{
	protected static function loadSql(string $filename) : string
	{
		return file_get_contents(APPPATH."Database/Seeds/SQL/{$filename}.sql");
	}
}
