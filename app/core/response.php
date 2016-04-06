<?php

namespace App\Core;

class Response
{

  public $status;
  public $headers;
  public $content;

  /**
   * Constructor.
   *
   * @param string $content Content to be echoed out to the Web Server
   * @param int    $status  The response status code
   * @param array  $headers An array of HTTP headers
   *
   */

  public function __construct($content = '', $status = 200, $headers = [])
  {
    $this->content = $content;
    $this->status  = $status;
    $this->headers = $headers;
  }

  /**
   * Dumps HTTP Response for the Web Server, including status code, headers and
   * body.
   */
  public function send()
  {
    $this->sendStatus();
    $this->sendHeaders();

    echo $this->content;
  }

  /**
   * Sends HTTP Status Code in the proper response format.
   */
  protected function sendStatus()
  {
    header(sprintf('HTTP/1.0 %s', $this->status));
  }

  /**
   * Sends HTTP headers in the proper response format.
   */
  protected function sendHeaders()
  {
    foreach ($this->headers as $name => $value)
    {
      header("{$name}: {$value}");
    }
  }

  /**
   * Adds a header to a custom data store, note that new values for already set
   * headers will overwrite previous ones.
   *
   * @param string $name  Name of the HTTP header, also used as key.
   * @param string $value Value of the HTTP header.
   *
   * @return Response
   */
  public function withHeader($name, $value)
  {
    $this->headers[$name] = $value;

    return $this;
  }

  /**
   * Adds status code
   *
   * @param int $statusCode Status Code number (between 200 and 599).
   *
   * @return Response
   */
  public function withStatus($statusCode)
  {
    if ($statusCode >= 200 && $statusCode <=599)
    {
      $this->status = $statusCode;
    } else {
      throw new Exception(500, 'Invalid Status Code for response.');
    }

    return $this;
  }
}
