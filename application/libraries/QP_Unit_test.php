<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Unit Testing Class
 *
 * Simple testing class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	UnitTesting
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/unit_testing.html
 */
class QP_Unit_test extends CI_Unit_test
{
  /**
   * Check that test value IS the expected value(s)
   *
   * @param	mixed	$test
   * @param	mixed|array	$expected
   * @param	string $test_name
   * @param	string $notes
   * @return	string
   */
  public function is($test, $expected = TRUE, $test_name = 'undefined', $notes = '')
  {
    if (is_array($expected))
    {
      foreach($expected as $exp)
        $this->run($test, $exp, $test_name, $notes);
    }
    else $this->run($test, $expected, $test_name, $notes);
  }

  /**
   * Checks that test value IS NOT 'unexpected' value(s)
   *
   * @param	mixed	$test
   * @param	mixed|array	$unexpected
   * @param	string $test_name
   * @param	string $notes
   * @return	string
   */
  public function isnt($test, $unexpected = TRUE, $test_name = 'undefined', $notes = '')
  {
    if (is_array($unexpected))
    {
      foreach($unexpected as $exp)
        $this->run_not($test, $exp, $test_name, $notes);
    }
    else $this->run_not($test, $unexpected, $test_name, $notes);
  }

  /**
   * Run the tests
   * Ensures that the test value IS NOT the 'unexpected' value
   *
   * @param	mixed	$test
   * @param	mixed	$not_expected
   * @param	string $test_name
   * @param	string $notes
   * @return	string
   */
  protected function run_not($test, $unexpected = TRUE, $test_name = 'undefined', $notes = '')
  {
    if ($this->active === FALSE)
    {
      return FALSE;
    }

    $is_type_test = in_array($unexpected, [
      'is_object', 'is_string', 'is_bool', 'is_true',
      'is_false', 'is_int', 'is_numeric', 'is_float',
      'is_double', 'is_array', 'is_null', 'is_resource'
    ], TRUE);

    if ($is_type_test)
    {
      $result = !$unexpected($test);
      $extype = str_replace(
        ['true', 'false'], 'bool', str_replace('is_', '', $unexpected)
      );
    }
    else
    {
      $result = ($this->strict === TRUE) ?
        ($test !== $unexpected) : ($test != $unexpected);
      $extype = gettype($unexpected);
    }

    $back = $this->_backtrace();

    $report = array (
      'test_name'     => $test_name,
      'test_datatype' => gettype($test),
      'res_datatype'  => $extype,
      'result'        => ($result === TRUE) ? 'passed' : 'failed',
      'file'          => $back['file'],
      'line'          => $back['line'],
      'notes'         => $notes
    );

    $this->results[] = $report;

    return $this->report($this->result(array($report)));
  }
}
