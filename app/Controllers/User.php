<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RedirectResponse;
use Config\Services;
use ReflectionException;

class User extends BaseController
{
  use ResponseTrait;

  public function login()
  {
    if (!$this->validate('login'))
      return redirect()->to('/login/100')->withInput();

    $name = $this->request->getVar('login_name');
    $password = $this->request->getVar('login_password');

    $user = $this->userModel->getUser($name, ['username', 'email']);

    $userExists = isset($user) && password_verify($password, $user->password);

    if (!$userExists)
    {
      $this->validator->setError(
        'loginForm', 'Login failed! Please try again...'
      );

      return redirect()->to('/login/100')->withInput();
    }

    if ($user->email_verifier !== null)
    {
      $this->userModel->unverifiedUsername($user->username);
      return redirect()->to('/login/400');
    }

    $this->userModel->login($user);

    return redirect()->to('/');
  }

  //--------------------------------------------------------------------

  /**
   * Sign-up and send email verification to user
   *
   * @return RedirectResponse|void
   * @throws ReflectionException
   */
  public function signup()
  {
    if (! $this->validate('signup'))
      return redirect()->to('login/200')->withInput();

    $user = $this->userModel->createUser($this->request->getVar());

    if (! $user)
    {
      $this->validator->setError(
        'signupForm', 'Server error. Sign Up failed! Please try again later.'
      );

      return redirect()->to('/login/200')->withInput();
    }

    return $this->send_email_verifier($user);
  }

  //--------------------------------------------------------------------

  /**
   * Verify Email when user follows verification email link
   *
   * @param string $username
   * @param string $emailVerifier
   * @return RedirectResponse
   */
  public function verify_email($username, $emailVerifier)
  {
    $user = $this->userModel->getUser($username, 'username');

    if (empty($user) || $user->email_verifier === null)
      return redirect()->to('/login');

    if ($emailVerifier !== $user->email_verifier)
    {
      $this->userModel->unverifiedUsername($user->username);
      return redirect()->to('/login/400');
    }

    $this->userModel->confirmEmailVerification($user->id);

    return redirect()->to('/login/300');
  }

  //--------------------------------------------------------------------

  /**
   * Send email verification to user
   *
   * @param array|string $user
   * @return RedirectResponse
   */
  public function send_email_verifier($user)
  {
    $data = is_object($user) ? $user :
      $this->userModel->getUser($user, 'username');

    if ($data['email_verifier'] === '1') return redirect()->to('/login/300');

    $email = Services::email();

    $email->setSubject('Email Verification');
    $email->setTo($data['email']);
    $email->setMessage(view('template/verify_email', $data));
    $email->send();

    $this->userModel->unverifiedUsername($data['username']);

    return redirect()->to('/login/400');
  }

  // TODO: remove test_email() and show_email()
  public function test_email()
  {
    $email = Services::email();

    $data['user'] = $this->userModel->getUser(5);

    $email->setSubject('Email Verification');
    $email->setTo($data['email']);
    $email->setMessage(view('template/verify_email', $data));
    $email->send(false);

    echo $email->printDebugger(['headers']);
  }

  // TODO: remove test_email() and show_email()
  public function show_email()
  {
    $data['user'] = $this->userModel->getUser(5);
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
    $input = $this->request->getVar('input');

    $validation = Services::validation();

    $invalidEmail = $inputType === 'email' &&
      $validation->check($input, 'valid_email');

    if ($invalidEmail) $this->respond(false);

    $isUnique = $validation->check($input, "is_unique[user.{$inputType}]");

    return $this->respond($isUnique);
  }

  //--------------------------------------------------------------------

  public function logout()
  {
    session_destroy();
    return redirect()->to('/login');
  }
}
