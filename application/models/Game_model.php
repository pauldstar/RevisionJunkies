<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Game_model extends CI_Model
{
  private static $score;
  private static $level;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    self::$level = &$_SESSION['level'];
    self::$score = &$_SESSION['score'];
    self::$level OR self::$level = 1;
    self::$score OR self::$score = 0;
  }

  public function set_session_score($score)
  {
    self::$score += $score;
  }

  public function level($increment = NULL)
  {
    if ($increment) self::$level++;
    else return self::$level;
  }

  public function reset()
  {
    self::$score = 0;
    self::$level = 1;
  }
}
