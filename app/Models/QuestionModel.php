<?php namespace App\Models;

class QuestionModel extends BaseModel
{
	protected $useTimestamps = true;

	protected $allowedFields = [
		'user_id',
		'question',
		'category',
		'type',
		'difficulty',
		'correct_answer',
		'incorrect_answers',
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

	public function __construct()
	{
		parent::__construct();

		$this->questions = & $_SESSION['questions'];
		$this->answerHashChain = & $_SESSION['answerHashChain'];

		$this->questions OR $this->questions = [];
		$this->answerHashChain OR $this->answerHashChain = [''];
	}

	//--------------------------------------------------------------------

	/**
	 * Prepare questions for user game session
	 *
	 * @param array $dbQuestions
	 * @return array
	 */
	public function formatQuestions($dbQuestions)
	{
		$gameQuestions = [];

		foreach ($dbQuestions as $sessionQuestion)
		{
			$gameQuestion['level'] = $sessionQuestion->level;
			$gameQuestion['question'] = $sessionQuestion->question;
			$gameQuestion['type'] = $sessionQuestion->type;
      $gameQuestion['score'] = $this->getRandomScore($sessionQuestion->level);
      $gameQuestion['ah'] = $this->
        getNextAnswerHash($sessionQuestion->correct_answer);

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
			$this->questions[] = $sessionQuestion;
		}

		return $gameQuestions;
	}

	//--------------------------------------------------------------------

	/**
	 * Load random questions from database for game level
	 *
	 * @param	int $level current game level
	 * @return array
	 */
	public function loadDbQuestions($level)
	{
	  $this->builder->select(
			"question, type, correct_answer, difficulty,".
			"incorrect_answers, {$level} as level"
		);

		switch ($level)
		{
			case 1:
				$this->builder->where('difficulty', 'easy');
				$this->builder->where('type', 'boolean');
				$limit = 4;
				break;
			case 2:
				$this->builder->where('difficulty', 'easy');
				$limit = 7;
				break;
			case 3:
				$this->builder->where('difficulty', 'medium');
				$this->builder->orWhere('difficulty', 'easy');
				$limit = 7;
				break;
			default:
				$limit = 10;
				break;
		}

		$this->builder->orderBy('', 'random');
		$query = $this->builder->get($limit);

		return $query->getResult();
	}

	//--------------------------------------------------------------------

	/**
	 * @param $questionId
	 * @param bool $unset
	 * @return null|object
	 */
	public function getSessionQuestion($questionId, $unset = TRUE)
	{
		if (isset($this->questions[$questionId]))
		{
			$question = $this->questions[$questionId];
			// dont refactor to short-hand conditional statement
			if ($unset) unset($this->questions[$questionId]);
			return $question;
		}

		return null;
	}

	//--------------------------------------------------------------------

	public function reset()
	{
		$this->questions = [];
		$this->answerHashChain = [''];
	}

	//--------------------------------------------------------------------

	/**
	 * Get score for user answer on current question
	 *
	 * @param string|int $questionId
	 * @param string|int $answerCode - null value suggests question timed out
	 * @return int
	 */
	public function getAnswerScore($questionId, $answerCode = null)
	{
		$answerCode AND $question = array_shift($this->questions);

		if (isset($question))
		{
			$answerHash = $this->calcUserAnswerHash($question, $answerCode);
			return $answerHash === $question->answer_hash ? $question->score : 0;
		}

		return 0;
	}

	//--------------------------------------------------------------------

	/**
	 * get hash for user's answer
	 *
	 * @param object $question
	 * @param string|int $answerCode
	 * @return string
	 */
	private function calcUserAnswerHash($question, $answerCode)
	{
		$testHash = '';

		switch ($question->type)
		{
			case 'multiple':
				$testHash = md5($question->options[$answerCode]);
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

		$firstHash = $this->getFirstAnswerHash();

		return md5($firstHash . $testHash);
	}

	//--------------------------------------------------------------------

	/**
	 * Get a random score to assign to a question if correct
	 *
	 * @param int $level - the current game level
	 * @return int
	 */
	private function getRandomScore($level)
	{
		$max = 33 * $level;
		$min = 1;
		$randFloat = mt_rand() / mt_getrandmax();

		return $randFloat * ($max - $min) + $min;
	}

	//--------------------------------------------------------------------

	/**
	 * Get and remove the first hash on the answer hash chain
	 *
	 * @return string
	 */
	private function getFirstAnswerHash()
	{
		return array_shift($this->answerHashChain);
	}

	//--------------------------------------------------------------------

	/**
	 * Use last hash on answer hash chain to create new hash
	 * then add hash to chain
	 *
	 * @param $answer
	 * @return string
	 */
	private function getNextAnswerHash($answer)
	{
		$lastHash = end($this->answerHashChain);
		$nextHash = md5($lastHash . md5($answer));
		$this->answerHashChain[] = $nextHash;
		return $nextHash;
	}
}
