<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends QP_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('question', 'qtn_mod');
		$this->load->model('score', 'sc_mod');
	}

	public function get_questions($game_level)
	{
		if ($game_level == 1) self::reset_game();
		else $this->qtn_mod->clear_old_level_questions($game_level);

		$questions = $this->qtn_mod->load_questions($game_level);
		$ssn_questions = [];
		$usr_questions = [];
		$id = 0;

		foreach($questions as $qtn)
		{
			$usr_qtn = [];
			$usr_qtn['id'] = $id;
			$usr_qtn['level'] = $game_level;
			$usr_qtn['question'] = $qtn->question;
			$usr_qtn['type'] = $qtn->type;

			$usr_qtn['correct'] = str_replace(['"', "'", " "], '', $qtn->correct_answer);
			$usr_qtn['answerHash'] = $this->qtn_mod->get_next_answer_chain_hash($qtn->correct_answer);
			$usr_qtn['score'] = self::calc_score($game_level);

			$qtn->score = $usr_qtn['score'];
			$qtn->answer_hash = $usr_qtn['answerHash'];

			if ($qtn->type === 'multiple')
			{
				$usr_qtn['options'][] = $qtn->correct_answer;

				foreach ($qtn->incorrect_answers as $ans) $usr_qtn['options'][] = $ans;
				shuffle($usr_qtn['options']);
				$qtn->options = $usr_qtn['options'];

				foreach ($usr_qtn['options'] as $opt)
					$usr_qtn['optionsTrim'][] = str_replace(['"', "'", " "], '', $opt);
				$qtn->options_trim = $usr_qtn['optionsTrim'];
			}

			$ssn_questions[] = $qtn;
			$usr_questions[] = $usr_qtn;
			$id++;
		}

		$this->qtn_mod->set_session_questions($ssn_questions, $game_level);

    echo json_encode($usr_questions);
	}

	function score_user_answer($answer_code, $question_id, $game_level)
	{
		$question = $this->qtn_mod->get_session_question($question_id, $game_level);

		if (isset($question))
		{
			$score = 0;
			$this->qtn_mod->unset_session_question($question_id, $game_level);

			$answer_hash = self::get_user_answer_hash($question, $answer_code);

			$is_correct = $answer_hash === $question->answer_hash;
			if ($is_correct) $score = $question->score;
			$this->sc_mod->set_session_score($score);

			echo $score;
		}
	}

	private function get_user_answer_hash($question, $answer_code)
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

		$old_hash = $this->qtn_mod->get_prev_answer_chain_hash($question->answer_hash);

		return md5($old_hash.$new_hash);
	}

	private function calc_score($game_level)
	{
		$max = $game_level * 33;
		$min = 1;
		$rand_float = mt_rand() / mt_getrandmax();
		return $rand_float * ($max - $min) + $min;
	}

	private function reset_game()
	{
		$this->qtn_mod->reset();
		$this->sc_mod->reset();
	}
}
