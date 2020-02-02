<?php namespace App\Models;

class QuestionModel extends BaseModel
{
  protected $useTimestamps = true;

  protected $allowedFields = [
    'user_id',
    'question',
    'category',
    'type',
    'difficulty',
    'correct_answer',
    'incorrect_answers',
  ];

  protected $afterFind = ['decodeIncorrectAnswers'];

  //--------------------------------------------------------------------

  protected function decodeIncorrectAnswers(array $data): array
  {
    foreach ($data['data'] as $i => $question)
    {
      $answers = $question->incorrect_answers;
      $question->incorrect_answers = json_decode($answers);
    }

    return $data;
  }
}