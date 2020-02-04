<?php namespace App\Entities;

use CodeIgniter\Entity;

class QuestionEntity extends Entity
{
  protected $casts = [
    'incorrect_answers' => 'json'
  ];
}