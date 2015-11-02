<?php

namespace App\Core;

use App\Models\Session as Session;

class Response
{
  public $status;
  public $headers;
  public $content;

  public function __construct($content = '', $status = 200, $headers = [])
  {
    $this->content = $content;
    $this->status  = $status;
    $this->headers = $headers;
  }

  public function send()
  {
    $this->sendStatus();
    $this->sendHeaders();

    echo $this->content;
  }

  protected function sendStatus()
  {
    header(sprintf('HTTP/1.0 %s', $this->status));
  }

  protected function sendHeaders()
  {
    foreach ($this->headers as $name => $value)
    {
      header("{$name}: {$value}");
    }
  }

  public function withMessage($type, $message)
  {
    Session::addMessage($type, $message);

    return $this;
  }

  public function withHeader($name, $value)
  {
    $this->headers[$name] = $value;

    return $this;
  }

  public function withStatus($statusCode)
  {
    $this->status = $statusCode;

    return $this;
  }
}
