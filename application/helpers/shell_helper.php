<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * QuePenny shell commands helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Paul Ogbeiwi @ QuePenny
 */

if ( ! function_exists('terminal'))
{
  /**
   * Terminal
   *
   * Execute shell commands; returns output and status
   * Status 0 if successful; status 1 if unsuccessful
   *
   * @param	string $command
   * @return array
   */

   function terminal($command)
   {
     if(function_exists('passthru'))
     {
       ob_start();
       passthru($command, $return_var);
       $output = ob_get_contents();
       ob_end_clean();
     }
     if(function_exists('system'))
     {
       ob_start();
       system($command, $return_var);
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
