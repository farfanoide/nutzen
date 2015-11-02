<?php

namespace App\Controllers;

use App\Core\Controller as BaseController;

class ApplicationController extends BaseController
{

  public function home($request)
  {
    return $this->view('home');
  }

}
