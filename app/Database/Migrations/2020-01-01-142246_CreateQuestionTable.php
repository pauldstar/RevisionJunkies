<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuestionTable extends Migration
{
	public function up()
	{
		$fields = [
			'id' => [
				'type' => 'INT',
				'unsigned' => true,
				'auto_increment' => true
			],
			'user_id' => [
				'type' => 'INT',
				'unsigned' => true,
				'default' => 1
			],
			'question' => [
				'type' => 'TEXT',
			],
			'category' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'type' => [
				'type' => 'VARCHAR',
				'constraint' => 8,
				'comment' => 'boolean/multiple'
			],
			'difficulty' => [
				'type' => 'VARCHAR',
				'constraint' => 6,
				'comment' => 'easy/medium/hard'
			],
			'correct_answer' => [
				'type' => 'TEXT',
			],
			'incorrect_answers' => [
				'type' => 'TEXT',
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
		$this->forge->addForeignKey('user_id', 'user', 'id', 'CASCADE', 'CASCADE');
		$this->forge->createTable('question', false, ['ENGINE' => 'InnoDB']);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('question');
	}
}
