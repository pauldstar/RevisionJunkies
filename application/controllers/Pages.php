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

		$hammer_js = self::load_asset('hammer.min', 'js');
		$md5_js = self::load_asset('md5.min', 'js');
    $game_js = self::load_asset('game', 'js');

    $data['scripts'] = $hammer_js.$md5_js.$game_js;

    $this->load->view('html', $data);
	}

	public function leaderboard()
	{

	}

	public function server_info()
	{
		echo '<pre>';
		var_dump($_SERVER);
		echo '</pre>';
	}
}
