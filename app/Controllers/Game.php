<?php namespace App\Controllers;

use App\Models\Facades\QuestionFacade;
use App\Models\Facades\GameFacade;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Response;

class Game extends BaseController
{
  use ResponseTrait;

  /**
   * Called before start_game()
   * Load/prepare/reload/reset game
   *
   * @echo array
   */
  public function load_game(): Response
  {
    GameFacade::reset();
    QuestionFacade::reset();

    return $this->get_questions();
  }

  //--------------------------------------------------------------------

  /**
   * Start game & timer
   */
  public function start_game(): void
  {
    GameFacade::startTime(true);
  }

  //--------------------------------------------------------------------

  /**
   * End game and update user details
   *
   * @param string|int $score
   * @param string|int $timeDelta - game time length
   * @return Response
   */
  public function end_game($score, $timeDelta): Response
  {
    $elapsed = time() - GameFacade::startTime();
    $output = "user<br />{$timeDelta}<br />session<br />$elapsed";

    return $this->respond($output);
  }

  //--------------------------------------------------------------------

  /**
   * Get game questions (displayed to user) and increment game level
   *
   * @return Response
   */
  public function get_questions(): Response
  {
    $level = GameFacade::level();
    $dbQuestions = QuestionFacade::loadDbQuestions($level);
    $gameQuestions = QuestionFacade::formatQuestions($dbQuestions);
    GameFacade::level(true);

    return $this->response->setJSON($gameQuestions);
  }

  //--------------------------------------------------------------------

  /**
   * Get score for user answer and increment game score
   *
   * @param string|int $answerCode - null value suggests question timed out
   * @return Response
   */
  public function answer_score($answerCode = null): Response
  {
    $question = QuestionFacade::currentSessionQuestion();
    $firstHash = QuestionFacade::currentAnswerHash();
    $answerHash = QuestionFacade
      ::getUserAnswerHash($question, $firstHash, $answerCode);
    $score = QuestionFacade::getAnswerScore($question, $answerHash);
    GameFacade::score($score);

    return $this->respond($score);
  }
}
