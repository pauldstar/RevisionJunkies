<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QP_Controller extends CI_Controller
{
	/**
	 * Is user logged in or not?
	 *
	 * @var	bool
	 */
	protected static $logged_in;

	/**
	 * List of pages to load views for
	 * Initialised by child classes
	 *
	 * @var	array
	 */
	protected static $pages;

	/**
	 * Class constructor
	 *
	 * Save if user is logged in
	 *
	 * @return void
	 */
  public function __construct()
  {
    parent::__construct();

		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->model('user_model');

		self::$logged_in = $this->user_model->get_user() ? TRUE : FALSE;
  }

	/**
   * Remap
	 * All controller calls go through this function first
   *
	 * @param string $page - name of requested page
	 * @param array $params - to pass in page method call e.g. $page()
   * @return callback|void
   */
	public function _remap($page, $params = [])
	{
		in_array($page, self::$pages) OR show_404();

		if (method_exists($this, $page))
			return call_user_func_array([$this, $page], $params);

		self::_output_page($page);
	}

	/**
   * Output Page
   * Create and output a view using provided data
   * At method CLOSE, most (or all) view-producing methods SHOULD call this
	 *
	 * @param array $page - for the page
	 * @param array $data - for the page
   * @return void
   */
	protected function _output_page($page, $data = [])
	{
		$data['logged_in'] = self::$logged_in;
		$data['title'] = $page;
		$data['styles'] = css_tag($page);

		$data['nav_items']['main'] = [
			'game' => ['glyphicon' => 'equalizer', 'color' => 'danger'],
			'leagues' => ['glyphicon' => 'fire', 'color' => 'warning'],
			'races' => ['glyphicon' => 'random', 'color' => 'success'],
			'leaderboard' => ['glyphicon' => 'th-list', 'color' => 'info'],
			'contact' => ['glyphicon' => 'phone-alt', 'color' => 'primary']
		];

		if (self::$logged_in)
		{
			$data['nav_items']['profile'] = [
				'statistics' => ['glyphicon' => 'stats', 'color' => 'info'],
				'picture' => ['glyphicon' => 'camera', 'color' => 'success'],
				'password' => ['glyphicon' => 'eye-close', 'color' => 'warning'],
				'logout' => ['glyphicon' => 'log-out', 'color' => 'danger']
			];

			$data['user'] = $this->user_model->get_user();
			$data['hi_score'] = 100;
		}
		else $data['nav_items']['main']['login'] = [
			'glyphicon' => 'log-in',
			'color' => 'success'
		];

		$data['page_content'] = $this->load->view(
			"content/{$page}", $data, TRUE
		);

		if ($page === 'game' && ENVIRONMENT === 'production')
			$data['scripts'] = script_tag('game.min');
		else $data['scripts'] = script_tag($page);

		$data['header'] = $this->load->view('template/header', $data, TRUE);
		$data['footer'] = $this->load->view('template/footer', '', TRUE);

		$data['mainbar'] = $this->load->view('template/mainbar', $data, TRUE);
		$data['sidebar'] = $this->load->view('template/sidebar', $data, TRUE);

		$this->load->view('template/html', $data);
	}
}
