<?php namespace App\Models\Facades;

abstract class GameFacade extends BaseFacade
{
  private static $startTime;
  private static $score;
  private static $level;

  /**
   * Initialise class static properties for session
   */
  public static function __static()
  {
    parent::__static();

    self::$startTime = & $_SESSION['startTime'];
    self::$score = & $_SESSION['score'];
    self::$level = & $_SESSION['level'];

    self::$startTime || self::$startTime = null;
    self::$level || self::$level = 1;
    self::$score || self::$score = 0;
  }

  //--------------------------------------------------------------------

  /**
   * @param bool $startGame
   * @return void|int
   */
  public static function startTime(bool $startGame = false)
  {
    if ($startGame) self::$startTime = time();
    else return self::$startTime;
  }

  //--------------------------------------------------------------------

  /**
   * @param int $score
   * @param bool $increment
   * @return void|int
   */
  public static function score($score = 0, $increment = true)
  {
    if ($score && $increment) self::$score += $score;
    elseif (!$increment) self::$score = $score;
    else return self::$score;
  }

  //--------------------------------------------------------------------

  /**
   * @param bool $increment
   * @param bool $reset
   * @return void|int
   */
  public static function level(bool $increment = false, bool $reset = false)
  {
    if ($increment) self::$level++;
    elseif ($reset) self::$level = 1;
    else return self::$level;
  }

  //--------------------------------------------------------------------

  public static function reset()
  {
    self::score(0);
    self::level(false, true);
  }
}