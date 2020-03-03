<?php namespace App\Facades;

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
    GameFacade::score(true);
    $this->assertEmpty(GameFacade::score());

    GameFacade::score(10);
    $this->assertCount(1, GameFacade::score());
    $this->assertEquals(10, GameFacade::score()[0]);

    GameFacade::score(20);
    $this->assertCount(2, GameFacade::score());
    $this->assertEquals(20, GameFacade::score()[1]);

    GameFacade::score(40);
    $this->assertCount(3, GameFacade::score());
    $this->assertEquals(40, GameFacade::score()[2]);

    GameFacade::score(true);
    $this->assertEmpty(GameFacade::score());
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
