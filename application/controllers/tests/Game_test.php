<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game_test extends QP_Test_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('game_model', '_game');
  }

  public function run()
  {
    self::start_time();
    self::score();
    self::level();
  }

  public function start_time()
  {
    $this->_game->start_time(1234567890);
    $test = $this->_game->start_time();
    $this->unit->is($test, 1234567890, 'Set start time as 1234567890');

    echo $this->unit->report();
  }

  public function score()
  {
    $test = $this->_game->score();
    $this->unit->is($test, 0, 'Initial score is 0');

    $this->_game->score(10);
    $test = $this->_game->score();
    $this->unit->is($test, 10, 'Set score as 10');

    $this->_game->score(20);
    $test = $this->_game->score();
    $this->unit->is($test, 30, 'Increment score by 20');

    $this->_game->score(40);
    $test = $this->_game->score();
    $this->unit->is($test, 70, 'Increment score by 40');

    $this->_game->score(0, FALSE);
    $test = $this->_game->score();
    $this->unit->is($test, 0, 'Reset score to 0');

    echo $this->unit->report();
  }

  public function level()
  {
    $test = $this->_game->level();
    $this->unit->is($test, 1, 'Initial level is 1');

    $this->_game->level(TRUE);
    $test = $this->_game->level();
    $this->unit->is($test, 2, 'Increment level to 2');

    $this->_game->level(TRUE);
    $test = $this->_game->level();
    $this->unit->is($test, 3, 'Increment level to 3');

    $this->_game->level(FALSE, TRUE);
    $test = $this->_game->level();
    $this->unit->is($test, 1, 'Reset level to 1');

    echo $this->unit->report();
  }
 }
