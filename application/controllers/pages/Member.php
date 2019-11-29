<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends QP_Pages_Controller
{
	/**
	 * Class constructor
	 *
	 * Create list of member-only pages
	 * Each having a view file matching its name in application/views/content
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
