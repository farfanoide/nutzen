<?php

require dirname(__FILE__) . '/../app/core/view.php';
define('__APP_ROOT__', dirname(__FILE__));

// ob_get_clean adds some extra characters so we make sure to remove them.
function clean_output($str)
{
  return trim(preg_replace('/\s{1,}|\r|\n/', ' ', $str));
}

class ViewTest extends PHPUnit_Framework_TestCase
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
    $output = (new View('test_context.html'))->render(['info' => 'Some Information']);

    $this->assertEquals($expected_output, clean_output($output));
  }

  public function testItPrintsWarningWhenContextVariableIsNotFound()
  {
    $output = (new View('test_context.html'))->render();
    $this->assertTrue((boolean) preg_match('/\[WARNING\]/', clean_output($output)));
  }

  public function testItRendersWithinALayout()
  {
    $output = (new View('test_context.html', ['info' => 'INFO'], 'layout.html'))->render();
    $expected_output = clean_output('<!DOCTYPE html> <html> <head> <meta charset="utf-8" /> <title>test</title> </head> <body> <div class="content">INFO</div> </body> </html> ');
    $this->assertEquals($expected_output, clean_output($output));
  }

}
