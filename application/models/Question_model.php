<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Question_model extends CI_Model
{
	private static $session_questions;
	private static $answer_hash_chain;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		self::$session_questions = &$_SESSION['questions'];
		self::$answer_hash_chain = &$_SESSION['answer_hash_chain'];

		self::$session_questions OR self::$session_questions = [];
		self::$answer_hash_chain OR self::$answer_hash_chain = [''];
	}

	/**
   * Get and save formatted questions to display in-game
	 *
   * @param	array $db_questions current game level
   * @return object
   */
	public function format_questions($level, $db_questions)
	{
		$game_questions = [];

		foreach ($db_questions as $index => $qtn)
		{
			$game_qtn['level'] = $qtn->level;
			$game_qtn['question'] = $qtn->question;
			$game_qtn['type'] = $qtn->type;

			$qtn->score = self::_get_random_score($level);
			$game_qtn['score'] = $qtn->score;

			$qtn->answer_hash = self::_get_next_answer_hash($qtn->correct_answer);
			$game_qtn['ah'] = $qtn->answer_hash;

			if ($qtn->type === 'multiple')
			{
				$game_qtn['options'] = array_merge(
					[$qtn->correct_answer], explode(',', $qtn->incorrect_answers)
				);

				shuffle($game_qtn['options']);
			}

			self::$session_questions[] = $qtn;
			$game_questions[] = $game_qtn;
		}

		return $game_questions;
	}

	/**
	 * Load random questions from database for game level
	 *
	 * @param	int $level current game level
	 * @return object
	 */
	public function load_questions($level)
	{
		$this->load->database();

		$this->db->select(
			"question, type, correct_answer, difficulty,".
			"incorrect_answers, {$level} as level"
		);

		switch ($level)
		{
			case 1:
				$this->db->where('difficulty', 'easy');
				$this->db->where('type', 'boolean');
				$limit = 4;
				break;
			case 2:
				$this->db->where('difficulty', 'easy');
				$limit = 7;
				break;
			case 3:
				$this->db->where('difficulty', 'medium');
				$this->db->or_where('difficulty', 'easy');
				$limit = 7;
				break;
			default:
				$limit = 10;
				break;
		}

		$this->db->order_by(NULL, 'random');
		$query = $this->db->get('question', $limit);

		return $query->result();
	}

	/**
   * Save game questions in session
   *
   * @param	array $questions
   * @param	mixed|array	$expected
   * @param	string $test_name
   * @param	string $notes
   * @return	string
   */
	public function set_session_questions($questions)
	{
		foreach ($questions as $qtn) self::$session_questions[$qtn->id] = $qtn;
	}

	public function get_session_question($question_id, $unset = TRUE)
	{
		if (isset(self::$session_questions[$question_id]))
		{
			$question = self::$session_questions[$question_id];
			// dont refactor to short-hand conditional statement
			if ($unset) unset(self::$session_questions[$question_id]);
      return $question;
    }

		return NULL;
	}

	// TODO: Question.php: remove get_options_test_hashes()
	public function get_options_test_hashes($qtn)
	{
		$result = [];

		$last_hash = end(self::$answer_hash_chain);

		switch ($qtn['type'])
		{
			case 'boolean':
				$result = [
					md5($last_hash . md5('False')),
					md5($last_hash . md5('True'))
				];
				break;

			case 'multiple':
				foreach ($qtn['optionsTrim'] as $opt)
					$result[] = md5($last_hash . md5($opt));
				break;
		}

		return $result;
	}

	public function reset()
	{
		self::$session_questions = [];
		self::$answer_hash_chain = [''];
	}

	/**
	 * Get score for user answer on current question
	 *
	 * @param string|int $question_id
	 * @param string|int $answer_code - null value suggests question timed out
	 * @return int
	 */
	public function get_answer_score($question_id, $answer_code = NULL)
	{
		$answer_code AND $question = array_first(self::$session_questions);

		if (isset($question))
		{
			$answer_hash = self::_calc_user_answer_hash($question, $answer_code);
			return $answer_hash === $question->answer_hash ? $question->score : 0;
		}

		return 0;
	}

	/**
	 * get hash for user's answer
	 *
	 * @param object $question
	 * @param string|int $answer_code
	 * @return string
	 */
	private function _calc_user_answer_hash($question, $answer_code)
	{
		$test_hash = '';

		switch ($question->type)
		{
			case 'multiple':
				$test_hash = md5($question->options[$answer_code]);
				break;

			case 'boolean':
				switch ($answer_code)
				{
					case 0:
						$test_hash = md5('False');
						break;
					case 1:
						$test_hash = md5('True');
						break;
				}
				break;
		}

		$first_hash = self::_get_first_answer_hash();

		return md5($first_hash . $test_hash);
	}

	/**
	* Get a random score to assign to a question if correct
	*
	* @param int $level - the current game level
	* @return int
	*/
	private function _get_random_score($level)
	{
		$max = 33 * $level;
		$min = 1;
		$rand_float = mt_rand() / mt_getrandmax();

		return $rand_float * ($max - $min) + $min;
	}


	/**
	* Get and remove the first hash on the answer hash chain
	*
	* @return string
	*/
	private function _get_first_answer_hash()
	{
		return array_shift(self::$answer_hash_chain);
	}

	/**
	 * Use last hash on asnwer hash chain to create new hash
	 * then add hash to chain
	 *
	 * @param $answer
	 * @return string new_hash
	 */
	private function _get_next_answer_hash($answer)
	{
		$last_hash = end(self::$answer_hash_chain);
		$next_hash = md5($last_hash . md5($answer));
		self::$answer_hash_chain[] = $next_hash;
		return $next_hash;
	}
}
