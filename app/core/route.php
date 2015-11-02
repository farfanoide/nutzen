<?php

namespace App\Core;

class route
{
  public $pattern;
  public $method;
  public $regex;
  public $params;

  public function __construct($pattern = '', $method = 'GET', $handler = '')
  {
    $this->pattern = $pattern;
    $this->method  = $method;
    $this->handler = $handler;
    $this->params  = $this->paramNamesFrom($pattern);
    $this->regex   = $this->expandRegexFor($pattern);
  }

  private function expandRegexFor($pattern)
  {
    $regex = str_replace('/', '\/', $pattern);

    foreach ($this->params as $param)
    {
      $regex = str_replace(":{$param}", "(?<{$param}>\w+)", $regex);
    }

    return "/^{$regex}\/?$/";
  }

  public function matches($pattern)
  {
    return (boolean) preg_match($this->regex, $pattern);
  }

  public function paramNamesFrom($pattern)
  {
    preg_match_all("/(?::(?<params>\w+))/", $pattern, $matches);

    return array_values($matches['params']);
  }

  public function namedParamsFrom($uri)
  {
    preg_match($this->regex, $uri, $matches);

    return array_intersect_key($matches, array_flip($this->params));
  }
}
