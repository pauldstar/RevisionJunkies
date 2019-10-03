<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages_public extends QP_Controller
{
	public function game()
	{
	  $data['title'] = 'game';
    $data['styles'] = self::_load_asset('game', 'css');
    $data['page_content'] = $this->load->view('content/game', $data, TRUE);
    $data['footer'] = $this->load->view('template/footer', '', TRUE);

    if (ENVIRONMENT === 'development')
			$game_js = self::_load_asset('game', 'js');
		else $game_js = self::_load_asset('game.min', 'js');

    $data['scripts'] = $game_js;

		self::_output_page($data);
	}

	public function races()
	{
		$data['title'] = 'races';
		$data['styles'] = '';
    $data['page_content'] = '';
    $data['scripts'] = '';
		self::_output_page($data);
	}

	public function leagues()
	{
		$data['title'] = 'leagues';
    $data['styles'] = '';
		$data['page_content'] = '';
		$data['scripts'] = '';
		self::_output_page($data);
	}

	public function leaderboard()
	{
		$data['title'] = 'leaderboard';
    $data['styles'] = '';
		$data['page_content'] = '';
		$data['scripts'] = '';
		self::_output_page($data);
	}

	public function contact()
	{
		$data['title'] = 'contact';
    $data['styles'] = '';
    $data['page_content'] = '';
    $data['scripts'] = '';
		self::_output_page($data);
	}

	public function login($response_code = '')
	{
		if (self::$logged_in) redirect();

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

    $data['page_content'] = $this->load->view('content/login', $data, TRUE);
    $data['scripts'] = self::_load_asset('login', 'js');
		self::_output_page($data);
	}

	public function server_info()
	{
		echo '<pre>';
		var_dump($_SERVER);
		echo '</pre>';
	}
}
