<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('question', '_question');
		$this->load->model('game_model', '_game');
	}

	/**
   * Load/prepare/reload/reset game
	 * Called before start_game()
	 *
   * @return void
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
	 *
   * @return void
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
   * @return void
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
   * @return void
   */
	public function get_questions()
	{
		$level = $this->_game->level();
		$user_questions = $this->_game->get_user_questions($level);
    $this->_game->level(TRUE);
		echo json_encode($user_questions);
	}

	/**
   * Get score for user answer and increment game score
	 *
	 * @param string|int $question_id
	 * @param string|int $answer_code - null value suggests question timed out
   * @return void
   */
	public function answer_score($question_id, $answer_code = NULL)
	{
		$score = $this->_question->get_answer_score($question_id, $answer_code);
		$this->_game->score($score);
		echo $score;
	}
}
