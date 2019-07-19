<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Questions extends CI_Model
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

  public function get_session_question($question_id)
  {
    if (isset(self::$questions[$question_id]))
      return self::$questions[$question_id];
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

  public function set_session_questions($questions)
  {
    foreach ($questions as $qtn) self::$questions[$qtn->id] = $qtn;
  }

  public function unset_session_question($question_id)
  {
    unset(self::$questions[$question_id]);
  }

  public function load_questions()
  {
    $this->load->database();
    $this->load->model('state');

    $where = '';
    $limit = 0;

    switch ($this->state->level())
    {
      default:
        $where .= 'difficulty = "hard" OR ';
        $limit += 3;
      case 2:
        $where .= 'difficulty = "medium" OR ';
        $limit += 3;
      case 1:
        $where .= 'difficulty = "easy"';
        $limit += 4;
    }

    $this->db->select('question, type, correct_answer, incorrect_answers');
    $this->db->where($where);
    $this->db->order_by(NULL, 'random');
    $query = $this->db->get('questions', $limit);

    return $query->result();
  }

  public function reset()
  {
    self::$questions = [];
    self::$next_answer_chain_hash = NULL;
    self::$prev_answer_chain_hash = NULL;
  }
}
