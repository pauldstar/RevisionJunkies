<?php namespace App\Models;

class QuestionTest extends \CIUnitTestCase
{
  public function testFormatQuestionsEmpty()
  {
    $questionModel = new QuestionModel();
    $test = $questionModel->formatQuestions([]);
    $this->assertIsArray($test);
    $this->assertEmpty($test);
  }

  /**
   * @depends testLoadDbQuestionsLvl1
   * @param array $dbQuestions
   */
  public function testFormatQuestionsLvl1(array $dbQuestions)
  {
    $this->formatQuestions($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl2
   * @param array $dbQuestions
   */
  public function testFormatQuestionsLvl2(array $dbQuestions)
  {
    $this->formatQuestions($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl3
   * @param array $dbQuestions
   */
  public function testFormatQuestionsLvl3(array $dbQuestions)
  {
    $this->formatQuestions($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl4
   * @param array $dbQuestions
   */
  public function testFormatQuestionsLvl4(array $dbQuestions)
  {
    $this->formatQuestions($dbQuestions);
  }

  /**
   * @depends testLoadDbQuestionsLvl4
   * @param array $dbQuestions
   */
  public function testFormatQuestionsLvl134(array $dbQuestions)
  {
    $questionModel = new QuestionModel();
    $dbQuestions = $questionModel->loadDbQuestions(134);
    $this->formatQuestions($dbQuestions);
  }

  private function formatQuestions($dbQuestions)
  {
    $questionModel = new QuestionModel();
    $questions = $questionModel->formatQuestions($dbQuestions);

    foreach ($questions as $index => $qtn)
    {
      $this->assertGreaterThanOrEqual('1', $qtn['score']);
      $this->assertLessThanOrEqual(33 * $qtn['level'], $qtn['score']);

      if ($qtn['type'] === 'multiple')
      {
        $this->assertCount(4, $qtn['options']);
      }

      $sessionQuestion = $_SESSION['questions'][$index];
      $this->assertEquals($qtn['level'], $sessionQuestion->level);
      $this->assertEquals($qtn['score'], $sessionQuestion->score);
      $this->assertEquals($qtn['ah'], $sessionQuestion->answer_hash);
    }

    $questionModel->reset();
  }

  public function testLoadDbQuestionsLvl1()
  {
    $questionModel = new QuestionModel();
    $questions = $questionModel->loadDbQuestions(1);
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
    $questionModel = new QuestionModel();
    $questions = $questionModel->loadDbQuestions(2);
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
    $questionModel = new QuestionModel();
    $questions = $questionModel->loadDbQuestions(3);
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
    $questionModel = new QuestionModel();
    $questions = $questionModel->loadDbQuestions(4);
    $this->assertCount(10, $questions);

    foreach ($questions as $qtn) $this->assertEquals('4', $qtn->level);

    return $questions;
  }
}
