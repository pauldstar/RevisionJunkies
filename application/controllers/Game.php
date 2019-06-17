<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends QP_Controller
{
	private static $game_level;
	private static $questions;
	private static $score;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		self::$questions = &$_SESSION['questions'];
		self::$score = &$_SESSION['score'];
	}

	public function get_questions($game_level)
	{
		self::$game_level = $game_level;
		$game_level == 1 AND self::clear_old_questions();

		$api_urls = self::get_api_urls();
		$svr_questions = [];
		$usr_questions = [];
		$id = 0;

		foreach ($api_urls as $url)
		{
			$data = json_decode(file_get_contents($url));

			foreach($data->results as $qtn)
			{
				$svr_questions[] = $qtn;

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
				}

				$usr_questions[] = $usr_qtn;
				$id++;
			}
		}

		self::$questions[$game_level] = $svr_questions;

		shuffle($usr_questions);
    echo json_encode($usr_questions);
	}

	private function get_api_urls()
	{
		$urls = [];

		switch (self::$game_level)
		{
			default: $urls[] = 'https://opentdb.com/api.php?amount=10&difficulty=hard';
			case 2: $urls[] = 'https://opentdb.com/api.php?amount=10&difficulty=medium';
			case 1: $urls[] = 'https://opentdb.com/api.php?amount=10&difficulty=easy';
		}

		return $urls;
	}

	private function clear_old_questions()
	{
		self::$questions = [];

		switch (self::$game_level)
		{
			case 1: self::$questions = []; break;
			case 2: break;
			default: self::$questions[$game_level - 3] = NULL;
		}
	}
}
