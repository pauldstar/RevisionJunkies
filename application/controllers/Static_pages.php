<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Static_pages extends CI_Controller
{
	public function index()
	{
    $data['title'] = 'game';

    $data['styles'] = $this->load->view('css/game', NULL, TRUE);

    $data['header'] = $this->load->view('header', NULL, TRUE);
    $data['page_content'] = $this->load->view('game', NULL, TRUE);
    $data['footer'] = $this->load->view('footer', NULL, TRUE);

    $hammer_js = $this->load->view('js/hammer', NULL, TRUE);
    $game_js = $this->load->view('js/game', NULL, TRUE);

    $data['scripts'] = $hammer_js.$game_js;

    $this->load->view('html', $data);
	}
}
