<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game_state extends CI_Model
{
  private static $start_time;
  private static $score;
  private static $level;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');

    self::$start_time = &$_SESSION['start_time'];
    self::$level = &$_SESSION['level'];
    self::$score = &$_SESSION['score'];

    self::$start_time OR self::$start_time = time();
    self::$level OR self::$level = 1;
    self::$score OR self::$score = 0;
  }

  public function score($score = NULL)
  {
    if ($score) self::$score += $score;
    else return self::$score;
  }

  public function level($increment = FALSE)
  {
    if ($increment) self::$level++;
    else return self::$level;
  }

  public function start_time($start = FALSE)
  {
    if ($start) self::$start_time = time();
    else return self::$start_time;
  }

  public function time_delta()
  {
    return time() - self::$start_time;
  }

  public function reset()
  {
    self::$score = 0;
    self::$level = 1;
  }
}
