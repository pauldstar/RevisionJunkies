<?php namespace App\Models\Facades;

use ReflectionException;

class QuestionFacadeTest extends \CIUnitTestCase
{
  /**
   * @depends testLoadDbQuestionsLvl1
   * @param array $dbQuestions
   */
  public function testGetAnswerScoreLvl1(array $dbQuestions)
  {
    $this->answerScore($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl2
   * @param array $dbQuestions
   */
  public function testGetAnswerScoreLvl2(array $dbQuestions)
  {
    $this->answerScore($dbQuestions);
  }

  private function answerScore($dbQuestions)
  {
    QuestionFacade::reset();
    $gameQuestions = QuestionFacade::formatQuestions($dbQuestions);
    $sessionQuestion = QuestionFacade::currentSessionQuestion();
    $this->assertEquals(0, QuestionFacade::getAnswerScore($sessionQuestion, ''));

    foreach ($gameQuestions as $qtn)
    {
      switch ($qtn['type'])
      {
        case 'boolean':
        {
          $firstHash = QuestionFacade::currentAnswerHash();

          foreach (['False', 'True'] as $i => $option)
          {
            $answerHash = QuestionFacade
              ::getUserAnswerHash($sessionQuestion, $firstHash, $i);
            $answerScore = QuestionFacade
              ::getAnswerScore($sessionQuestion, $answerHash);

            if ($option === $sessionQuestion->correct_answer)
              $this->assertEquals($sessionQuestion->score, $answerScore);
            else $this->assertEquals(0, $answerScore);
          }

          break;
        }

        case 'multiple':
        {
          foreach ($qtn['options'] as $i => $option)
          {
            $answerScore = QuestionFacade::getAnswerScore($sessionQuestion, $i);
            $answerHash = QuestionFacade::getUserAnswerHash($sessionQuestion, $i);

            if ($option === $sessionQuestion->correct_answer)
              $this->assertEquals($sessionQuestion->score, $answerScore);
            else $this->assertEquals(0, $answerScore);
          }

          break;
        }
      }

      $sessionQuestion = QuestionFacade::currentSessionQuestion();
    }
  }

  public function testFormatQuestionsEmpty()
  {
    $test = QuestionFacade::formatQuestions([]);
    $this->assertIsArray($test);
    $this->assertEmpty($test);
  }

  /**
   * @depends testLoadDbQuestionsLvl1
   * @param array $dbQuestions
   * @return array
   */
  public function testFormatQuestionsLvl1(array $dbQuestions)
  {
    return $this->formatQuestions($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl2
   * @param array $dbQuestions
   * @return array
   */
  public function testFormatQuestionsLvl2(array $dbQuestions)
  {
    return $this->formatQuestions($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl3
   * @param array $dbQuestions
   * @return array
   */
  public function testFormatQuestionsLvl3(array $dbQuestions)
  {
    return $this->formatQuestions($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl4
   * @param array $dbQuestions
   * @return array
   */
  public function testFormatQuestionsLvl4(array $dbQuestions)
  {
    return $this->formatQuestions($dbQuestions);
  }

  /**
   * @return array
   */
  public function testFormatQuestionsLvl134()
  {
    $dbQuestions = QuestionFacade::loadDbQuestions(134);
    return $this->formatQuestions($dbQuestions);
  }

  /**
   * @param $dbQuestions
   * @return array
   */
  private function formatQuestions($dbQuestions)
  {
    QuestionFacade::reset();
    $questions = QuestionFacade::formatQuestions($dbQuestions);

    foreach ($questions as $qtn)
    {
      $this->assertGreaterThanOrEqual('1', $qtn['score']);
      $this->assertLessThanOrEqual(33 * $qtn['level'], $qtn['score']);

      if ($qtn['type'] === 'multiple')
      {
        $this->assertCount(4, $qtn['options']);
      }

      $sessionQuestion = QuestionFacade::currentSessionQuestion();
      $this->assertEquals($qtn['level'], $sessionQuestion->level);
      $this->assertEquals($qtn['score'], $sessionQuestion->score);
      $this->assertEquals($qtn['ah'], $sessionQuestion->answer_hash);
    }

    return $questions;
  }

  public function testLoadDbQuestionsLvl1()
  {
    $questions = QuestionFacade::loadDbQuestions(1);
    $this->assertCount(4, $questions);

    foreach ($questions as $qtn)
    {
      $this->assertEquals('easy', $qtn->difficulty);
      $this->assertEquals('boolean', $qtn->type);
      $this->assertEquals('1', $qtn->level);
    }

    return $questions;
  }

  public function testLoadDbQuestionsLvl2()
  {
    $questions = QuestionFacade::loadDbQuestions(2);
    $this->assertCount(7, $questions);

    foreach ($questions as $qtn)
    {
      $this->assertEquals('easy', $qtn->difficulty);
      $this->assertEquals('2', $qtn->level);
    }

    return $questions;
  }

  public function testLoadDbQuestionsLvl3()
  {
    $questions = QuestionFacade::loadDbQuestions(3);
    $this->assertCount(7, $questions);

    foreach ($questions as $qtn)
    {
      $this->assertNotEquals('hard', $qtn->difficulty);
      $this->assertEquals('3', $qtn->level);
    }

    return $questions;
  }

  public function testLoadDbQuestionsLvl4()
  {
    $questions = QuestionFacade::loadDbQuestions(4);
    $this->assertCount(10, $questions);

    foreach ($questions as $qtn) $this->assertEquals('4', $qtn->level);

    return $questions;
  }

  /**
   * @afterclass
   */
  public static function resetSessionQuestions()
  {
    QuestionFacade::reset();
  }
}

