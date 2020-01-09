<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLeagueColUserTable extends Migration
{
	public function up()
	{
		$this->forge->setForeignKey('user', 'league_id', 'league', 'id');
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropForeignKey('user', 'league_id', true);
	}
}
