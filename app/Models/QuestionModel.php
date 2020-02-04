<?php namespace App\Models;

use App\Entities\QuestionEntity;

class QuestionModel extends BaseModel
{
  protected $returnType = QuestionEntity::class;
  protected $useTimestamps = true;

  protected $allowedFields = [
    'user_id',
    'question',
    'category',
    'type',
    'difficulty',
    'correct_answer',
    'incorrect_answers'
  ];
}