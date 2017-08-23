<?php

class User
{

  public $email;
  public $password;
  public $name;

  public function __construct($name, $email = '', $password = '')
  {
    $this->name     = $name;
    $this->email    = $email;
    $this->password = $password;
  }
}
