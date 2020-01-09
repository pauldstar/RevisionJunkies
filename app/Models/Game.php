<?php namespace App\Models;

class Game extends BaseModel
{
  private $startTime;
	private $score;
	private $level;

  public function __construct()
  {
    parent::__construct();

    $this->startTime = & $_SESSION['startTime'];
    $this->score = & $_SESSION['score'];
    $this->level = & $_SESSION['level'];

		$this->startTime OR $this->startTime = time();
		$this->level OR $this->level = 1;
		$this->score OR $this->score = 0;
  }

	//--------------------------------------------------------------------

	/**
	 * @param int $time
	 * @return void|int
	 */
	public function startTime($time = 0)
  {
    if ($time) $this->startTime = $time;
    else return $this->startTime;
  }

	//--------------------------------------------------------------------

	/**
	 * @param int $score
	 * @param bool $increment
	 * @return void|int
	 */
	public function score($score = 0, $increment = TRUE)
  {
    if ($score && $increment) $this->score += $score;
    else if (!$increment) $this->score = $score;
    else return $this->score;
  }

	//--------------------------------------------------------------------

	/**
	 * @param bool $increment
	 * @param bool $reset
	 * @return void|int
	 */
	public function level($increment = FALSE, $reset = FALSE)
  {
    if ($increment) $this->level++;
    else if ($reset) $this->level = 1;
    else return $this->level;
  }
}
