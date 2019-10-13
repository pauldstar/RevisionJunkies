<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages_public extends QP_Controller
{
	/**
	 * Class constructor
	 *
	 * Create list of public pages
	 * Each with view file matching its name
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		self::$pages = [
			'game',
			'leagues',
			'races',
			'leaderboard',
			'contact',
			'login',
			'server_info'
		];
	}

	/**
   * Login
   *
   * Response codes:
	 * 100 - login failed
	 * 200 - sign-up failed
   * 300 - email verified
	 * 400 - verify email
	 *
	 * @param int $response_code
   * @return void
   */
	public function login($response_code = '')
	{
		self::$logged_in AND redirect();

		$data['active_tab'] = 'login';
		$data['user_id'] = '';
		$data['email_verified'] = '';
		$data['email_unverified'] = '';

		switch ($response_code)
		{
			case '400':
				$data['email_unverified'] = 'active';
				$data['username'] = $this->user_model->unverified_username();
				break;
			case '300':
				$data['email_verified'] = 'active';
				break;
			case '200':
				$data['active_tab'] = 'signup';
			case '100':
				$this->load->library('form_validation');
				$this->form_validation->reload_data();
				break;
		}

		$this->load->helper('form');

		self::_output_page('login', $data);
	}

// TODO: remove server_info
	public function server_info()
	{
		echo '<pre>';
		var_dump($_SERVER);
		echo '</pre>';
	}
}
