<?php

require dirname(__FILE__) . '/../app/core/route.php';

class RouteTest extends PHPUnit_Framework_TestCase
{

  public function testPatternWithoutPlaceholders()
  {
    $route = new Route('/home');
    $this->assertEquals('/^\/home\/?$/', $route->regex);

    $route = new Route('/');
    $this->assertEquals('/^\/\/?$/', $route->regex);
  }

  public function testPatternWithOnePlaceholder()
  {
    $route = new Route('/resources/:id/edit/:second');
    $this->assertEquals('/^\/resources\/(?<id>\w+)\/edit\/(?<second>\w+)\/?$/', $route->regex);
  }

  public function testStaticRouteMatches()
  {
    $route = new Route('/myRoute');
    $this->assertTrue($route->matches('/myRoute'));
  }

  public function testMatchesRequestWithTrailingSlash()
  {
    $route = new Route('/resources');
    $this->assertTrue($route->matches('/resources/'));
  }

  public function testRouteWithParamsMatches()
  {
    $route = new Route('/resources/:id/edit/:second');
    $this->assertFalse($route->matches('/myRoute'));
    $this->assertTrue($route->matches('/resources/param/edit/secondparam'));
  }
}
