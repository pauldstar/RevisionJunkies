<?php namespace App\Models\Facades;

use App\Entities\UserEntity;
use App\Models\EmailVerifierModel;
use ReflectionException;

abstract class UserFacade extends BaseFacade
{
  /**
   * Current user
   * @var  object
   */
  private static $user;

  /**
   * Username of currently unverified user
   * @var  string
   */
  private static $unverifiedUsername;

  //--------------------------------------------------------------------

  /**
   * Initialise class static properties for session
   */
  public static function __static()
  {
    parent::__static();

    self::$user = &$_SESSION['user'];
    self::$unverifiedUsername = &$_SESSION['unverifiedUsername'];
  }

  //--------------------------------------------------------------------

  public static function isLoggedIn(): bool
  {
    return self::$user ? true : false;
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
  public static function getUser($id = '', $idType = 'id')
  {
    if ($id === '') return self::$user;

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

    $builder = self::select($selections);

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
  public static function createUser(array $input)
  {
    helper('text');

    $user = new UserEntity($input);
    $verifierModel = new EmailVerifierModel();

    self::transStart();

    self::save($user);
    $user->id = self::getInsertID();

    $emailVerifier = [
      'user_id' => $user->id,
      'verifier' => random_string('alnum', 10)
    ];

    $verifierModel->insert($emailVerifier);

    self::transComplete();

    if (! self::transStatus()) return false;

    self::$unverifiedUsername = $user->username;
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
  public static function confirmEmailVerification($userId)
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
  public static function unverifiedUsername($username = null)
  {
    if ($username) self::$unverifiedUsername = $username;
    elseif ($username === false) self::$unverifiedUsername = null;

    return self::$unverifiedUsername;
  }

  //--------------------------------------------------------------------

  /**
   * @param int $score
   * @return void|null
   * @throws ReflectionException
   */
  public static function updateStats($score = 0)
  {
    if (! self::$user) return;

    self::$user->hi_score = $score;
    self::$user->total_qp = $score;

    self::save(self::$user);
  }

  //--------------------------------------------------------------------

  /**
   * Save user in session and update last login time in db
   *
   * @param object $user
   * @return void
   */
  public static function login($user)
  {
    self::$user = $user;

    self::set('updated_at', 'NOW()')
      ->where('id', $user->id)
      ->update();
  }

  //--------------------------------------------------------------------

  /**
   * End member session
   */
  public static function logout()
  {
    // TODO: remove ENVIRONMENT check once CI4 session testing is possible
    if (ENVIRONMENT === 'testing')
    {
      self::$user = null;
      self::$unverifiedUsername = null;
    }
    else session_destroy();
  }
}