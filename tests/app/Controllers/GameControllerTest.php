<?php namespace App\Controllers;

use CodeIgniter\Test\ControllerTester;

class GameControllerTest extends \CIUnitTestCase
{
  use ControllerTester;

  public function testLoadGame()
  {
    $result = $this->withURI(base_url('game/load_game'))
      ->controller(Game::class)
      ->execute('load_game');

    $this->assertTrue($result->isOK());

    $questions = json_decode($result->getBody());

    $this->assertCount(4, $questions);

    foreach ($questions as $qtn)
    {
      $this->assertEquals('boolean', $qtn->type);
      $this->assertEquals('1', $qtn->level);
    }

    return $questions;
  }

  public function testStartGame()
  {
    $result = $this->withURI(base_url('game/start_game'))
      ->controller(Game::class)
      ->execute('start_game');

    $this->assertTrue($result->isOK());
  }

  public function testGetQuestions()
  {
    $result = $this->withURI(base_url('game/get_questions'))
      ->controller(Game::class)
      ->execute('load_game');
  }
}
