<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QP_Controller extends CI_Controller
{
	protected static $logged_in;

  public function __construct($members_only = FALSE)
  {
    parent::__construct();

		$this->load->helper('url');
		$this->load->model('user_model');

		self::$logged_in = $this->user_model->get_user() ? TRUE : FALSE;
  }

	protected function _load_asset($name, $ext)
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

	/**
   * Initialise and retrieve mandatory page $data
   *
   * @return array
   */
	protected function _init_page_data()
	{
		$data['logged_in'] = self::$logged_in;

		$data['nav_items']['main'] = [
			'game' => [
				'glyphicon' => 'equalizer',
				'color' => 'danger'
			],
			'leagues' => [
				'glyphicon' => 'fire',
				'color' => 'warning'
			],
			'races' => [
				'glyphicon' => 'random',
				'color' => 'success'
			],
			'leaderboard' => [
				'glyphicon' => 'th-list',
				'color' => 'info'
			],
			'contact' => [
				'glyphicon' => 'phone-alt',
				'color' => 'primary'
			]
		];

		if (self::$logged_in)
		{
			$data['nav_items']['profile'] = [
				'statistics' => [
					'glyphicon' => 'stats',
					'color' => 'info'
				],
				'picture' => [
					'glyphicon' => 'camera',
					'color' => 'success'
				],
				'password' => [
					'glyphicon' => 'eye-close',
					'color' => 'warning'
				],
				'logout' => [
					'glyphicon' => 'log-out',
					'color' => 'danger'
				]
			];

			$data['user'] = $this->user_model->get_user();
		}
		else $data['nav_items']['main']['login'] = [
			'glyphicon' => 'log-in',
			'color' => 'success'
		];

		return $data;
	}

	/**
   * Create and output a view using it's data
   *
   * @return void
   */
	protected function _output_page($data)
	{
		$data['header'] = $this->load->view('template/header', $data, TRUE);
		$data['footer'] = $this->load->view('template/footer', '', TRUE);

		$data['mainbar'] = $this->load->view('template/mainbar', $data, TRUE);
		$data['sidebar'] = $this->load->view('template/sidebar', $data, TRUE);

		$this->load->view('template/html', $data);
	}
}
