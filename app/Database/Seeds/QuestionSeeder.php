<?php namespace App\Database\Seeds;

class QuestionSeeder extends BaseSeeder
{
	public function run()
	{
		$sql = self::loadSQL('questions');
		$this->db->query($sql);
	}
}
