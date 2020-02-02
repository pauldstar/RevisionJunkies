<?php namespace Config;

use App\Filters\PageAccess;
use App\Filters\AjaxAccess;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;

class Filters extends BaseConfig
{
  // Makes reading things below nicer,
  // and simpler to change out script that's used.
  public $aliases = [
    'csrf' => CSRF::class,
    'toolbar' => DebugToolbar::class,
    'honeypot' => Honeypot::class,
    'pageAccess' => PageAccess::class,
    'ajaxAccess' => AjaxAccess::class,
  ];

  // Always applied before every request
  public $globals = [
    'before' => [
      // 'csrf',
      //'honeypot'
    ],
    'after' => [
      'toolbar',
      //'honeypot'
    ],
  ];

  // Works on all of a particular HTTP method
  // (GET, POST, etc) as BEFORE filters only
  //     like: 'post' => ['CSRF', 'throttle'],
  public $methods = [
//    'post' => [ 'CSRF' ]
  ];

  // List filter aliases and any before/after uri patterns
  // that they should run on, like:
  //    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
  public $filters = [
    'pageAccess' => [
      'before' => [
        'pages/statistics',
        'pages/picture',
        'pages/password',
        'user/logout'
      ]
    ],

//    'ajaxAccess' => [
//      'before' => [
//        ''
//      ]
//    ]
  ];
}
