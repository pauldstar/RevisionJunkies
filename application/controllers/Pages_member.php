<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages_member extends QP_Controller
{
	/**
	 * Class constructor
	 *
	 * Create list of member-only pages
	 * Each with view file matching its name
	 *
	 * @return void
	 */
	public function __construct()
  {
		parent::__construct();
		self::$logged_in OR redirect();

		self::$pages = [
			'statistics',
			'picture',
			'password'
		];
  }
}
