<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends QP_Controller
{
	public function game()
	{
	  $data['title'] = 'game';

    $data['styles'] = self::load_asset('game', 'css');

    $data['header'] = $this->load->view('header', $data, TRUE);
    $data['page_content'] = $this->load->view('game', NULL, TRUE);
    $data['footer'] = $this->load->view('footer', NULL, TRUE);

    if (ENVIRONMENT === 'development')
			$game_js = self::load_asset('game', 'js');
		else $game_js = self::load_asset('game.min', 'js');

    $data['scripts'] = $game_js;

    $this->load->view('html', $data);
	}

	public function prizes()
	{
		$data['title'] = 'races';

    $data['styles'] = '';

    $data['header'] = $this->load->view('header', $data, TRUE);
    $data['page_content'] = '';
    $data['footer'] = $this->load->view('footer', NULL, TRUE);

    $data['scripts'] = '';

		$this->load->view('html', $data);
	}

	public function leaderboard()
	{
		$data['title'] = 'leaderboard';

    $data['styles'] = '';

    $data['header'] = $this->load->view('header', $data, TRUE);
    $data['page_content'] = '';
    $data['footer'] = $this->load->view('footer', NULL, TRUE);

    $data['scripts'] = '';

		$this->load->view('html', $data);
	}

	public function contact()
	{
		$data['title'] = 'contact';

    $data['styles'] = '';

    $data['header'] = $this->load->view('header', $data, TRUE);
    $data['page_content'] = '';
    $data['footer'] = $this->load->view('footer', NULL, TRUE);

    $data['scripts'] = '';

		$this->load->view('html', $data);
	}

	public function login()
	{
		$data['title'] = 'login';

    $data['styles'] = '';

    $data['header'] = $this->load->view('header', $data, TRUE);
    $data['page_content'] = '';
    $data['footer'] = $this->load->view('footer', NULL, TRUE);

    $data['scripts'] = '';

		$this->load->view('html', $data);
	}

	public function server_info()
	{
		echo '<pre>';
		var_dump($_SERVER);
		echo '</pre>';
	}
}
