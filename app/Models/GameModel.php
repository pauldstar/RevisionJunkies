<?php namespace App\Models;

class GameModel extends BaseModel
{
  private $startTime;
  private $level;
  private $scores;
  private $cache;
  private $ignoredIndices;

  //--------------------------------------------------------------------

  public function reset()
  {
    $this->startTime = null;
    $this->level = 1;
    $this->scores = [];
    $this->ignoredIndices = '';
    $this->cache = [[]];
  }

  //--------------------------------------------------------------------

  /**
   * Start game counter
   *
   * @param bool $startGame
   * @return void|int
   */
  public function startTime(bool $startGame = false)
  {
    if ($startGame) $this->startTime = time();
    else return $this->startTime;
  }

  //--------------------------------------------------------------------

  /**
   * Set/reset scores
   *
   * @param int|bool $score
   * @return void|array
   */
  public function score($score = false)
  {
    if ($score === true)
    {
      $this->scores = [];
      $this->ignoredIndices = '';
    }
    elseif (is_int($score))
    {
      $this->scores[] = $score;
      $this->ignoredIndices .= '0';
    }
    else return $this->scores;
  }

  //--------------------------------------------------------------------

  /**
   * Get/set level
   *
   * @param bool $increment
   * @param bool $reset
   * @return void|int
   */
  public function level(bool $increment = false, bool $reset = false)
  {
    if ($increment) $this->level++;
    elseif ($reset) $this->level = 1;
    else return $this->level;
  }

  //--------------------------------------------------------------------

  /**
   * Check if user final score is valid
   *
   * @param float $remainder default value is the game score
   * @param int $sum
   * @param int $count
   * @param string $ignoredIndices
   * @return bool
   */
  public function scoreIsValid(float $remainder, int $sum = 0,
                               int $count = null, string $ignoredIndices = null)
  {
    isset($count) || $count = count($this->scores);
    isset($ignoredIndices) || $ignoredIndices = $this->ignoredIndices;

    if ($this->minCache($remainder, $ignoredIndices)) return false;
    if ($remainder <= 0 || $count === 0) return $remainder == 0 ? true : false;

    foreach ($this->scores as $i => $score)
    {
      $shouldIgnore = $ignoredIndices[$i] || ! $this->sameClass($sum, $score);

      if ($shouldIgnore) continue;

      $ignoredIndices[$i] = '1';

      $validScore = $this->scoreIsValid(
        $remainder - $score,
        $sum + $score,
        $count - 1,
        $ignoredIndices
      );

      if ($validScore) return true;

      $ignoredIndices[$i] = '0';
    }

    $this->minCache($remainder, $ignoredIndices, true);

    return false;
  }

  //--------------------------------------------------------------------

  /**
   * Get/set cached sub-problem results in user final score check
   *
   * @param int $remainder
   * @param string $ignoreIndices
   * @param bool $setCache
   * @return void|bool
   */
  private function minCache(int $remainder, string $ignoreIndices,
                            bool $setCache = false)
  {
    if ($setCache)
    {
      $this->cache[$remainder][$ignoreIndices] = true;
      return;
    }

    if (isset($this->cache[$remainder][$ignoreIndices])) return true;

    return false;
  }

  //--------------------------------------------------------------------

  /**
   * If values meet the current merge criteria
   *
   * @param float $v1
   * @param float $v2
   * @return bool
   */
  private function sameClass(float $v1, float $v2): bool
  {
    if ($v1 === 0 || $v2 === 0) return true;

    $v1 = $v1 / 10;
    $v2 = $v2 / 10;

    $mod1 = $v1 % 2;
    $mod2 = $v2 % 2;

    return $mod1 === $mod2;
  }
}