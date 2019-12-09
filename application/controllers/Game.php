<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('question_model', '_question');
		$this->load->model('game_model', '_game');
	}

	/**
   * Load/prepare/reload/reset game
	 * Called before start_game()
	 *
   * @echo array
   */
	public function load_game()
	{
		$this->_game->score(0);
		$this->_game->level(1);
		$this->_question->reset();
		self::get_questions();
	}

	/**
   * Start game & timer
   */
	public function start_game()
	{
		$this->_game->start_time(time());
	}

	/**
   * End game and update user details
	 *
	 * @param string|int $score
	 * @param string|int $time_delta - game time length
   * @echo string
   */
	public function end_game($score, $time_delta)
	{
		echo 'user<br />';
		echo $time_delta.'<br />';
		echo 'session<br />';
		echo time() - $this->_game->start_time();
	}

	/**
   * Get game questions (displayed to user) and increment game level
	 *
   * @echo json - questions for user to answer
   */
	public function get_questions()
	{
		$level = $this->_game->level();
		$db_questions = $this->_question->load_questions($level);
		$game_questions = $this->_question->format_questions($level, $db_questions);
    $this->_game->level(TRUE);
		echo json_encode($game_questions);
	}

	/**
   * Get score for user answer and increment game score
	 *
	 * @param string|int $question_id
	 * @param string|int $answer_code - null value suggests question timed out
   * @echo int
   */
	public function answer_score($question_id, $answer_code = NULL)
	{
		$score = $this->_question->get_answer_score($question_id, $answer_code);
		$this->_game->score($score);
		echo $score;
	}
}
