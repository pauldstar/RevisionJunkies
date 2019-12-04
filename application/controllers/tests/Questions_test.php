<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Questions_test extends QP_Test_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('question_model', '_question');
  }

  public function run()
  {
    self::load_questions();
    // self::score();
    // self::level();
  }

  public function load_questions()
  {
    $questions = $this->_question->load_questions(1);

    $this->unit->is(count($questions), 4, 'Level 1 has 4 questions');

    foreach ($questions as $qtn)
    {
      $this->unit->
        is($qtn->difficulty, 'easy', 'Level 1 has only easy difficulty');
      $this->unit->
        is($qtn->type, 'boolean', 'Level 1 has only boolean questions');
    }

    $questions = $this->_question->load_questions(2);

    $this->unit->is(count($questions), 7, 'Level 2 has 7 questions');

    foreach ($questions as $qtn)
      $this->unit->
        is($qtn->difficulty, 'easy', 'Level 2 has only easy difficulty');

    $questions = $this->_question->load_questions(3);

    $this->unit->is(count($questions), 7, 'Level 3 has 7 questions');

    foreach ($questions as $qtn)
      $this->unit->
        isnt($qtn->difficulty, 'hard', 'Level 3 has no hard difficulty');

    $questions = $this->_question->load_questions(4);
    $this->unit->is(count($questions), 10, 'Level 4 has 10 questions');

    echo $this->unit->report();
  }

  public function session_questions()
  {
    $test = $this->_question->set_session_questions();
    $this->unit->is($test, 'is_int', 'start_time() returns int');

    $this->_question->start_time(1234567890);
    $test = $this->_question->start_time();
    $this->unit->is($test, 1234567890, 'Set start time as 1234567890');

    echo $this->unit->report();
  }
}
