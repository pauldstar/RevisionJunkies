<?php namespace App\Database\Seeds;

class QuestionSeeder extends BaseSeeder
{
	public function run()
	{
		$sql = self::loadSql('questions');
		$this->db->query($sql);
	}
}
