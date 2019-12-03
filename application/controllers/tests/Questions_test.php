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
    self::session_questions();
    // self::score();
    // self::level();
  }

  public function load_questions()
  {
    $questions = $this->_question->load_questions(1);

    $test = count($questions);
    $this->unit->run($test, 4, '4 questions for level 1');

    $test = count($questions);


  }

  public function session_questions()
  {
    $test = $this->_question->set_session_questions();
    $this->unit->run($test, 'is_int', 'start_time() returns int');

    $this->_question->start_time(1234567890);
    $test = $this->_question->start_time();
    $this->unit->run($test, 1234567890, 'Set start time as 1234567890');

    echo $this->unit->report();
  }
}
