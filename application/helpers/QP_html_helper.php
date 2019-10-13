<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('script_tag'))
{
	/**
	 * Script tag
	 *
	 * Generates link to a javascript file
	 *
	 * @param	string script file name
	 * @return string
	 */
	function script_tag($filename)
	{
		$CI =& get_instance();
    $src = $CI->config->base_url("assets/js/{$filename}.js");
    return "<script src='{$src}'></script>";
  }
}

if ( ! function_exists('css_tag'))
{
	/**
	 * CSS link tag
	 *
	 * Generates link to a CSS file
	 *
	 * @param	string CSS file name
	 * @return string
	 */
	function css_tag($filename)
	{
		$CI =& get_instance();
    $href = $CI->config->base_url("assets/css/{$filename}.css");
    return "<link href='{$href}' rel='stylesheet' type='text/css'>";
  }
}
