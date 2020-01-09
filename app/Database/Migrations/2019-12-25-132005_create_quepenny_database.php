<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;
use Config\Database;

class CreateQuepennyDatabase extends Migration
{
	public function up()
	{
		$this->forge->createDatabase('quepenny');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropDatabase('quepenny');
	}
}
