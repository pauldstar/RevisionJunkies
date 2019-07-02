<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends CI_Model
{
  private static $questions;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    self::$questions = &$_SESSION['questions'];
  }

  public function get_session_question($question_id, $game_level)
  {
    return self::$questions[$game_level][$question_id];
  }

  public function set_session_questions($questions, $game_level)
  {
    self::$questions[$game_level] = $questions;
  }

  public function delete_session_question($question_id, $game_level)
  {
    unset(self::$questions[$game_level][$question_id]);
  }

  public function load_questions($game_level)
  {
    self::clear_old_session_questions($game_level);

    $api_urls = self::get_api_urls($game_level);
    $questions = [];

    foreach ($api_urls as $url)
    {
      $data = json_decode(file_get_contents($url));
      foreach($data->results as $qtn) $questions[] = $qtn;
    }

    return $questions;
  }

  private function get_api_urls($game_level)
	{
		$urls = [];

		switch ($game_level)
		{
			default: $urls[] = 'https://opentdb.com/api.php?amount=5&difficulty=hard';
			case 2: $urls[] = 'https://opentdb.com/api.php?amount=5&difficulty=medium';
			case 1: $urls[] = 'https://opentdb.com/api.php?amount=5&difficulty=easy';
		}

		return $urls;
	}

  public function clear_old_session_questions($game_level)
  {
    switch ($game_level)
    {
      case 1: self::$questions = []; break;
      case 2: break;
      default: self::$questions[$game_level - 3] = NULL;
    }
  }
}
