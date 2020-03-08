<?php namespace App\Facades;

use App\Models\QuestionModel;

/**
 * Class QuestionFacade
 * @package App\Facades
 *
 * @method static formatQuestions(array $dbQuestions): array
 * @method static loadQuestions(int $level): array
 * @method static nextSessionQuestion(): object
 * @method static nextHashSecret(): string
 * @method static answerScore(object $sessionQuestion, string $userAnswerHash): int
 * @method static reset()
 * @method static userAnswerHash(object $sessionQuestion, string $currentHashSecret, string $answerCode = null)
 *
 * @mixin QuestionModel
 */
abstract class QuestionFacade extends BaseFacade {}