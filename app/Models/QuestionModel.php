<?php namespace App\Models;

use App\Entities\QuestionEntity;

class QuestionModel extends BaseModel
{
  protected $returnType = QuestionEntity::class;
  protected $useTimestamps = true;

  protected $allowedFields = [
    'user_id',
    'question',
    'category',
    'type',
    'difficulty',
    'correct_answer',
    'incorrect_answers'
  ];

  /**
   * Questions saved in session
   * @var array
   */
  private $questions;

  /**
   * Hash chain used for answer authentication
   * @var array
   */
  private $answerHashChain;

  //--------------------------------------------------------------------

  /**
   * Prepare questions for user game session
   *
   * @depends loadQuestions
   * @param array $dbQuestions
   * @return array
   */
  public function formatQuestions(array $dbQuestions): array
  {
    $gameQuestions = [];

    foreach ($dbQuestions as $sessionQtn)
    {
      $gameQtn['level'] = $sessionQtn->level;
      $gameQtn['question'] = $sessionQtn->question;
      $gameQtn['type'] = $sessionQtn->type;
      $gameQtn['score'] = $this->getRandomScore($sessionQtn->level);
      $gameQtn['ah'] = $this->getNextAnswerHash($sessionQtn->correct_answer);

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
      $this->questions[] = $sessionQtn;
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
  public function loadQuestions(int $level): array
  {
    $builder = $this->select(
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
   * @return object
   */
  public function nextSessionQuestion(): object
  {
    return array_shift($this->questions);
  }

  //--------------------------------------------------------------------

  /**
   * Get and remove the first hash on the answer hash chain
   *
   * @return string
   */
  public function nextHashSecret(): string
  {
    return array_shift($this->answerHashChain);
  }

  //--------------------------------------------------------------------

  /**
   * Get score for user answer on given question
   *
   * @depends userAnswerHash
   * @param object $sessionQuestion
   * @param string $userAnswerHash
   * @return int
   */
  public function answerScore(object $sessionQuestion,
                                     string $userAnswerHash): int
  {
    if (empty($userAnswerHash)) return 0;

    return $userAnswerHash === $sessionQuestion->answer_hash
      ? $sessionQuestion->score : 0;
  }

  //--------------------------------------------------------------------

  public function reset()
  {
    $this->questions = [];
    $this->answerHashChain = [''];
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
  public function userAnswerHash(object $sessionQuestion,
                                        string $currentHashSecret,
                                        string $answerCode = null)
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
  private function getRandomScore(int $level): int
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
  private function getNextAnswerHash(string $answer): string
  {
    $lastHash = end($this->answerHashChain);
    $nextHash = md5($lastHash . md5($answer));
    $this->answerHashChain[] = $nextHash;
    return $nextHash;
  }
}
