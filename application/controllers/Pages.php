<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller
{
	private static $user_id;
	private static $unverified_data;

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		self::$user_id = &$_SESSION['user_id'];
		self::$unverified_data = &$_SESSION['unverified_data'];
	}

	public function game()
	{
	  $data['title'] = 'game';

    $data['styles'] = self::_load_asset('game', 'css');
    $data['logged_in'] = isset(self::$user_id);

    $data['header'] = $this->load->view('template/header', $data, TRUE);
    $data['page_content'] = $this->load->view('content/game', $data, TRUE);
    $data['footer'] = $this->load->view('template/footer', '', TRUE);

    if (ENVIRONMENT === 'development')
			$game_js = self::_load_asset('game', 'js');
		else $game_js = self::_load_asset('game.min', 'js');

    $data['scripts'] = $game_js;

    $this->load->view('template/html', $data);
	}

	public function races()
	{
		$data['title'] = 'races';

    $data['styles'] = '';
    $data['logged_in'] = isset(self::$user_id);

    $data['header'] = $this->load->view('template/header', $data, TRUE);
    $data['page_content'] = '';
    $data['footer'] = $this->load->view('template/footer', '', TRUE);

    $data['scripts'] = '';

		$this->load->view('template/html', $data);
	}

	public function leaderboard()
	{
		$data['title'] = 'leaderboard';

    $data['styles'] = '';
    $data['logged_in'] = isset(self::$user_id);

    $data['header'] = $this->load->view('template/header', $data, TRUE);
    $data['page_content'] = '';
    $data['footer'] = $this->load->view('template/footer', '', TRUE);

    $data['scripts'] = '';

		$this->load->view('template/html', $data);
	}

	public function contact()
	{
		$data['title'] = 'contact';

    $data['styles'] = '';
    $data['logged_in'] = isset(self::$user_id);

    $data['header'] = $this->load->view('template/header', $data, TRUE);
    $data['page_content'] = '';
    $data['footer'] = $this->load->view('template/footer', '', TRUE);

    $data['scripts'] = '';

		$this->load->view('template/html', $data);
	}

	public function login($response_code = '')
	{
		if (self::$user_id) redirect();

		$data['active_tab'] = 'login';

		switch ($response_code)
		{
			case '400':
				$data['email_unverified'] = TRUE;
				$data['email'] = self::$unverified_data['email'];
				break;
			case '300':
				$data['email_verified'] = TRUE;
				break;
			case '200':
				$data['active_tab'] = 'signup'; // no break
			case '100':
				$this->load->library('form_validation');
				$this->form_validation->reload_data();
				break;
		}

		$this->load->helper('form');

		$data['title'] = 'login';

    $login_css = self::_load_asset('login', 'css');
    $glyphicons = self::_load_asset('glyphicons.min', 'css');

    $data['styles'] = $login_css.$glyphicons;
		$data['logged_in'] = isset(self::$user_id);

    $data['header'] = $this->load->view('template/header', $data, TRUE);
    $data['page_content'] = $this->load->view('content/login', $data, TRUE);
    $data['footer'] = $this->load->view('template/footer', '', TRUE);

    $data['scripts'] = self::_load_asset('login', 'js');

		$this->load->view('template/html', $data);
	}

	public function server_info()
	{
		echo '<pre>';
		var_dump($_SERVER);
		echo '</pre>';
	}

	private function _load_asset($name, $ext)
	{
		$path = base_url("assets/{$ext}/{$name}.{$ext}");

		switch ($ext)
		{
			case 'css':
				return "<link href='{$path}' rel='stylesheet' type='text/css'>";

			case 'js':
				return "<script src='{$path}'></script>";
		}
	}
}
