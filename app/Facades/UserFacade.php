<?php namespace App\Facades;

use App\Models\UserModel;

/**
 * Class UserFacade
 * @package App\Facades
 *
 * @method static isLoggedIn(): bool
 * @method static getUser($id = '', $idType = 'id')
 * @method static createUser(array $input)
 * @method static confirmEmailVerification($userId)
 * @method static unverifiedUsername($username = null)
 * @method static updateStats($score = 0)
 * @method static login($user)
 * @method static logout()
 *
 * @mixin UserModel
 */
abstract class UserFacade extends BaseFacade {}