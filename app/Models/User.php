<?php namespace App\Models;

use CodeIgniter\Email\Email;
use CodeIgniter\HTTP\RequestInterface;
use ReflectionException;

class User extends BaseModel
{
	/**
	 * Current logged in user
	 *
	 * @var	object
	 */
	private $user;

	/**
	 * Username of currently unverified user
	 *
	 * @var	string
	 */
	private $unverifiedUsername;

	protected $useTimestamps = true;
	protected $beforeInsert = ['hashPassword'];
	protected $beforeUpdate = ['hashPassword'];
	protected $validationRules = 'signup';

	protected $allowedFields = [
		'username',
		'password',
		'email',
		'firstname',
		'lastname',
		'hi_score',
		'total_qp',
		'league_id'
	];

	public function __construct()
	{
		parent::__construct();
		$this->user = & $_SESSION['user'];
		$this->unverifiedUsername = & $_SESSION['unverifiedUsername'];
	}

	//--------------------------------------------------------------------

	protected function hashPassword(array $data) : array
	{
		if (!isset($data['data']['password'])) return $data;

		$password = $data['data']['password'];
		$data['data']['password'] = password_hash($password, PASSWORD_BCRYPT);

		return $data;
	}

	//--------------------------------------------------------------------

  public function isLoggedIn()
  {
    return $this->user ? true : false;
  }

	//--------------------------------------------------------------------

	/**
	 * Retrieve user details from the database or session
	 *
	 * @param mixed $id - text/number
	 * @param string|array $idType - userID/username/email or an array
	 *
	 * @return object
	 */
	public function getUser($id = '', $idType = 'id')
	{
		if (empty($id)) return $this->user;

		$selections = '
      user.id,
      user.username,
      user.password,
      user.email,
      user.hi_score,
      user.total_qp,
      user_photo.file_name AS photo,
      email_verifier.verifier AS email_verifier,
      league.name AS league_name,
      league.color AS league_color
    ';

		$this->builder->select($selections);

		$this->builder->join(
			'email_verifier', 'email_verifier.user_id = user.id', 'left'
		);

		$this->builder->join('user_photo', 'user_photo.user_id = user.id', 'left');
		$this->builder->join('league', 'league.id = user.league_id');

		if (is_array($idType))
		{
			$idType = array_map(function($type) use($id)
			{
				return "{$type} = '{$id}'";
			}, $idType);

			$clause = implode(' OR ', $idType);
			$this->builder->where($clause);
		}
		else $this->builder->where("user.{$idType}", $id);

    return $this->builder->get()->getRow();
	}

	//--------------------------------------------------------------------

	/**
	 * For signing up users
	 *
	 * @param array $input
	 * @return object|bool
	 * @throws ReflectionException
	 */
	public function createUser(array $input)
	{
		helper('text');

		$email_verifier = random_string('alnum', 10);

		$user = [
			'username' => $input['username'],
			'password' => $input['username'],
			'email' => $input['email'],
			'firstname' => $input['firstname'],
			'lastname' => $input['lastname'],
		];

		$verifierModel = new EmailVerifier();

		$this->db->transStart();

		$this->save($user);

		$emailVerifier = [
			'user_id' => $this->db->insertID(),
			'verifier' => $email_verifier
		];

		$verifierModel->save($emailVerifier);

		$this->db->transComplete();

		if (!$this->db->transStatus()) return false;

		$this->unverifiedUsername = $input['username'];
		$user['email_verified'] = '0';
		$user['email_verifier'] = $email_verifier;

		return (object) $user;
	}

	//--------------------------------------------------------------------

	/**
	 * Set user email as verified
	 *
	 * @param int $userId
	 * @return bool
	 */
	public function confirmEmailVerification($userId)
	{
	  return (new EmailVerifier)->delete($userId) ? true : false;
	}

	//--------------------------------------------------------------------

	/**
	 * Set & get the session username for an unverified email
	 *
	 * @param string $username
	 * @return void|string
	 */
	public function unverifiedUsername($username = NULL)
	{
		if ($username) $this->unverifiedUsername = $username;
		else return $this->unverifiedUsername;
	}

	//--------------------------------------------------------------------

	/**
	 * Save user in session and update last login time in db
	 *
	 * @param object $user
	 * @return void
	 */
	public function login($user)
	{
		$this->user = $user;

		$this->builder->set('updated_at', 'NOW()', false);
		$this->builder->where('id', $user->id);
		$this->builder->update();
	}
}

