<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Defines 3 'base' controller classes
 *
 * - Main QP controller
 * - Test controller
 * - Page controller
 */

class QP_Controller extends CI_Controller
{
	/**
	 * Is user logged in or not?
	 *
	 * @var	bool
	 */
	protected static $logged_in;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model', '_user');
		self::$logged_in = !!$this->_user->get_user();
	}
}

class QP_Test_Controller extends QP_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		ENVIRONMENT === 'production' AND redirect();
		$this->load->library('unit_test');
		$this->unit->use_strict(TRUE);
	}
}

class QP_Page_Controller extends QP_Controller
{
	/**
	 * List of pages to load views for
	 * Initialised/defined by child classes in application/controllers
	 *
	 * @var	array
	 */
	protected static $pages;

  public function __construct()
  {
    parent::__construct();
		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->helper('number');
  }

	/**
   * Remap
	 * This function overrides the normal controller behavior in which the URI
	 * determines which controller method is called; allowing us to define our
	 * own method routing rules.
   *
	 * @param string $page - name of requested page
	 * @param array $params - for controller method call e.g. $page()
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
	 * @param string $page
	 * @param array $data - for the page
   * @return void
   */
	protected function _output_page($page, $data = [])
	{
		$data['logged_in'] = self::$logged_in;
		$data['title'] = $page;
		$data['styles'] = css_tag($page);
		$data['user'] = $this->_user->get_user();
		$data['hi_score'] = $data['user'] ? $data['user']->hi_score : 0;
		$data['total_qp'] = $data['user'] ? $data['user']->total_qp : 0;

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
		}
		else $data['nav_items']['main']['login'] = [
			'glyphicon' => 'log-in',
			'color' => 'success'
		];

		$data['header'] = $this->load->view('template/header', $data, TRUE);
		$data['footer'] = $this->load->view('template/footer', '', TRUE);
		$data['page_content'] = $this->load->view("content/{$page}", $data, TRUE);
		$data['mainbar'] = $this->load->view('template/mainbar', $data, TRUE);
		$data['sidebar'] = $this->load->view('template/sidebar', $data, TRUE);

		if ($page === 'game' && ENVIRONMENT === 'production')
			$data['scripts'] = script_tag('game.min');
		else $data['scripts'] = script_tag($page);

		$this->load->view('template/html', $data);
	}
}
