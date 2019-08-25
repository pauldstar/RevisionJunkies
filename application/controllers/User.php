<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
  private static $user_id;
  private static $unverified_data;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    self::$user_id = &$_SESSION['user_id'];
    self::$unverified_data = &$_SESSION['unverified_data'];
  }

  public function login()
  {
    $this->load->helper('url');

    $name = $this->input->post('login-name');
    $password = $this->input->post('login-password');
    isset($name) AND isset($password) OR redirect('login/100');

    $this->load->library('form_validation');
    $this->load->database();

    $this->db->select(
      'user_id, username, password, email, email_verifier, email_verified'
    );
    $this->db->where('username', $name);
    $this->db->or_where('email', $name);
    $user = $this->db->get('user')->row();

    $user_exists = isset($user) && password_verify($password, $user->password);

    if (!$user_exists)
    {
      $this->form_validation->
        set_error('login_form', 'Login failed! Please try again...');
      $this->form_validation->save_data();
      redirect('login/100');
    }

    if ($user->email_verified === '0')
    {
      self::_save_unverified_data(
        $user->username,
        $user->email,
        $user->email_verifier
      );
      redirect('login/400');
    }

    self::$user_id = $user->user_id;

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

    $email_verifier = substr(md5($this->input->post('signup-email')), 11, 20);

    $password_hash =
      password_hash($this->input->post('signup-password'), PASSWORD_BCRYPT);

    $params = [
      'username' => $this->input->post('signup-username', TRUE),
      'password' => $password_hash,
      'email' => $this->input->post('signup-email', TRUE),
      'firstname' => $this->input->post('signup-firstname', TRUE),
      'lastname' => $this->input->post('signup-lastname', TRUE),
      'email_verifier' => $email_verifier
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

    self::_save_unverified_data(
      $this->input->post('signup-username'),
      $this->input->post('signup-email'),
      $email_verifier
    );

    self::send_email_verifier();
  }

  public function verify_email($username, $email_verifier)
  {
    $this->db->select('email, email_verifier');
    $this->db->where('username', $username);
    $user = $this->db->get('user')->row();

    isset($user) OR redirect('login');

    if ($email_verifier !== $user->email_verifier)
    {
      self::_save_unverified_data(
        $username,
        $user->email,
        $user->email_verifier
      );
      redirect('login/400');
    }

    $this->db->set('email_verified', 1);
    $this->db->where('username', self::$username);
    $this->db->update('user');

    redirect('login/300');
  }

  public function send_email_verifier()
  {
    isset($email) OR redirect('login/400');

    $this->load->library('email');

    $this->email->initialize([ 'mailtype' => 'html' ]);

    $this->email->from('admin@quepenny.com', 'QuePenny');
    $this->email->to($email);
    $this->email->subject('Email Verification');

    $unverified_data = $_SESSION['unverified_data'];
    $data['username'] = $unverified_data['username'];
    $data['email_verifier'] = $unverified_data['email_verifier'];

    $this->email->message(
      $this->load->view('template/verify_email', $data, TRUE)
    );

    $this->email->send();

    redirect('login/400');
  }

  // TODO: remove test_email() and show_email()
  public function test_email()
  {
    $this->load->library('email');

    $this->email->initialize([ 'mailtype' => 'html' ]);

    $this->email->from('admin@quepenny.com', 'QuePenny');
    $this->email->to('paulogbeiwi@gmail.com');
    $this->email->subject('Email Verification');

    $unverified_data = $_SESSION['unverified_data'];
    $data['username'] = $unverified_data['username'];
    $data['email_verifier'] = $unverified_data['email_verifier'];

    $this->email->message(
      $this->load->view('template/verify_email', $data, TRUE)
    );

    $this->email->send();
  }

  public function show_email()
  {
    $unverified_data = $_SESSION['unverified_data'];
    $data['username'] = $unverified_data['username'];
    $data['email_verifier'] = $unverified_data['email_verifier'];

    $this->load->view('template/verify_email', $data);
  }

  public function is_valid($input_type)
  {
    $input_text = $_POST['inputText'];

    $this->load->library('form_validation');

    $invalid_email = $input_type === 'email' &&
      !$this->form_validation->valid_email($input_text);

    if ($invalid_email)
    {
      echo json_encode([ 'response' => FALSE ]);
      die();
    }

    $this->load->database();

    $is_unique = $this->form_validation->
      is_unique($input_text, "user.{$input_type}");

    echo json_encode([ 'response' => $is_unique ]);
  }

  public function logout()
  {
    $this->load->helper('url');
    session_destroy();
    redirect('login');
  }

  private function _save_unverified_data($username, $email, $email_verifier)
  {
    $_SESSION['unverified_data'] = [
      'username' => $username,
      'email' => $email,
      'email_verifier' => $email_verifier
    ];
  }
}
