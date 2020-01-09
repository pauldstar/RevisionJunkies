<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailVerifierTable extends Migration
{
	public function up()
	{
		$fields = [
			'user_id' => [
				'type' => 'INT',
				'unsigned' => true,
			],
			'verifier' => [
				'type' => 'CHAR',
				'constraint' => 10
			],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('user_id', true);
		$this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');
		$this->forge->createTable('email_verifier', false, ['ENGINE' => 'InnoDB']);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('email_verifier');
	}
}
