<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
  private static $user_id;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    self::$user_id = &$_SESSION['user_id'];
  }

  public function login()
  {
    $this->load->helper('url');

    $name = $this->input->post('login-name');
    $password = $this->input->post('login-password');
    isset($name) AND isset($password) OR redirect('login/100');

    $this->load->library('form_validation');
    $this->load->database();

    $this->db->select('user_id, password');
    $this->db->where('username', $name);
    $this->db->or_where('email', $name);
    $query = $this->db->get('user');
    $row = $query->row_array();

    $user_exists = isset($row) && password_verify($password, $row['password']);

    if (!$user_exists)
    {
      $this->form_validation->set_error('login_form',
        'Login failed! Please try again...'
      );
      $this->form_validation->save_data();
      redirect('login/100');
    }

    self::$user_id = $row['user_id'];

		$this->db->set('logged_in', 1);
		$this->db->where('user_id', self::$user_id);
		$this->db->update('user');

		redirect();
  }

  public function signup()
  {
    $this->load->helper('url');
    $this->load->library('form_validation');
    $this->load->database();

    $this->form_validation->set_rules(
      'signup-firstname', 'First-Name', 'required'.
      "|regex_match[/^[A-Za-z][A-Za-z]*(?:-[A-Za-z]+)*(?:'[A-Za-z]+)*$/]"
    );
    $this->form_validation->set_rules(
      'signup-lastname', 'Last-Name', 'required'.
      "|regex_match[/^[A-Za-z][A-Za-z]*(?:-[A-Za-z]+)*(?:'[A-Za-z]+)*$/]"
    );
    $this->form_validation->set_rules(
      'signup-password', 'Password', 'required|min_length[8]'
    );
    $this->form_validation->set_rules(
      'signup-email', 'Email', 'required|valid_email|is_unique[user.email]'
    );
    $this->form_validation->set_rules(
      'signup-username', 'Username', 'required|max_length[20]'.
      '|regex_match[/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/]'.
      '|is_unique[user.username]'
    );

    if (!$this->form_validation->run())
    {
      $this->form_validation->save_data();
      redirect('login/200');
    }

    $password_hash = password_hash(
      $this->input->post('signup-password'), PASSWORD_BCRYPT
    );

    $params = [
			'username' => $this->input->post('signup-username', TRUE),
			'password' => $password_hash,
			'email' => $this->input->post('signup-email', TRUE),
			'firstname' => $this->input->post('signup-firstname', TRUE),
			'lastname' => $this->input->post('signup-lastname', TRUE)
		];

    $query = $this->db->insert('user', $params);

    if (!$query)
    {
      $this->form_validation->set_error('signup_form',
        'Server error. Sign Up failed! Please try again later.'
      );
      $this->form_validation->save_data();
      redirect('login/200');
    }

    self::$user_id = $this->db->insert_id();

		redirect();
  }

  public function is_unique($input_type)
  {
    $input_text = $_GET['inputText'];

    $this->load->database();
    $this->load->library('form_validation');

    $is_unique = $this->form_validation->is_unique(
      $input_text, "user.{$input_type}"
    );

    echo json_encode([ 'response' => $is_unique ]);
  }

	public function logout()
	{
    $this->load->helper('url');

		$_SESSION = [];

    $session_running = session_id() != "" || isset($_COOKIE[session_name()]);

    if ($session_running)
    {
      session_unset();
      //setcookie(session_name(), '', time() - 2592000, '/');
      session_destroy();
    }

    redirect('login');
	}
}
