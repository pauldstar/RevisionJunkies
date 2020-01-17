<?php namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\User;

class AjaxAccess implements FilterInterface
{
  use ResponseTrait;

  private $request, $response;

  public function before(RequestInterface $request)
  {
    $this->request = $request;
    $this->response = Services::response();
    $userModel = new User;

    if ($userModel->isLoggedIn()) return $request;

    return $this->failUnauthorized();
  }

  //--------------------------------------------------------------------

  public function after(RequestInterface $request, ResponseInterface $response)
  {
  }
}