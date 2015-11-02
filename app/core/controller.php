<?php

namespace App\Core;

use App\Core\Session as Session;
use App\Core\Response as Response;
use App\Core\View as View;

class Controller
{

  public function __construct()
  {
    $this->response = new Response;
  }

  public function redirect($url, $statusCode = 302)
  {
    $this->response->withHeader('Location', $url)->withStatus($statusCode);
    return $this;
  }

  public function view($view, $params = [])
  {
    $this->response->content = (new View($view, $params))->render();
  }

  public function withMessage($type, $message)
  {
    Session::addMessage($type, $message);
    return $this;
  }

  public function execute($action, $request)
  {
    $this->$action($request);
    return $this->response;
  }

}

