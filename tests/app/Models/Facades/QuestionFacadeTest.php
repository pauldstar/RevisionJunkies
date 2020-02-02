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

  /**
   * @depends testLoadDbQuestionsLvl3
   * @param array $dbQuestions
   */
  public function testGetAnswerScoreLvl3(array $dbQuestions)
  {
    $this->answerScore($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl4
   * @param array $dbQuestions
   */
  public function testGetAnswerScoreLvl4(array $dbQuestions)
  {
    $this->answerScore($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl134
   * @param array $dbQuestions
   */
  public function testGetAnswerScoreLvl134(array $dbQuestions)
  {
    $this->answerScore($dbQuestions);
  }

  /**
   * @param $dbQuestions
   */
  private function answerScore($dbQuestions)
  {
    QuestionFacade::reset();
    $gameQuestions = QuestionFacade::formatQuestions($dbQuestions);
    $sessionQuestion = QuestionFacade::nextSessionQuestion();
    $this->assertEquals(0, QuestionFacade::answerScore($sessionQuestion, ''));

    foreach ($gameQuestions as $qtn)
    {
      $options = $qtn['type'] === 'multiple'
        ? $qtn['options'] : ['False', 'True'];

      $hashSecret = QuestionFacade::nextHashSecret();

      foreach ($options as $i => $option)
      {
        $answerHash = QuestionFacade
          ::userAnswerHash($sessionQuestion, $hashSecret, $i);
        $answerScore = QuestionFacade
          ::answerScore($sessionQuestion, $answerHash);

        if ($option === $sessionQuestion->correct_answer)
          $this->assertEquals($sessionQuestion->score, $answerScore);
        else $this->assertEquals(0, $answerScore);
      }

      $sessionQuestion = QuestionFacade::nextSessionQuestion();
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
   * @depends testLoadDbQuestionsLvl134
   * @param array $dbQuestions
   * @return array
   */
  public function testFormatQuestionsLvl134($dbQuestions)
  {
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

      $sessionQuestion = QuestionFacade::nextSessionQuestion();
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

  public function testLoadDbQuestionsLvl134()
  {
    $questions = QuestionFacade::loadDbQuestions(134);
    $this->assertCount(10, $questions);

    foreach ($questions as $qtn) $this->assertEquals('134', $qtn->level);

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