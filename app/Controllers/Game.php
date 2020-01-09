<?php namespace App\Controllers;

use App\Models\Question as QuestionModel;
use App\Models\Game as GameModel;
use CodeIgniter\API\ResponseTrait;

class Game extends BaseController
{
	use ResponseTrait;

	private $gameModel;
	private $questionModel;

	public function __construct()
	{
		$this->gameModel = new GameModel();
		$this->questionModel = new QuestionModel();
	}

	//--------------------------------------------------------------------

	/**
   * Load/prepare/reload/reset game
	 * Called before start_game()
	 *
   * @echo array
   */
	public function load_game()
	{
		$this->gameModel->score(0);
		$this->gameModel->level(1);
		$this->questionModel->reset();
		$this->get_questions();
	}

	//--------------------------------------------------------------------

	/**
   * Start game & timer
   */
	public function start_game()
	{
		$this->gameModel->startTime(time());
	}

	//--------------------------------------------------------------------

	/**
   * End game and update user details
	 *
	 * @param string|int $score
	 * @param string|int $timeDelta - game time length
   * @echo string
   */
	public function end_game($score, $timeDelta)
	{
		echo 'user<br />';
		echo $timeDelta.'<br />';
		echo 'session<br />';
		echo time() - $this->gameModel->startTime();
	}

	//--------------------------------------------------------------------

	/**
   * Get game questions (displayed to user) and increment game level
	 *
   * @return ResponseTrait - questions for user to answer
   */
	public function get_questions()
	{
		$level = $this->gameModel->level();
		$dbQuestions = $this->questionModel->loadDbQuestions($level);
		$gameQuestions = $this->questionModel->formatQuestions($level, $dbQuestions);
    $this->gameModel->level(TRUE);

    return $this->respond($gameQuestions);
	}

	//--------------------------------------------------------------------

	/**
	 * Get score for user answer and increment game score
	 *
	 * @param string|int $questionId
	 * @param string|int $answerCode - null value suggests question timed out
	 * @return ResponseTrait
	 */
	public function answer_score($questionId, $answerCode = NULL)
	{
		$score = $this->questionModel->getAnswerScore($questionId, $answerCode);
		$this->gameModel->score($score);

		return $this->respond($score);
	}
}
