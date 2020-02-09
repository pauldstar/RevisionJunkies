<?php namespace App\Models\Facades;

use App\Models\BaseModel;

/**
 * Class BaseFacade
 * Allows calling model methods as if static
 *
 * @package App\Models\Facades
 * @mixin BaseModel
 */
abstract class BaseFacade
{
  public static $models = [];

  //--------------------------------------------------------------------

  /**
   * This is ONLY a reminder to declare any child classes as abstract
   * So do NOT implement
   *
   * @param $doNotImplement
   * @return mixed
   */
  abstract public static function subclassMustBeAbstract($doNotImplement);

  //--------------------------------------------------------------------

  public static function __static()
  {
    // 'testing' environment already init's session()
    // TODO: remove ENVIRONMENT check once CI4 session testing is possible
    ENVIRONMENT === 'testing' || session();
  }

  //--------------------------------------------------------------------

  public static function __callStatic($method, $args)
  {
    $className = substr(strrchr(static::class, "\\"), 1);
    $className = str_replace('Facade', 'Model', $className);
    $modelName = "\App\Models\\{$className}";

    isset(self::$models[$modelName])
     || self::$models[$modelName] = new $modelName();

    $model = self::$models[$modelName];

    switch(count($args))
    {
      case 0: return $model->$method();
      case 1: return $model->$method($args[0]);
      case 2: return $model->$method($args[0], $args[1]);
      case 3: return $model->$method($args[0], $args[1], $args[2]);
      case 4: return $model->$method($args[0], $args[1], $args[2], $args[3]);
      default: return call_user_func_array([$model, $method], $args);
    }
  }

}