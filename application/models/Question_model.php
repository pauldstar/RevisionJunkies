<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Question_model extends CI_Model
{
	private static $questions;
	private static $answer_hash_chain;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		self::$questions = &$_SESSION['questions'];
		self::$answer_hash_chain = &$_SESSION['answer_hash_chain'];

		self::$questions OR self::$questions = [];
		self::$answer_hash_chain OR self::$answer_hash_chain = [''];
	}

	public function load_questions($level)
	{
		$this->load->database();

		$this->db->
			select('question, type, correct_answer, difficulty, incorrect_answers');

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

	public function set_session_questions($questions)
	{
		foreach ($questions as $qtn) self::$questions[$qtn->id] = $qtn;
	}

	public function get_session_question($question_id, $unset = TRUE)
	{
		if (isset(self::$questions[$question_id]))
		{
			$question = self::$questions[$question_id];
			// dont refactor to short-hand conditional statement
			if ($unset) unset(self::$questions[$question_id]);
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
		self::$questions = [];
		self::$answer_hash_chain = [''];
	}

	public function get_calc_scores($level, $amount)
	{
		$max = 33 * $level;
		$min = 1;
		$scores = [];

		while ($amount-- > 0)
		{
			$rand_float = mt_rand() / mt_getrandmax();
			$scores[] = $rand_float * ($max - $min) + $min;
		}

		return $scores;
	}

	/**
	 * Get score for user answer
	 *
	 * @param string|int $question_id
	 * @param string|int $answer_code - null value suggests question timed out
	 * @return int
	 */
	public function get_answer_score($question_id, $answer_code = NULL)
	{
		$answer_code AND $question = self::get_session_question($question_id);

		if (isset($question))
		{
			$answer_hash = self::_calc_user_answer_hash($question, $answer_code);
			return $answer_hash === $question->answer_hash ? $question->score : 0;
		}

		return 0;
	}

	public function get_user_questions($level)
	{
		$questions = self::load_questions($level);
		$scores = self::get_calc_scores($level, count($questions));
		$session_questions = [];
		$user_questions = [];

		foreach ($questions as $index => $qtn)
		{
			$qtn->id = "{$level}{$index}";
			$qtn->score = $scores[$index];

			$usr_qtn = [];
			$usr_qtn['id'] = $qtn->id;
			$usr_qtn['level'] = $level;
			$usr_qtn['question'] = $qtn->question;
			$usr_qtn['type'] = $qtn->type;
			$usr_qtn['score'] = $qtn->score;

			// TODO: Game.php controller: remove $usr_qtn['correct']
			$usr_qtn['correct'] = str_replace(['"', "'", " "], '', $qtn->correct_answer);

			if ($qtn->type === 'multiple')
			{
				$usr_qtn['options'][] = $qtn->correct_answer;

				$incorrect_answers = explode(',', $qtn->incorrect_answers);
				foreach ($incorrect_answers as $ans) $usr_qtn['options'][] = $ans;

				shuffle($usr_qtn['options']);

				foreach ($usr_qtn['options'] as $opt)
					$usr_qtn['optionsTrim'][] = str_replace(['"', "'", " "], '', $opt);
				$qtn->options_trim = $usr_qtn['optionsTrim'];
			}

			// TODO: Questions.php model: remove $usr_qtn['hashes']
			$usr_qtn['hashes'] = self::get_options_test_hashes($usr_qtn);

			$qtn->answer_hash = self::_get_next_answer_chain_hash($qtn->correct_answer);
			$usr_qtn['ah'] = $qtn->answer_hash;

			$session_questions[] = $qtn;
			$user_questions[] = $usr_qtn;
		}

		self::set_session_questions($session_questions);
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
				$test_hash = md5($question->options_trim[$answer_code]);
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

		$first_hash = array_shift(self::$answer_hash_chain);

		return md5($first_hash . $test_hash);
	}

	/**
	 * @param $answer
	 * @return string new_hash
	 */
	private function _get_next_answer_chain_hash($answer)
	{
		$answer = str_replace(['"', "'", " "], '', $answer);
		$last_hash = end(self::$answer_hash_chain);
		$next_hash = md5($last_hash . md5($answer));
		self::$answer_hash_chain[] = $next_hash;
		return $next_hash;
	}
}
