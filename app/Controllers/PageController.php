<?php namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class PageController extends BaseController
{
	/**
	 * List of pages to load views for
	 * Initialised/defined by child classes in application/controllers
	 *
	 * @var	array
	 */
	protected $pages;

	protected $userModel;

	protected $helpers = ['html', 'number'];

	public function __construct()
	{
		$this->userModel = new User();
	}

	/**
	 * Remap
	 * This function overrides the normal controller behavior in which the URI
	 * determines which controller method is called; allowing us to define our
	 * own method routing rules.
	 *
	 * @param string $page - name of requested page
	 * @param array $params - for controller method call e.g. $page()
	 * @return PageNotFoundException|void
	 */
	public function _remap(string $page, array $params = [])
	{
		if (! in_array($page, $this->pages)) throw new PageNotFoundException();

		if (method_exists($this, $page))
			return call_user_func_array([$this, $page], $params);

		$this->outputPage($page);
	}

	/**
	 * Output Page
	 * Create and output a view using provided data
	 * At method CLOSE, most (or all) view-producing methods SHOULD call this
	 *
	 * @param string $page
	 * @param array $data - for the page
	 */
	protected function outputPage(string $page, array $data = [])
	{
		$data['loggedIn'] = $this->loggedIn;
		$data['title'] = $page;
		$data['styles'] = link_tag($page);
		$data['user'] = $this->userModel->getUser();
		$data['hiScore'] = $data['user'] ? $data['user']->hi_score : 0;
		$data['totalQP'] = $data['user'] ? $data['user']->total_qp : 0;

		$data['navItems']['main'] = [
			'game' => ['glyphicon' => 'equalizer', 'color' => 'danger'],
			'leagues' => ['glyphicon' => 'fire', 'color' => 'warning'],
			'races' => ['glyphicon' => 'random', 'color' => 'success'],
			'leaderboard' => ['glyphicon' => 'th-list', 'color' => 'info'],
			'contact' => ['glyphicon' => 'phone-alt', 'color' => 'primary']
		];

		if ($this->loggedIn)
		{
			$data['navItems']['profile'] = [
				'statistics' => ['glyphicon' => 'stats', 'color' => 'info'],
				'picture' => ['glyphicon' => 'camera', 'color' => 'success'],
				'password' => ['glyphicon' => 'eye-close', 'color' => 'warning'],
				'logout' => ['glyphicon' => 'log-out', 'color' => 'danger']
			];
		}
		else $data['navItems']['main']['login'] = [
			'glyphicon' => 'log-in',
			'color' => 'success'
		];

		$data['header'] = view('template/header', $data);
		$data['footer'] = view('template/footer');
		$data['pageContent'] = view("content/{$page}", $data);
		$data['mainbar'] = view('template/mainbar', $data);
		$data['sidebar'] = view('template/sidebar', $data);

		if ($page === 'game' && ENVIRONMENT === 'production')
			$data['scripts'] = script_tag('game.min');
		else $data['scripts'] = script_tag($page);

		echo view('template/html', $data);
	}
}
