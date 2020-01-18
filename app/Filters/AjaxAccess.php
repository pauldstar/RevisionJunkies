<?php namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\UserModel;

class AjaxAccess implements FilterInterface
{
  use ResponseTrait;

  private $request, $response;

  public function before(RequestInterface $request)
  {
    $userModel = new UserModel;
    if ($userModel->isLoggedIn()) return $request;

    $this->request = $request;
    $this->response = Services::response();
    return $this->failUnauthorized();
  }

  //--------------------------------------------------------------------

  public function after(RequestInterface $request, ResponseInterface $response)
  {
  }
}