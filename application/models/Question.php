<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Question extends CI_Model
{
  private static $questions;
  private static $next_answer_chain_hash;
  private static $prev_answer_chain_hash;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    self::$questions = &$_SESSION['questions'];
    self::$next_answer_chain_hash = &$_SESSION['next_answer_chain_hash'];
    self::$prev_answer_chain_hash = &$_SESSION['prev_answer_chain_hash'];
  }

  public function get_session_question($question_id, $game_level)
  {
    if ( isset(self::$questions[$game_level][$question_id]) )
      return self::$questions[$game_level][$question_id];

    return NULL;
  }

  public function get_next_answer_chain_hash($answer)
  {
    $answer = str_replace(['"', "'", " "], '', $answer);

    $old_hash = self::$next_answer_chain_hash ?? '';
    $new_hash = md5($answer);
    self::$next_answer_chain_hash = md5($old_hash.$new_hash);

    return self::$next_answer_chain_hash;
  }

  public function get_prev_answer_chain_hash($answer_hash)
  {
    $hash = self::$prev_answer_chain_hash ?? '';
    self::$prev_answer_chain_hash = $answer_hash;
    return $hash;
  }

  public function set_session_questions($questions, $game_level)
  {
    self::$questions[$game_level] = $questions;
  }

  public function unset_session_question($question_id, $game_level)
  {
    unset(self::$questions[$game_level][$question_id]);
  }

  public function load_questions($game_level)
  {
    $api_urls = self::get_api_urls($game_level);
    $questions = [];

    foreach ($api_urls as $url)
    {
      $data = json_decode(file_get_contents($url));
      foreach($data->results as $qtn) $questions[] = $qtn;
    }

    shuffle($questions);

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

  public function clear_old_level_questions($game_level)
  {
    $game_level > 2 AND self::$questions[$game_level - 3] = NULL;
  }

  public function reset()
  {
    self::$questions = [];
    self::$next_answer_chain_hash = NULL;
    self::$prev_answer_chain_hash = NULL;
  }
}
