<?php namespace App\Controllers;

class Migrate extends \CodeIgniter\Controller
{
	public function index()
	{
		$migrate = \Config\Services::migrations();

		$migrate->latest();
	}
}
