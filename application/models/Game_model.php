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

  public function start_time($time = NULL)
  {
    if ($time) self::$start_time = $time;
    else return self::$start_time;
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

}
