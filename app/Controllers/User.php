<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Config\Services;
use ReflectionException;

class User extends BaseController
{
	use ResponseTrait;

	public function login()
	{
		$name = $this->request->getVar('login-name');
		$password = $this->request->getVar('login-password');

		isset($name) AND isset($password) OR redirect()->to('login/100');

		$user = $this->userModel->getUser($name, ['username', 'email'], true);

		$userExists = isset($user) && password_verify($password, $user->password);

		if (!$userExists) redirect()->to('login/200')->with('login_form',
			'Login failed! Please try again...'
		);

		if ($user->email_verified === '0')
		{
			$this->userModel->unverifiedUsername($user->username);
			redirect()->to('login/400');
		}

		$this->userModel->login($user);

		redirect();
	}

	//--------------------------------------------------------------------

	/**
	 * Sign-up and send email verification to user
	 *
	 * @return void
	 * @throws ReflectionException
	 */
	public function signup()
	{
		if (!$this->validate('signup'))
			redirect()->to('login/100')->withInput();

		$user = $this->userModel->createUser($this->request->getVar());

		if (!$user) redirect()->to('login/200')->with('signup_form',
			'Server error. Sign Up failed! Please try again later.'
		);

		$this->send_email_verifier($user);
	}

	//--------------------------------------------------------------------

	/**
	 * Verify Email when user follows verification email link
	 *
	 * @param string $username
	 * @param string $emailVerifier
	 * @return void
	 */
	public function verify_email($username, $emailVerifier)
	{
		$user = $this->userModel->getUser($username, 'username');

		isset($user) AND $user->email_verified === '0' OR redirect()->to('login');

		if ($emailVerifier !== $user->email_verifier)
		{
			$this->userModel->unverifiedUsername($user->username);
			redirect()->to('login/400');
		}

		$this->userModel->confirmEmailVerification($user->id);

		redirect()->to('login/300');
	}

	//--------------------------------------------------------------------

	/**
	 * Send email verification to user
	 *
	 * @param array|string $user
	 * @return void
	 */
	public function send_email_verifier($user)
	{
		$data = is_object($user) ? $user :
			(array) $this->userModel->getUser($user, 'username');

		if ($data['email_verified'] === '1') redirect()->to('login/300');

		$email = Services::email();

		$email->setSubject('Email Verification');
		$email->setTo($data['email']);
		$email->setMessage( view('template/verify_email', $data) );
		$email->send();

		$this->userModel->unverifiedUsername($data['username']);

		redirect()->to('login/400');
	}

	// TODO: remove test_email() and show_email()
	public function test_email()
	{
		$email = Services::email();

		$data = (array) $this->userModel->getUser(5);

		$email->setSubject('Email Verification');
		$email->setTo($data['email']);
		$email->setMessage( view('template/verify_email', $data) );
		$email->send(false);

		echo $email->printDebugger(['headers']);
	}

	// TODO: remove test_email() and show_email()
	public function show_email()
	{
		$data = (array) $this->userModel->getUser(5);
		echo view('template/verify_email', $data);
	}

	//--------------------------------------------------------------------

	/**
	 * Check if login/signup form input is valid
	 * Called mostly by ajax calls
	 *
	 * @param string $inputType
	 * @return void
	 */
	public function is_valid($inputType)
	{
		$input = $this->request->getVar('inputText');

		$validation = Services::validation();

		$invalidEmail = $inputType === 'email' &&
			$validation->check($input, 'valid_email');

		if ($invalidEmail) $this->failValidationError('Invalid Email Format');

		$isUnique = $validation->check($input, "is_unique[user.{$inputType}]");

		return $isUnique ?
			$this->respond(true, 200) :
			$this->failValidationError('Email Not Unique');
	}

	//--------------------------------------------------------------------

	public function logout()
	{
		session_destroy();
		redirect()->to('login');
	}
}
