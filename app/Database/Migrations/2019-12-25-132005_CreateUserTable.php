<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTable extends Migration
{
	public function up()
	{
		$fields = [
			'id' => [
				'type' => 'INT',
				'unsigned' => true,
				'auto_increment' => true
			],
			'username' => [
				'type' => 'VARCHAR',
				'constraint' => 20,
				'unique' => true
			],
			'password' => [
				'type' => 'CHAR',
				'constraint' => 60,
			],
			'email' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'unique' => true
			],
			'firstname' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
			],
			'lastname' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
			],
			'hi_score' => [
				'type' => 'SMALLINT',
				'unsigned' => true,
				'null' => true,
			],
			'total_qp' => [
				'type' => 'INT',
				'unsigned' => true,
				'null' => true,
			],
			'league_id' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => true,
				'default' => 1
			],
			'created_at' => [
				'type' => 'TIMESTAMP',
			],
			'updated_at' => [
				'type' => 'TIMESTAMP',
			]
		];

		$this->forge->addField($fields);
		$this->forge->addKey('id', true);
		$this->forge->createTable('user', false, ['ENGINE' => 'InnoDB']);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('user');
	}
}
