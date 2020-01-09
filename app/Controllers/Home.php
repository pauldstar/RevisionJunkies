<?php namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
//		return view('welcome_message');
	echo file_get_contents(APPPATH.'Database/Seeds/SQL/questions.sql');
	}

	//--------------------------------------------------------------------

}
