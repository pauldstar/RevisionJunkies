<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QP_Form_validation extends CI_Form_validation
{
  /**
   * Save field data in session for reuse in other controllers
   *
   * @return	void
   */
  public function save_data()
  {
    $this->CI->load->library('session');
    $_SESSION['form_validation_field_data'] = $this->_field_data;
    $_SESSION['form_validation_error_array'] = $this->_error_array;
  }

  /**
   * Reload stored field data from sessions for reuse
   *
   * Allows use of form_helper functions, such as validation_errors() and
   * form_error(), on previously validated data
   *
   * @return	void
   */
  public function reload_data()
  {
    $this->CI->load->library('session');
    $this->_field_data = $_SESSION['form_validation_field_data'];
    $this->_error_array = $_SESSION['form_validation_error_array'];
  }

  /**
   * Add custom error for (existent or new) field
   *
   * Allows use of form_helper functions such as form_error($field) and
   * validation_errors() on previously validated data
   *
	 * @param	string	$field	To identify error
	 * @param	string	$message	Error message
   * @return	void
   */
  public function set_error($field, $message)
  {
    $this->_field_data[$field]['error'] = $message;
    $this->_error_array[] = $message;
  }
}
