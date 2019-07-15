<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends QP_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('game', 'state');
		$this->load->model('questions');
	}

	public function get_questions($game_level)
	{
		if ($game_level == 1) self::reset_game();

		$questions = $this->questions->load_questions();
		$scores = self::get_calc_scores(count($questions));
		$session_questions = [];
		$user_questions = [];

		$game_level = $this->state->level();

		foreach($questions as $index => $qtn)
		{
			$usr_qtn = [];
			$usr_qtn['id'] = "{$game_level}{$index}";
			$usr_qtn['level'] = $game_level;
			$usr_qtn['question'] = $qtn->question;
			$usr_qtn['type'] = $qtn->type;
			$usr_qtn['score'] = $scores[$index];
			$usr_qtn['correct'] = str_replace(['"', "'", " "], '', $qtn->correct_answer);
			$usr_qtn['answerHash'] =
				$this->questions->get_next_answer_chain_hash($qtn->correct_answer);

			$qtn->id = $usr_qtn['id'];
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

			$session_questions[] = $qtn;
			$user_questions[] = $usr_qtn;
		}

		$this->questions->set_session_questions($session_questions);
		$this->state->level(TRUE);

    echo json_encode($user_questions);
	}

	public function score_user_answer($answer_code, $question_id)
	{
		$question = $this->questions->get_session_question($question_id);

		if (isset($question))
		{
			$this->questions->unset_session_question($question_id);

			$answer_hash = self::get_user_answer_hash($question, $answer_code);

			$score = 0;
			$is_correct = $answer_hash === $question->answer_hash;
			if ($is_correct) $score = $question->score;
			$this->state->set_session_score($score);

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

		$old_hash =
			$this->questions->get_prev_answer_chain_hash($question->answer_hash);

		return md5($old_hash.$new_hash);
	}

	private function get_calc_scores($amount)
	{
		$game_level = $this->state->level();

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

	private function reset_game()
	{
		$this->state->reset();
		$this->questions->reset();
	}
}
