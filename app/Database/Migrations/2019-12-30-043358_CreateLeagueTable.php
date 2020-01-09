<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeagueTable extends Migration
{
	public function up()
	{
		$fields = [
			'id' => [
				'type' => 'TINYINT',
				'constraint' => '1',
				'unsigned' => true,
				'auto_increment' => true
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 17
			],
			'color' => [
				'type' => 'CHAR',
				'constraint' => 7
			],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id', true);
		$this->forge->createTable('league', false, ['ENGINE' => 'InnoDB']);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('league');
	}
}
