<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Webhooks extends QP_Controller
{
	public function deploy()
	{
		// secret QDjzkg574GjPw29B
		echo '<pre>';
		var_dump($_REQUEST);
		echo '</pre>';
	}
}
