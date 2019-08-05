<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QP_Controller extends Ci_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('url');
  }

  protected function load_asset($name, $ext)
  {
    $path = base_url("assets/{$ext}/{$name}.{$ext}");

    switch ($ext)
    {
      case 'css':
        return "<link href='{$path}' rel='stylesheet' type='text/css'>";

      case 'js':
        return "<script src='{$path}'></script>";
    }
  }

  function terminal($command)
  {
    if(function_exists('system'))
    {
      ob_start();
      system($command, $return_var);
      $output = ob_get_contents();
      ob_end_clean();
    }
    else if(function_exists('passthru'))
    {
      ob_start();
      passthru($command, $return_var);
      $output = ob_get_contents();
      ob_end_clean();
    }
    else if(function_exists('exec'))
    {
      exec($command, $output, $return_var);
      $output = implode('n', $output);
    }
    else if(function_exists('shell_exec'))
    {
      $output = shell_exec($command) ;
    }
    else
    {
      $output = 'Command execution not possible on this system';
      $return_var = 1;
    }

    return [
      'output' => $output,
      'status' => $return_var
    ];
  }
}
