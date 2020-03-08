<?php namespace App\Models;

use App\Entities\UserEntity;
use ReflectionException;

class UserModel extends BaseModel
{
  protected $returnType = UserEntity::class;
  protected $useTimestamps = true;
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

  /**
   * Current user
   * @var  object
   */
  private $user;

  /**
   * Username of currently unverified user
   * @var  string
   */
  private $unverifiedUsername;

  //--------------------------------------------------------------------

  public function isLoggedIn(): bool
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
    if ($id === '') return $this->user;

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

    $builder = $this->select($selections);

    $builder->join(
      'email_verifier', 'email_verifier.user_id = user.id', 'left'
    );

    $builder->join('user_photo', 'user_photo.user_id = user.id', 'left');
    $builder->join('league', 'league.id = user.league_id');

    if (is_array($idType))
    {
      $idType = array_map(function ($type) use ($id)
      {
        return "{$type} = '{$id}'";
      }, $idType);

      $clause = implode(' OR ', $idType);
      $builder->where($clause);
    } else $builder->where("user.{$idType}", $id);

    return $builder->first();
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

    $user = new UserEntity($input);
    $verifierModel = new EmailVerifierModel();

    $this->transStart();

    $this->save($user);
    $user->id = $this->insertID();

    $emailVerifier = [
      'user_id' => $user->id,
      'verifier' => random_string('alnum', 10)
    ];

    $verifierModel->insert($emailVerifier);

    $this->transComplete();

    if (! $this->transStatus()) return false;

    $this->unverifiedUsername = $user->username;
    $user->email_verifier = $emailVerifier['verifier'];

    return $user;
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
    return (new EmailVerifierModel())->delete($userId) ? true : false;
  }

  //--------------------------------------------------------------------

  /**
   * Set & get the session username for an unverified email
   *
   * @param string $username
   * @return void|string
   */
  public function unverifiedUsername($username = null)
  {
    if ($username) $this->unverifiedUsername = $username;
    elseif ($username === false) $this->unverifiedUsername = null;

    return $this->unverifiedUsername;
  }

  //--------------------------------------------------------------------

  /**
   * @param int $score
   * @return void|null
   * @throws ReflectionException
   */
  public function updateStats($score = 0)
  {
    if (! $this->user) return;

    $this->user->hi_score = $score;
    $this->user->total_qp = $score;

    $this->save($this->user);
  }

  //--------------------------------------------------------------------

  /**
   * Save user in session and update last login time in db
   *
   * @param object $user
   */
  public function login($user)
  {
    $this->user = $user;

    $this->set('updated_at', 'NOW()')
      ->where('id', $user->id)
      ->update();
  }

  //--------------------------------------------------------------------

  /**
   * End member session
   */
  public function logout()
  {
    // TODO: remove ENVIRONMENT check once CI4 session testing is possible
    if (ENVIRONMENT === 'testing')
    {
      $this->user = null;
      $this->unverifiedUsername = null;
    }
    else session_destroy();
  }
}

