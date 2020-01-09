<?php namespace App\Libraries;

class MigrationRunner extends \CodeIgniter\Database\MigrationRunner
{
	/**
	 * Grabs the full migration history from the database for a group
	 *
	 * @override setting runner default group to Config\database default group
	 *
	 * @param string $group
	 * @return array
	 */
	public function getHistory(string $group = null): array
	{
		$group === null AND $group = $this->group;
		return parent::getHistory($group);
	}

	//--------------------------------------------------------------------
}
