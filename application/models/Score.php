<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Score extends CI_Model
{
  private static $score;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    self::$score = &$_SESSION['score'];
    isset(self::$score) OR self::$score = 0;
  }

  public function set_session_score($score)
  {
    self::$score += $score;
  }
}
