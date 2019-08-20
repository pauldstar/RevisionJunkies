<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends CI_Controller
{
	private static $start_time;
	private static $score;
	private static $level;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');

		self::$start_time = &$_SESSION['start_time'];
		self::$level = &$_SESSION['level'];
		self::$score = &$_SESSION['score'];

		self::$start_time OR self::$start_time = time();
		self::$level OR self::$level = 1;
		self::$score OR self::$score = 0;

		$this->load->model('questions');
	}

	public function load_game()
	{
		self::$score = 0;
		self::$level = 1;

		$this->questions->reset();
		self::get_questions();
	}

	public function start_game()
	{
		self::$start_time = time();
	}

	public function end_game($score, $time_delta)
	{
		echo 'user<br />';
		echo $time_delta.'<br />';
		echo 'session<br />';
		echo time() - self::$start_time;
	}

	public function get_questions()
	{
		$level = self::_level();
		$questions = $this->questions->load_questions($level);
		$scores = self::_get_calc_scores(count($questions));
		$session_questions = [];
		$user_questions = [];

		$game_level = self::_level();

		foreach($questions as $index => $qtn)
		{
			$qtn->id = "{$game_level}{$index}";
			$qtn->score = $scores[$index];

			$usr_qtn = [];
			$usr_qtn['id'] = $qtn->id;
			$usr_qtn['level'] = $game_level;
			$usr_qtn['question'] = $qtn->question;
			$usr_qtn['type'] = $qtn->type;
			$usr_qtn['score'] = $qtn->score;
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

			// TODO: Game.php controller: remove $usr_qtn['hashes']
			$usr_qtn['hashes'] = $this->questions->get_options_test_hashes($usr_qtn);

			$qtn->answer_hash =
				$this->questions->get_next_answer_chain_hash($qtn->correct_answer);
			$usr_qtn['ah'] = $qtn->answer_hash;

			$session_questions[] = $qtn;
			$user_questions[] = $usr_qtn;
		}

		$this->questions->set_session_questions($session_questions);
		self::_level(TRUE);

    echo json_encode($user_questions);
	}

	public function score_user_answer($question_id, $answer_code = NULL)
	{
		$question = $this->questions->get_session_question($question_id);

		if (isset($question))
		{
			$this->questions->unset_session_question($question_id);

			$score = 0;

			if (isset($answer_code))
			{
				$answer_hash = self::_get_user_answer_hash($question, $answer_code);
				$is_correct = $answer_hash === $question->answer_hash;
				if ($is_correct) $score = $question->score;
			}

			self::_score($score);

			echo $score;
		}
	}

	private function _score($score = NULL)
	{
		if ($score) self::$score += $score;
		else return self::$score;
	}

	private function _level($increment = FALSE)
	{
		if ($increment) self::$level++;
		else return self::$level;
	}

	private function _get_user_answer_hash($question, $answer_code)
	{
		$new_hash = '';

		switch($question->type)
		{
			case 'multiple':
				$new_hash = md5($question->options_trim[$answer_code]);
				break;

			case 'boolean':
				switch ($answer_code)
				{
					case 0: $new_hash = md5('False'); break;
					case 1: $new_hash = md5('True'); break;
				}
				break;
		}

		$old_hash =
			$this->questions->get_prev_answer_chain_hash($question->answer_hash);

		return md5($old_hash.$new_hash);
	}

	private function _get_calc_scores($amount)
	{
		$game_level = self::_level();

		$max = 33 * $game_level;
		$min = 1;
		$scores = [];

		while ($amount-- > 0)
		{
			$rand_float = mt_rand() / mt_getrandmax();
			$scores[] = $rand_float * ($max - $min) + $min;
		}

		return $scores;
	}
}
