<?php namespace App\Models\Facades;

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
   * @param array $dbQuestions
   * @return array
   */
  public static function formatQuestions($dbQuestions)
  {
    $gameQuestions = [];

    foreach ($dbQuestions as $sessionQuestion)
    {
      $gameQuestion['level'] = $sessionQuestion->level;
      $gameQuestion['question'] = $sessionQuestion->question;
      $gameQuestion['type'] = $sessionQuestion->type;
      $gameQuestion['score'] = self::getRandomScore($sessionQuestion->level);
      $gameQuestion['ah'] = self
        ::getNextAnswerHash($sessionQuestion->correct_answer);

      if ($sessionQuestion->type === 'multiple')
      {
        $gameQuestion['options'] = array_merge(
          [$sessionQuestion->correct_answer],
          json_decode($sessionQuestion->incorrect_answers)
        );

        shuffle($gameQuestion['options']);
      }

      $gameQuestions[] = $gameQuestion;

      $sessionQuestion->score = $gameQuestion['score'];
      $sessionQuestion->answer_hash = $gameQuestion['ah'];
      self::$questions[] = $sessionQuestion;
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
  public static function loadDbQuestions($level)
  {
    $builder = self::instance()->select(
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
        break;
    }

    $builder->orderBy('', 'random');
    $query = $builder->get($limit);

    return $query->getResult();
  }

  //--------------------------------------------------------------------

  /**
   * @return null|object
   */
  public static function currentSessionQuestion()
  {
    return array_shift(self::$questions);
  }

  //--------------------------------------------------------------------

  /**
   * Get and remove the first hash on the answer hash chain
   *
   * @return string
   */
  public static function currentAnswerHash()
  {
    return array_shift(self::$answerHashChain);
  }

  //--------------------------------------------------------------------

  /**
   * Get score for user answer on given question
   *
   * @param object $sessionQuestion
   * @param string $answerHash
   * @return float
   */
  public static function getAnswerScore(object $sessionQuestion,
                                        string $answerHash): float
  {
    if (empty($answerHash)) return 0;

    return $answerHash === $sessionQuestion->answer_hash
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
   * @param object $sessionQuestion
   * @param string|int $answerCode
   * @param string $firstAnswerHash
   * @return string
   */
  public static function getUserAnswerHash(object $sessionQuestion,
                                           string $firstAnswerHash,
                                           $answerCode = null)
  {
    if ($answerCode === null) return '';

    $testHash = '';

    switch ($sessionQuestion->type)
    {
      case 'multiple':
        $testHash = md5($sessionQuestion->options[$answerCode]);
        break;

      case 'boolean':
        switch ($answerCode)
        {
          case 0:
            $testHash = md5('False');
            break;
          case 1:
            $testHash = md5('True');
            break;
        }
        break;
    }

    return md5($firstAnswerHash . $testHash);
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
    $max = 33 * $level;
    $min = 1;
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