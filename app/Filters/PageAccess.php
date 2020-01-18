<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\UserModel;

class PageAccess implements FilterInterface
{
  public function before(RequestInterface $request)
  {
    $userModel = new UserModel;

    if (! $userModel->isLoggedIn()) return redirect()->to('login');

    return $request;
  }

  //--------------------------------------------------------------------

  public function after(RequestInterface $request, ResponseInterface $response)
  {
  }
}