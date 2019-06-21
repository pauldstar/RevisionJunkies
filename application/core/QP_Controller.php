<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QP_Controller extends Ci_Controller
{
	protected function load_asset($name, $ext)
  {
		$this->load->helper('url');
		
		$path = base_url("assets/{$ext}/{$name}.{$ext}");

		switch ($ext)
		{
			case 'css': return "<link href='{$path}' rel='stylesheet' type='text/css'>";
			case 'js': return "<script src='{$path}'></script>";
		}
  }
}
