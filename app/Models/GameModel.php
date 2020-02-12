<?php namespace App\Models;

class GameModel extends BaseModel
{
  private $startTime;
  private $score;
  private $level;

  public function __construct()
  {
    parent::__construct();

    $this->startTime = null;
    $this->level = 1;
    $this->score = 0;
  }

  //--------------------------------------------------------------------

  /**
   * @param bool $startGame
   * @return void|int
   */
  public function startTime(bool $startGame = false)
  {
    if ($startGame) $this->startTime = time();
    else return $this->startTime;
  }

  //--------------------------------------------------------------------

  /**
   * @param int $score
   * @param bool $increment
   * @return void|int
   */
  public function score($score = 0, $increment = true)
  {
    if ($score && $increment) $this->score += $score;
    elseif (!$increment) $this->score = $score;
    else return $this->score;
  }

  //--------------------------------------------------------------------

  /**
   * @param bool $increment
   * @param bool $reset
   * @return void|int
   */
  public function level(bool $increment = false, bool $reset = false)
  {
    if ($increment) $this->level++;
    elseif ($reset) $this->level = 1;
    else return $this->level;
  }

  //--------------------------------------------------------------------

  public function reset()
  {
    $this->score(0);
    $this->level(false, true);
  }
}