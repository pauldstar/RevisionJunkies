<?php namespace App\Models;

class GameTest extends \CIUnitTestCase
{
  public function testStartTime()
  {
    $game = new Game;
    $game->startTime(1234567890);
    $test = $game->startTime();
    $this->assertEquals(1234567890, $test);
  }

  public function testScore()
  {
    $game = new Game;
    $test = $game->score();
    $this->assertEquals(0, $test);

    $game->score(10);
    $test = $game->score();
    $this->assertEquals(10, $test);

    $game->score(20);
    $test = $game->score();
    $this->assertEquals(30, $test);

    $game->score(40);
    $test = $game->score();
    $this->assertEquals(70, $test);

    $game->score(0, FALSE);
    $test = $game->score();
    $this->assertEquals(0, $test);
  }

  public function testLevel()
  {
    $game = new Game;
    $test = $game->level();
    $this->assertEquals(1, $test);

    $game->level(TRUE);
    $test = $game->level();
    $this->assertEquals(2, $test);

    $game->level(TRUE);
    $test = $game->level();
    $this->assertEquals(3, $test);

    $game->level(FALSE, TRUE);
    $test = $game->level();
    $this->assertEquals(1, $test);
  }
 }
