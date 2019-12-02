<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game_model extends CI_Model
{
  private static $start_time;
	private static $score;
	private static $level;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');

    self::$start_time = &$_SESSION['start_time'];
    self::$score = &$_SESSION['score'];
    self::$level = &$_SESSION['level'];

		self::$start_time OR self::$start_time = time();
		self::$level OR self::$level = 1;
		self::$score OR self::$score = 0;
  }

  public function start_time($time = 0)
  {
    if ($time) self::$start_time = $time;
    else return self::$start_time;
  }

  public function score($score = 0, $increment = TRUE)
  {
    if ($score && $increment) self::$score += $score;
    else if (!$increment) self::$score = $score;
    else return self::$score;
  }

  public function level($increment = FALSE, $reset = FALSE)
  {
    if ($increment) self::$level++;
    else if ($reset) self::$level = 1;
    else return self::$level;
  }
}
