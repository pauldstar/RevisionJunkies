<?php namespace App\Models\Facades;

class GameFacadeTest extends \CIUnitTestCase
{
  public function testStartTime()
  {
    GameFacade::startTime(true);
    $expected = time();
    $this->assertEquals($expected, GameFacade::startTime());
  }

  public function testScore()
  {
    $test = GameFacade::score();
    $this->assertEquals(0, $test);

    GameFacade::score(10);
    $test = GameFacade::score();
    $this->assertEquals(10, $test);

    GameFacade::score(20);
    $test = GameFacade::score();
    $this->assertEquals(30, $test);

    GameFacade::score(40);
    $test = GameFacade::score();
    $this->assertEquals(70, $test);

    GameFacade::score(0, false);
    $test = GameFacade::score();
    $this->assertEquals(0, $test);
  }

  public function testLevel()
  {
    $test = GameFacade::level();
    $this->assertEquals(1, $test);

    GameFacade::level(true);
    $test = GameFacade::level();
    $this->assertEquals(2, $test);

    GameFacade::level(true);
    $test = GameFacade::level();
    $this->assertEquals(3, $test);

    GameFacade::level(false, true);
    $test = GameFacade::level();
    $this->assertEquals(1, $test);
  }
}
