<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages_member extends QP_Controller
{
	public function __construct()
  {
		parent::__construct();
		self::$logged_in OR redirect();
  }

	public function statistics()
	{
		$data = self::_init_page_data();
		$data['title'] = 'statistics';
		$data['styles'] = '';
    $data['page_content'] = '';
    $data['scripts'] = '';
		self::_output_page($data);
	}

	public function picture()
	{
		$data = self::_init_page_data();
		$data['title'] = 'picture';
		$data['styles'] = '';
    $data['page_content'] = '';
    $data['scripts'] = '';
		self::_output_page($data);
	}

	public function password()
	{
		$data = self::_init_page_data();
		$data['title'] = 'password';
		$data['styles'] = '';
    $data['page_content'] = '';
    $data['scripts'] = '';
		self::_output_page($data);
	}
}
