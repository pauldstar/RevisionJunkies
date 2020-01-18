<?php namespace App\Models;

class GameTest extends \CIUnitTestCase
{
  public function testStartTime()
  {
    $gameModel = new GameModel;
    $gameModel->startTime(1234567890);
    $test = $gameModel->startTime();
    $this->assertEquals(1234567890, $test);
  }

  public function testScore()
  {
    $gameModel = new GameModel;
    $test = $gameModel->score();
    $this->assertEquals(0, $test);

    $gameModel->score(10);
    $test = $gameModel->score();
    $this->assertEquals(10, $test);

    $gameModel->score(20);
    $test = $gameModel->score();
    $this->assertEquals(30, $test);

    $gameModel->score(40);
    $test = $gameModel->score();
    $this->assertEquals(70, $test);

    $gameModel->score(0, FALSE);
    $test = $gameModel->score();
    $this->assertEquals(0, $test);
  }

  public function testLevel()
  {
    $gameModel = new GameModel;
    $test = $gameModel->level();
    $this->assertEquals(1, $test);

    $gameModel->level(TRUE);
    $test = $gameModel->level();
    $this->assertEquals(2, $test);

    $gameModel->level(TRUE);
    $test = $gameModel->level();
    $this->assertEquals(3, $test);

    $gameModel->level(FALSE, TRUE);
    $test = $gameModel->level();
    $this->assertEquals(1, $test);
  }
 }
