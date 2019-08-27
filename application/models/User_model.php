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

  public function user_id($id = NULL)
  {
    if ($id) self::$user_id = $id;
    return self::$user_id;
  }

  public function get_user($id, $id_type, $for_login = FALSE)
  {
    $this->load->database();

    $selections = 'user.user_id, username, password, email, email_verified';
    $for_login AND $selections .= ', verifier';

    $this->db->select($selections);
    $this->db->from('user');

    $for_login AND $this->db->
      join('email_verifier', 'email_verifier.user_id = user.user_id', 'left');

    if (is_array($id_type))
    {
      $id_type = array_map(function($type) use($id)
      {
        return "{$type} = '{$id}'";
      }, $id_type);

      $clause = implode(' OR ', $id_type);
      $this->db->where($clause);
    }
    else $this->db->where("user.{$id_type}", $id);

    return $this->db->get()->row();
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
      'lastname' => $post['signup-lastname']
    ];

    $this->db->trans_start();

    $this->db->insert('user', $params);

    $params = [
      'user_id' => $this->db->insert_id(),
      'verifier' => $email_verifier
    ];

    $this->db->insert('email_verifier', $params);

    $this->db->trans_complete();

    if (!$this->db->trans_status()) return FALSE;

    self::set_email_verification_data(
      $post['signup-username'],
      $post['signup-email'],
      $email_verifier
    );

    return TRUE;
  }

  public function confirm_email_verification($user_id)
  {
    $this->load->database();

    $this->db->trans_start();

    $this->db->set('email_verified', 1);
    $this->db->where('user_id', $user_id);
    $this->db->update('user');

    $this->db->where('user_id', $user_id);
    $this->db->delete('email_verifier');

    $this->db->trans_complete();

    return $this->db->trans_status();
  }

  public function login($user_id)
  {
    self::$user_id = $user_id;

    $this->load->database();
    $this->db->set('last_login_time', 'CURRENT_TIMESTAMP');
    $this->db->where('user_id', $user_id);
    $this->db->update('user');
  }

  public function get_email_verification_data($key = NULL)
  {
    if ($key) return self::$email_verification_data[$key];
    return self::$email_verification_data;
  }

  public function set_email_verification_data($user_id, $email, $verifier)
  {
    self::$email_verification_data = [
      'user_id' => $user_id,
      'email' => $email,
      'email_verifier' => $verifier
    ];
  }
}
