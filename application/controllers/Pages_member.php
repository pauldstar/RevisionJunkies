<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages_member extends QP_Controller
{
	public function __construct()
  {
		parent::__construct(TRUE);
  }

	public function statistics()
	{
		$data['title'] = 'statistics';
		$data['styles'] = '';
    $data['page_content'] = '';
    $data['scripts'] = '';
		self::_output_page($data);
	}

	public function picture()
	{
		$data['title'] = 'picture';
		$data['styles'] = '';
    $data['page_content'] = '';
    $data['scripts'] = '';
		self::_output_page($data);
	}

	public function password()
	{
		$data['title'] = 'password';
		$data['styles'] = '';
    $data['page_content'] = '';
    $data['scripts'] = '';
		self::_output_page($data);
	}
}
