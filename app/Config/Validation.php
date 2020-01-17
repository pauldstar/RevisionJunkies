<?php namespace Config;

class Validation
{
	//--------------------------------------------------------------------
	// Setup
	//--------------------------------------------------------------------

	/**
	 * Stores the classes that contain the
	 * rules that are available.
	 *
	 * @var array
	 */
	public $ruleSets = [
		\CodeIgniter\Validation\Rules::class,
		\App\Libraries\FormatRules::class,
		\CodeIgniter\Validation\FileRules::class,
		\CodeIgniter\Validation\CreditCardRules::class,
	];

	/**
	 * Specifies the views that are used to display the
	 * errors.
	 *
	 * @var array
	 */
	public $templates = [
		'list'   => 'CodeIgniter\Validation\Views\list',
		'single' => 'CodeIgniter\Validation\Views\single',
	];

	//--------------------------------------------------------------------
	// Rules
	//--------------------------------------------------------------------

	public $signup = [
		'firstname' => 'required|name',
		'lastname' => 'required|name',
		'username' => 'required|username|max_length[20]|is_unique[user.username,id,{id}]',
		'password' => 'required|min_length[8]',
		'email' => 'required|valid_email|is_unique[user.email,id,{id}]'
	];

	public $login = [
    'login_name' => 'required',
    'login_password' => 'required',
  ];
}
