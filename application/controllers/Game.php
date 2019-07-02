<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends QP_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('question', 'qtn_mod');
		$this->load->model('score', 'sc_mod');
	}

	function score_user_answer($answer_code, $question_id, $game_level)
	{
		$score = 0;
		$question = $this->qtn_mod->get_session_question($question_id, $game_level);

		if (isset($question))
		{
			$answer = self::get_user_answer($question, $answer_code);
			$is_correct = $answer === $question->correct_answer;
			$this->qtn_mod->delete_session_question($question_id, $game_level);

			$max = $game_level * 33;
			$min = 1;

			$rand_float = mt_rand() / mt_getrandmax();

			if ($is_correct) $score = $rand_float * ($max - $min) + $min;

			$this->sc_mod->set_session_score($score);
		}

		echo $score;
	}

	public function get_questions($game_level)
	{
		$this->qtn_mod->clear_old_session_questions($game_level);

		$questions = $this->qtn_mod->load_questions($game_level);
		$ssn_questions = [];
		$usr_questions = [];
		$id = 0;

		foreach($questions as $qtn)
		{
			$usr_qtn = [];
			$usr_qtn['id'] = $id;
			$usr_qtn['lvl'] = $game_level;
			$usr_qtn['question'] = $qtn->question;
			$usr_qtn['type'] = $qtn->type;

			if ($qtn->type === 'multiple')
			{
				$usr_qtn['options'][] = $qtn->correct_answer;
				foreach ($qtn->incorrect_answers as $ans) $usr_qtn['options'][] = $ans;
				shuffle($usr_qtn['options']);
				$qtn->options = $usr_qtn['options'];
			}

			$ssn_questions[] = $qtn;
			$usr_questions[] = $usr_qtn;
			$id++;
		}

		$this->qtn_mod->set_session_questions($ssn_questions, $game_level);

		shuffle($usr_questions);
    echo json_encode($usr_questions);
	}

	private function get_user_answer($question, $answer_code)
	{
		switch($question->type)
		{
			case 'multiple': return $question->options[$answer_code];

			case 'boolean':
				switch ($answer_code)
				{
					case 0: return 'False';
					case 1: return 'True';
				}
		}
	}
}
