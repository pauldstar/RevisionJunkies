<?php namespace App\Models;

class QuestionTest extends \CIUnitTestCase
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('question_model', '_question');
  }

  public function run()
  {
    self::load_questions();
    self::format_questions();
    // self::score();
    // self::level();
  }

  public function load_questions()
  {
    self::_load_questions_lvl_1();
    self::_load_questions_lvl_2();
    self::_load_questions_lvl_3();
    self::_load_questions_lvl_4();
    echo $this->unit->report();
  }

  public function format_questions()
  {
    self::_format_questions_empty();
    self::_format_questions_lvl_1();
    // self::_format_questions_lvl_2();
    // self::_format_questions_lvl_3();
    // self::_format_questions_lvl_4();
    echo $this->unit->report();
  }

  private function _format_questions_empty()
  {
    $test = $this->_question->format_questions(1, []);
    $this->unit->is($test, 'is_array', 'format_questions() returns array');

    $test = empty($test);
    $this->unit->is($test, true, 'format_questions() with empty $db_questions returns empty array');
  }

  private function _format_questions_lvl_1()
  {
    $db_questions = $this->_question->load_questions(1);
    $questions = $this->_question->format_questions(1, $db_questions);

    foreach($questions as $qtn)
    {
      $qtn['type'] === 'multiple' AND
        $this->unit->is(count($qtn['options']), 4, 'Multiple choice questions has 4 options if multiple choice');
    }
  }

  private function _load_questions_lvl_1()
  {
    $questions = $this->_question->load_questions(1);

    $this->unit->is(count($questions), 4, 'Level 1 has 4 questions');

    foreach ($questions as $qtn)
    {
      $this->unit->
        is($qtn->difficulty, 'easy', 'Level 1 has only easy difficulty');
      $this->unit->
        is($qtn->type, 'boolean', 'Level 1 has only boolean questions');
      $this->unit->is($qtn->level, '1', 'Question is Level 1');
    }
  }


  private function _load_questions_lvl_2()
  {
    $questions = $this->_question->load_questions(2);

    $this->unit->is(count($questions), 7, 'Level 2 has 7 questions');

    foreach ($questions as $qtn)
    {
      $this->unit->
        is($qtn->difficulty, 'easy', 'Level 2 has only easy difficulty');
      $this->unit->is($qtn->level, '2', 'Question is Level 2');
    }
  }

  private function _load_questions_lvl_3()
  {
    $questions = $this->_question->load_questions(3);

    $this->unit->is(count($questions), 7, 'Level 3 has 7 questions');

    foreach ($questions as $qtn)
    {
      $this->unit->
        isnt($qtn->difficulty, 'hard', 'Level 3 has no hard difficulty');
      $this->unit->is($qtn->level, '3', 'Question is Level 3');
    }
  }

  private function _load_questions_lvl_4()
  {
    $questions = $this->_question->load_questions(4);
    $this->unit->is(count($questions), 10, 'Level 4 has 10 questions');

    foreach ($questions as $qtn)
      $this->unit->is($qtn->level, '4', 'Question is Level 4');
  }
}
