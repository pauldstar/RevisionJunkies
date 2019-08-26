<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
  private static $user_id;
  private static $unverified_user_data;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    $this->load->model('user_model');
  }

  public function login()
  {
    $this->load->helper('url');

    $name = $this->input->post('login-name', TRUE);
    $password = $this->input->post('login-password', TRUE);
    isset($name) AND isset($password) OR redirect('login/100');

    $this->load->library('form_validation');

    $row = $this->user_model->get_user($name, ['username', 'email']);

    $user_exists = isset($row) && password_verify($password, $row->password);

    if (!$user_exists)
    {
      $this->form_validation->
        set_error('login_form', 'Login failed! Please try again...');
      $this->form_validation->save_data();
      redirect('login/100');
    }

    if ($row->email_verified === '0')
    {
      $this->user_model->set_email_verification_data(
        $row->username,
        $row->email,
        $row->email_verifier
      );
      redirect('login/400');
    }

    $this->user_model->login($row->user_id);

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

    $email_verifier = $this->user_model->create_user();

    if (!$email_verifier)
    {
      $this->form_validation->set_error('signup_form',
        'Server error. Sign Up failed! Please try again later.'
      );
      $this->form_validation->save_data();
      redirect('login/200');
    }

    self::send_email_verifier();
  }

  public function verify_email($username, $email_verifier)
  {
    $this->load->helper('url');
    $this->load->database();

    $this->db->select('email, email_verifier');
    $this->db->where('username', $username);
    $row = $this->db->get('user')->row();

    isset($row) OR redirect('login');

    if ($email_verifier !== $row->email_verifier)
    {
      self::_save_unverified_user_data(
        $username,
        $row->email,
        $row->email_verifier
      );
      redirect('login/400');
    }

    $this->db->set('email_verified', 1);
    $this->db->where('username', $username);
    $this->db->update('user');

    redirect('login/300');
  }

  public function send_email_verifier()
  {
    $this->load->helper('url');
    $this->load->library('email');

    $this->email->initialize([ 'mailtype' => 'html' ]);

    $this->email->subject('Email Verification');
    $this->email->from('admin@quepenny.com', 'QuePenny');

    $unverified_user_data = $_SESSION['unverified_user_data'];
    $data['username'] = $unverified_user_data['username'];
    $data['email_verifier'] = $unverified_user_data['email_verifier'];

    $this->email->to($unverified_user_data['email']);
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

    $unverified_user_data = $_SESSION['unverified_user_data'];
    $data['username'] = $unverified_user_data['username'];
    $data['email_verifier'] = $unverified_user_data['email_verifier'];

    $this->email->message(
      $this->load->view('template/verify_email', $data, TRUE)
    );

    $this->email->send();
  }

  public function show_email()
  {
    $unverified_user_data = $_SESSION['unverified_user_data'];
    $data['username'] = $unverified_user_data['username'];
    $data['email_verifier'] = $unverified_user_data['email_verifier'];

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
    $this->user_model->logout();
    $this->load->helper('url');
    redirect('login');
  }

  private function _save_unverified_user_data($username, $email, $email_verifier)
  {
    $_SESSION['unverified_user_data'] = [
      'username' => $username,
      'email' => $email,
      'email_verifier' => $email_verifier
    ];
  }
}
