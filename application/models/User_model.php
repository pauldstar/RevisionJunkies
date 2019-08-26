<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
  private static $user_id;
  private static $email_verification_data;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    self::$user_id = &$_SESSION['user_id'];
    self::$email_verification_data = &$_SESSION['email_verification_data'];
  }

  public function id($id = NULL)
  {
    if ($id) self::$user_id = $id;
    return self::$user_id;
  }

  public function get_user($id, $id_type)
  {
    $this->load->database();

    $this->db->select(
      'user_id, username, password, email, email_verifier, email_verified'
    );

    if (is_array($id_type))
    {
      $id_type = array_map(function($type) use($id)
      {
        return "{$type} = '{$id}'";
      }, $id_type);

      $clause = implode(' OR ', $id_type);
      $this->db->where($clause);
    }
    else $this->db->where($id_type, $id);

    return $this->db->get('user')->row();
  }

  public function create_user()
  {
    $this->load->database();

    $post = $this->input->post(NULL, TRUE);

    $email_verifier = substr(md5($post['signup-email']), 11, 20);
    $password_hash = password_hash($post['signup-password'], PASSWORD_BCRYPT);

    $params = [
      'username' => $post['signup-username'],
      'password' => $password_hash,
      'email' => $post['signup-email'],
      'firstname' => $post['signup-firstname'],
      'lastname' => $post['signup-lastname'],
      'email_verifier' => $email_verifier
    ];

    $query = $this->db->insert('user', $params);
    if (!$query) return NULL;

    self::set_email_verification_data(
      $post['signup-username'],
      $post['signup-email'],
      $email_verifier
    );

    return $email_verifier;
  }

  public function login($user_id)
  {
    self::$user_id = $user_id;

    $this->load->database();
    $this->db->set('logged_in', 1);
    $this->db->where('user_id', $user_id);
    $this->db->update('user');
  }

  public function logout()
  {
    $this->load->database();
    $this->db->set('logged_in', 0);
    $this->db->where('user_id', self::$user_id);
    $this->db->update('user');

    session_destroy();
  }

  public function get_email_verification_data($key)
  {
    if ($key) return self::$email_verification_data[$key];
    return self::$email_verification_data;
  }

  public function set_email_verification_data($username, $email, $verifier)
  {
    self::$email_verification_data = [
      'username' => $username,
      'email' => $email,
      'email_verifier' => $email_verifier
    ];
  }
}
