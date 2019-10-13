<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
  /**
   * User data object
   *
   * @var	object
   */
  private static $user;

  /**
   * Username of currently unverified user
   *
   * @var	string
   */
  private static $unverified_username;

  /**
	 * Class constructor
	 *
	 * Initialise class variables with session variables
	 *
	 * @return void
	 */
  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    self::$user = &$_SESSION['user'];
    self::$unverified_username = &$_SESSION['unverified_username'];
  }

  /**
   * Set & get the username for an unverified email
   *
   * @param string $username
   * @return void set
   * @return string get
   */
  public function unverified_username($username = NULL)
  {
    if ($username) self::$unverified_username = $username;
    else return self::$unverified_username;
  }

  /**
   * Retrieve user details from the database or session
   *
   * @param mixed $id - text/number
   * @param mixed $id_type - user_id/username/email or an array
   * @param bool $save_session - set TRUE and save user data in session
   * @return object
   */
  public function get_user($id = '', $id_type = 'user_id', $save_session = FALSE)
  {
    if (empty($id)) return self::$user;

    $this->load->database();

    $selections = '
      user.user_id,
      user.username,
      user.password,
      user.email,
      user.email_verified,
      user.total_qp,
      user_photo.filename AS photo,
      user_email_verifier.verifier AS email_verifier,
      user_league.name AS league_name,
      user_league.color AS league_color
    ';

    $this->db->select($selections);
    $this->db->from('user');

    $this->db->join(
      'user_email_verifier',
      'user_email_verifier.user_id = user.user_id',
      'left'
    );

    $this->db->join(
      'user_photo',
      'user_photo.user_id = user.user_id',
      'left'
    );

    $this->db->join(
      'user_league',
      'user_league.league_id = user.league'
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
    else $this->db->where("user.{$id_type}", $id);

    $user = $this->db->get()->row();
    $save_session AND self::$user = $user;

    return $user;
  }

  /**
   * For signing up users
   *
   * @return array
   */
  public function create_user()
  {
    $this->load->helper('string');
    $this->load->database();

    $post = $this->input->post(NULL, TRUE);

    $email_verifier = random_string('alnum', 10);
    $password_hash = password_hash($post['signup-password'], PASSWORD_BCRYPT);

    $user_params = [
      'username' => $post['signup-username'],
      'password' => $password_hash,
      'email' => $post['signup-email'],
      'firstname' => $post['signup-firstname'],
      'lastname' => $post['signup-lastname'],
    ];

    $this->db->trans_start();

    $this->db->insert('user', $user_params);

    $email_verifier_params = [
      'user_id' => $this->db->insert_id(),
      'verifier' => $email_verifier
    ];

    $this->db->insert('email_verifier', $email_verifier_params);

    $this->db->trans_complete();

    if (!$this->db->trans_status()) return FALSE;

    self::$unverified_username = $post['signup-username'];
    $user_params['email_verified'] = '0';
    $user_params['email_verifier'] = $email_verifier;

    return $user_params;
  }

  /**
   * Set user email as verified
   *
	 * @param int $user_id
   * @return bool
   */
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

  /**
   * Save user in session and update last login time in db
   *
	 * @param object $user
   * @return void
   */
  public function login($user)
  {
    self::$user = $user;

    $this->load->database();
    $this->db->set('last_login_time', 'NOW()', FALSE);
    $this->db->where('user_id', $user->user_id);
    $this->db->update('user');
  }
}
