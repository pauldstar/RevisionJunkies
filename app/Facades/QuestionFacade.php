<?php namespace App\Facades;

abstract class QuestionFacade extends BaseFacade
{
  /**
   * Questions saved in session
   * @var array
   */
  private static $questions;

  /**
   * Hash chain used for answer authentication
   * @var array
   */
  private static $answerHashChain;

  //--------------------------------------------------------------------

  /**
   * Initialise class static properties for session
   */
  public static function __static()
  {
    parent::__static();

    self::$questions = &$_SESSION['questions'];
    self::$answerHashChain = &$_SESSION['answerHashChain'];

    self::$questions || self::$questions = [];
    self::$answerHashChain || self::$answerHashChain = [''];
  }

  //--------------------------------------------------------------------

  /**
   * Prepare questions for user game session
   *
   * @depends loadQuestions
   * @param array $dbQuestions
   * @return array
   */
  public static function formatQuestions($dbQuestions)
  {
    $gameQuestions = [];

    foreach ($dbQuestions as $sessionQtn)
    {
      $gameQtn['level'] = $sessionQtn->level;
      $gameQtn['question'] = $sessionQtn->question;
      $gameQtn['type'] = $sessionQtn->type;
      $gameQtn['score'] = self::getRandomScore($sessionQtn->level);
      $gameQtn['ah'] = self::getNextAnswerHash($sessionQtn->correct_answer);

      if ($sessionQtn->type === 'multiple')
      {
        $gameQtn['options'] = array_merge(
          [$sessionQtn->correct_answer],
          $sessionQtn->incorrect_answers
        );

        shuffle($gameQtn['options']);
        $sessionQtn->options = $gameQtn['options'];
      }

      $gameQuestions[] = $gameQtn;

      $sessionQtn->score = $gameQtn['score'];
      $sessionQtn->answer_hash = $gameQtn['ah'];
      self::$questions[] = $sessionQtn;
    }

    return $gameQuestions;
  }

  //--------------------------------------------------------------------

  /**
   * Load random questions from database for game level
   *
   * @param int $level current game level
   * @return array
   */
  public static function loadQuestions($level)
  {
    $builder = self::select(
      "question, type, correct_answer, difficulty," .
      "incorrect_answers, {$level} as level"
    );

    switch ($level)
    {
      case 1:
        $builder->where('difficulty', 'easy');
        $builder->where('type', 'boolean');
        $limit = 4;
        break;
      case 2:
        $builder->where('difficulty', 'easy');
        $limit = 7;
        break;
      case 3:
        $builder->where('difficulty', 'medium');
        $builder->orWhere('difficulty', 'easy');
        $limit = 7;
        break;
      default:
        $limit = 10;
    }

    $builder->orderBy('', 'random');

    return $builder->findAll($limit);
  }

  //--------------------------------------------------------------------

  /**
   * @return null|object
   */
  public static function nextSessionQuestion()
  {
    return array_shift(self::$questions);
  }

  //--------------------------------------------------------------------

  /**
   * Get and remove the first hash on the answer hash chain
   *
   * @return string
   */
  public static function nextHashSecret()
  {
    return array_shift(self::$answerHashChain);
  }

  //--------------------------------------------------------------------

  /**
   * Get score for user answer on given question
   *
   * @depends userAnswerHash
   * @param object $sessionQuestion
   * @param string $userAnswerHash
   * @return float
   */
  public static function answerScore(object $sessionQuestion,
                                     string $userAnswerHash): float
  {
    if (empty($userAnswerHash)) return 0;

    return $userAnswerHash === $sessionQuestion->answer_hash
      ? $sessionQuestion->score : 0;
  }

  //--------------------------------------------------------------------

  public static function reset()
  {
    self::$questions = [];
    self::$answerHashChain = [''];
  }

  //--------------------------------------------------------------------

  /**
   * get hash for user's answer
   *
   * @depends currentHashSecret
   * @param object $sessionQuestion
   * @param string $currentHashSecret
   * @param string|int $answerCode
   * @return string
   */
  public static function userAnswerHash(object $sessionQuestion,
                                        string $currentHashSecret,
                                        $answerCode = null)
  {
    if ($answerCode === null) return '';

    $options = $sessionQuestion->type === 'multiple'
      ? $sessionQuestion->options : ['False', 'True'];

    $hash = md5($options[$answerCode]);

    return md5($currentHashSecret . $hash);
  }

  //--------------------------------------------------------------------

  /**
   * Get a random score to assign to a question if correct
   *
   * @param int $level - the current game level
   * @return int
   */
  private static function getRandomScore($level)
  {
    $max = 330 * $level;
    $min = 10;
    $randFloat = mt_rand() / mt_getrandmax();

    return $randFloat * ($max - $min) + $min;
  }

  //--------------------------------------------------------------------

  /**
   * Use last hash on answer hash chain to create new hash
   * then add hash to chain
   *
   * @param $answer
   * @return string
   */
  private static function getNextAnswerHash($answer)
  {
    $lastHash = end(self::$answerHashChain);
    $nextHash = md5($lastHash . md5($answer));
    self::$answerHashChain[] = $nextHash;
    return $nextHash;
  }
}