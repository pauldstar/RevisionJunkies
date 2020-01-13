<?php  namespace App\Controllers;

use Config\Services;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class GuestPages extends PageController
{
	/**
	 * Class constructor
	 *
	 * Create list of guest pages
	 * Each having a view file matching its name in app/views/content
	 *
	 * Write all names in snake_case for use in routes
	 */
	public function __construct()
	{
		parent::__construct();

		$this->pages = [
			'game',
			'leagues',
			'races',
			'leaderboard',
			'contact',
			'login',
			'server_info',
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
	 * @param string $responseCode
	 * @return RedirectResponse|void
	 */
	public function login(string $responseCode = '')
	{
		if ($this->loggedIn) return redirect()->to('/');

		$data['activeTab'] = 'login';
		$data['userID'] = '';
		$data['emailVerified'] = '';
		$data['emailUnverified'] = '';

		switch ($responseCode)
		{
			case '400':
				$data['emailUnverified'] = 'active';
				$data['username'] = $this->userModel->unverifiedUsername();
				break;
			case '300':
				$data['emailVerified'] = 'active';
				break;
			case '200':
				$data['activeTab'] = 'signup';
				// don't break
			case '100':
				$data['validation'] = Services::validation();
		}

		helper('form');

		$this->outputPage('login', $data);
	}

	public function server_info($password = '')
	{
		if (ENVIRONMENT === 'production' && $password !== '')
			throw new PageNotFoundException();

		echo '<pre>';
		var_dump($_SERVER);
		echo '</pre>';
	}
}
