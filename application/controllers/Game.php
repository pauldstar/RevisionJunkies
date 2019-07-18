<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends QP_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('state');
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
			$qtn->id = "{$game_level}{$index}";
			$qtn->score = $scores[$index];
			$qtn->answer_hash =
				$this->questions->get_next_answer_chain_hash($qtn->correct_answer);

			$usr_qtn = [];
			$usr_qtn['id'] = $qtn->id;
			$usr_qtn['level'] = $game_level;
			$usr_qtn['question'] = $qtn->question;
			$usr_qtn['type'] = $qtn->type;
			$usr_qtn['score'] = $qtn->score;
			$usr_qtn['correct'] = str_replace(['"', "'", " "], '', $qtn->correct_answer);
			$usr_qtn['ah'] = $qtn->answer_hash;

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

			$session_questions[] = $qtn;
			$user_questions[] = $usr_qtn;
		}

		$this->questions->set_session_questions($session_questions);
		$this->state->level(TRUE);

    echo json_encode($user_questions);
	}

	public function test()
	{
		$this->load->database();

		$insert_query = NULL;

		while (true)
		{
			$data = json_decode(
				file_get_contents('https://opentdb.com/api.php?amount=50')
			);

      foreach($data->results as $qtn)
			{
				$insert_query = [
					'question_hash' => md5($qtn->question),
					'question' => $qtn->question,
					'category' => $qtn->category,
					'type' => $qtn->type,
					'difficulty' => $qtn->difficulty,
					'correct_answer' => $qtn->correct_answer,
					'incorrect_answers' => implode(',', $qtn->incorrect_answers)
				];

				$insert_query = $this->db->insert_string('questions', $insert_query);
				$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
				$this->db->query($insert_query);
			}

			// sleep(2);
		}
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
				$answer_hash = self::get_user_answer_hash($question, $answer_code);
				$is_correct = $answer_hash === $question->answer_hash;
				if ($is_correct) $score = $question->score;
			}

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
