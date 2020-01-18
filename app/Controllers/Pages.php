<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;

class Pages extends Controller
{
  /**
   * List of pages to load views for
   * Initialised/defined by child classes in application/controllers
   *
   * @var  array
   */
  protected $pages;

  protected $userModel;

  protected $helpers = ['html', 'number'];

  /**
   * Class constructor
   *
   * Create list of pages that are NOT EXPLICITLY called with methods
   * Each having a view file matching its name in app/views/content
   *
   * Write all page names in snake_case for use in routes
   */
  public function __construct()
  {
    $this->userModel = new UserModel();

    $this->pages = [
      'game',
      'leagues',
      'races',
      'leaderboard',
      'contact',
      'statistics',
      'picture',
      'password'
    ];
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
  public function _remap(string $page, ...$params)
  {
    if (method_exists($this, $page)) return $this->$page(...$params);

    if (!in_array($page, $this->pages))
      throw PageNotFoundException::forPageNotFound();

    $this->outputPage($page);
  }

  //--------------------------------------------------------------------

  /**
   * Login
   *
   * Response codes:
   * 100 - login failed
   * 200 - sign-up failed
   * 300 - email verified
   * 400 - verify email
   *
   * @param string $responseCode
   * @return RedirectResponse|void
   */
  public function login(string $responseCode = '')
  {
    if ($this->userModel->isLoggedIn()) return redirect()->to('/');

    $data = [
      'activeTab' => 'login',
      'validation' => null,
      'userId' => '',
      'username' => '',
      'emailVerified' => '',
      'emailUnverified' => ''
    ];

    switch ($responseCode)
    {
      case '400':
        $data['emailUnverified'] = 'active';
        $data['username'] = $this->userModel->unverifiedUsername();
        break;
      case '300':
        $data['emailVerified'] = 'active';
        break;
      case '200':
        $data['activeTab'] = 'signup';
      case '100': // don't break
        $data['validation'] = Services::validation();
    }

    helper('form');

    $this->outputPage('login', $data);
  }

  //--------------------------------------------------------------------

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
    $data['loggedIn'] = $this->userModel->isLoggedIn();
    $data['title'] = $page;
    $data['styles'] = link_tag($page);
    $data['user'] = $this->userModel->getUser();
    $data['hiScore'] = optional($data['user'])->hi_score ?? 0;
    $data['totalQP'] = optional($data['user'])->total_qp ?? 0;

    $data['navItems']['main'] = [
      'game' => ['glyphicon' => 'equalizer', 'color' => 'danger'],
      'leagues' => ['glyphicon' => 'fire', 'color' => 'warning'],
      'races' => ['glyphicon' => 'random', 'color' => 'success'],
      'leaderboard' => ['glyphicon' => 'th-list', 'color' => 'info'],
      'contact' => ['glyphicon' => 'phone-alt', 'color' => 'primary']
    ];

    if ($data['loggedIn'])
    {
      $data['navItems']['profile'] = [
        'statistics' => ['glyphicon' => 'stats', 'color' => 'info'],
        'picture' => ['glyphicon' => 'camera', 'color' => 'success'],
        'password' => ['glyphicon' => 'eye-close', 'color' => 'warning'],
        'logout' => ['glyphicon' => 'log-out', 'color' => 'danger']
      ];
    } else $data['navItems']['main']['login'] = [
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

  //--------------------------------------------------------------------

  public function server_info($password = '')
  {
    if (ENVIRONMENT === 'production' && $password !== '')
      throw new PageNotFoundException();

    echo '<pre>';
    var_dump($_SERVER);
    echo '</pre>';
  }
}
