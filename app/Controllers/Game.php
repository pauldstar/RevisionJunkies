<?php namespace App\Controllers;

use App\Facades\QuestionFacade;
use App\Facades\GameFacade;
use App\Facades\UserFacade;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Response;
use ReflectionException;

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
  public function start_game(): Response
  {
    GameFacade::startTime(true);
    return $this->respond(true);
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
    $dbQuestions = QuestionFacade::loadQuestions($level);
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
    $question = QuestionFacade::nextSessionQuestion();
    $secret = QuestionFacade::nextHashSecret();
    $hash = QuestionFacade::userAnswerHash($question, $secret, $answerCode);
    $score = QuestionFacade::answerScore($question, $hash);
    GameFacade::score($score);

    return $this->respond($score);
  }

  //--------------------------------------------------------------------

  /**
   * End game and update user details
   *
   * @param string|int $score
   * @param string|int $timeDelta - game time length
   * @return Response
   * @throws ReflectionException
   */
  public function end_game($score, $timeDelta): Response
  {
    $elapsed = time() - GameFacade::startTime();
    $output = "user<br />{$timeDelta}<br />session<br />$elapsed";
    UserFacade::updateStats($score);
    return $this->respond($output);
  }
}
