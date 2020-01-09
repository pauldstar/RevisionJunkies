<?php namespace App\Controllers;

class MemberPages extends PageController
{
	/**
	 * Class constructor
	 *
	 * Create list of member-only pages
	 * Each having a view file matching its name in app/views/content
	 *
	 * Write all names in snake_case for use in routes
	 */
	public function __construct()
  {
		parent::__construct();

		if ($this->loggedIn) redirect()->to('/');

		$this->pages = [
			'statistics',
			'picture',
			'password'
		];
  }
}
