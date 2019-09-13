<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('user_model');
	}

	public function game()
	{
	  $data['title'] = 'game';

    $data['styles'] = self::_load_asset('game', 'css');
    $data['logged_in'] = $this->user_model->user_id();

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
    $data['logged_in'] = $this->user_model->user_id();

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
    $data['logged_in'] = $this->user_model->user_id();

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
    $data['logged_in'] = $this->user_model->user_id();

    $data['header'] = $this->load->view('template/header', $data, TRUE);
    $data['page_content'] = '';
    $data['footer'] = $this->load->view('template/footer', '', TRUE);

    $data['scripts'] = '';

		$this->load->view('template/html', $data);
	}

	public function login($response_code = '')
	{
		if ($this->user_model->user_id()) redirect();

		$data['active_tab'] = 'login';
		$data['user_id'] = $data['email_verified'] = $data['email_unverified'] = '';

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
				$data['active_tab'] = 'signup'; // no break
			case '100':
				$this->load->library('form_validation');
				$this->form_validation->reload_data();
				break;
		}

		$this->load->helper('form');

		$data['title'] = 'login';

    $login_css = self::_load_asset('login', 'css');
    $glyphicons_css = self::_load_asset('glyphicons.min', 'css');

    $data['styles'] = $login_css.$glyphicons_css;
		$data['logged_in'] = $this->user_model->user_id();

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
