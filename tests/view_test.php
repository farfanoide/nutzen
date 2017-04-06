<?php

require dirname(__FILE__) . '/../app/core/view.php';
define('__APP_ROOT__', dirname(__FILE__));

use PHPUnit\Framework\TestCase;

function clean_output($str)
{
  return str_replace(array("\r","\n"),'',trim($str));
}

class RouteTest extends TestCase
{

  public function testRenderDoesNotOutput()
  {
    $this->expectOutputString('');
    (new View('template.html'))->render();
  }

  public function testRenderReturnsCorrectString()
  {
    $expected_output = '<span class="alert">empty template</span>';
    $output = (new View('template.html'))->render();

    $this->assertEquals($expected_output, clean_output($output));
  }

  public function testItRendersVariablesFromContext()
  {
    $expected_output = '<div class="content">Some Information</div>';
    $output = (new View('test_context.html', ['info' => 'Some Information']))->render();

    $this->assertEquals($expected_output, clean_output($output));
  }


}
