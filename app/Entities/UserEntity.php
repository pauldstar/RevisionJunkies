<?php namespace App\Entities;

use CodeIgniter\Entity;

class UserEntity extends Entity
{
  protected $casts = [
    'hi_score' => 'integer',
    'total_qp' => 'integer'
  ];

  //--------------------------------------------------------------------

  public function setPassword(string $password)
  {
    $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
  }

  //--------------------------------------------------------------------

  protected function setHiScore(int $value)
  {
    if (isset($this->attributes['hi_score']))
      $value > $this->attributes['hi_score']
        && $this->attributes['hi_score'] = $value;
    else $this->attributes['hi_score'] = $value;
  }

  //--------------------------------------------------------------------

  protected function getHiScore()
  {
    return $this->attributes['hi_score'] ?? 0;
  }

  //--------------------------------------------------------------------

  protected function setTotalQp(int $value)
  {
    if (isset($this->attributes['total_qp']))
      $this->attributes['total_qp'] += $value;
    else $this->attributes['total_qp'] = $value;
  }

  //--------------------------------------------------------------------

  protected function getTotalQp()
  {
    return $this->attributes['total_qp'] ?? 0;
  }
}