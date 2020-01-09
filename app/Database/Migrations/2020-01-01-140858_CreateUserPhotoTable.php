<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserPhotoTable extends Migration
{
	public function up()
	{
		$fields = [
			'user_id' => [
				'type' => 'INT',
				'unsigned' => true,
			],
			'file_name' => [
				'type' => 'VARCHAR',
				'constraint' => 255
			],
		];

		$this->forge->addField($fields);
		$this->forge->addKey('user_id', true);
		$this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');
		$this->forge->createTable('user_photo', false, ['ENGINE' => 'InnoDB']);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('user_photo');
	}
}
