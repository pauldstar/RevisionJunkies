<?php namespace App\Facades;

use App\Models\GameModel;

/**
 * Class GameFacade
 * @package App\Facades
 *
 * @method static reset()
 * @method static startTime(bool $startGame = false)
 * @method static score($score = false)
 * @method static level(bool $increment = false, bool $reset = false)
 * @method static scoreIsValid(float $remainder, int $sum = 0, int $count = null, string $ignoredIndices = null)
 *
 * @mixin GameModel
 */
abstract class GameFacade extends BaseFacade {}