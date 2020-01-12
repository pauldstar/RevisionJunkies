<?php namespace App\Models;

class QuestionTest extends \CIUnitTestCase
{
  public function testFormatQuestionsEmpty()
  {
    $question = new Question();
    $test = $question->formatQuestions([]);
    $this->assertIsArray($test);
    $this->assertEmpty($test);
  }

  /**
   * @depends testLoadDbQuestionsLvl1
   * @param array $dbQuestions
   */
  public function testFormatQuestionsLvl1(array $dbQuestions)
  {
    $question = new Question();
    $questions = $question->formatQuestions($dbQuestions);

    foreach($questions as $index => $qtn)
    {
      $this->assertEquals('1', $qtn['level']);

      if ($qtn['type'] === 'multiple')
      {
        $this->assertCount(4, $qtn['options']);

        $testOptions = array_merge(
          [ $dbQuestions[$index]->correct_answer ],
          json_decode($dbQuestions[$index]->incorrect_answers)
        );
        $this->assertNotSame($qtn['options'], $testOptions);

        $sessionQuestion = session('questions')[$index];
        $this->assertEquals($qtn['level'], $sessionQuestion->level);
        $this->assertEquals($qtn['score'], $sessionQuestion->score);
      }
    }

    $question->reset();
  }

  public function testLoadDbQuestionsLvl1()
  {
    $question = new Question();
    $questions = $question->loadDbQuestions(1);
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
    $question = new Question();
    $questions = $question->loadDbQuestions(2);
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
    $question = new Question();
    $questions = $question->loadDbQuestions(3);
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
    $question = new Question();
    $questions = $question->loadDbQuestions(4);
    $this->assertCount(10, $questions);

    foreach ($questions as $qtn) $this->assertEquals('4', $qtn->level);

    return $questions;
  }
}
