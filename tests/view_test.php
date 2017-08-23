<?php

require dirname(__FILE__) . '/../app/core/view.php';

// Definimos __APP_ROOT__ asi la clase View sabe donde encontrar los templates
// que le damos para renderizar.
define('__APP_ROOT__', dirname(__FILE__));

// borramos los caracteres extra que ob_get_clean agrega al final del buffer.
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
    $expected_output = clean_output('<div class="layout"><div class="content">INFO</div> </div>');
    $this->assertEquals($expected_output, clean_output($output));
  }


  public function testItCanIncludePartials()
  {
    $output = (new View('template_with_partial.html', ['info' => 'INFO']))->render();
    $expected_output = clean_output('<div class="content">INFO from within a partial </div>');
    $this->assertEquals($expected_output, clean_output($output));
  }

  public function testContextPassedToPartialsTakesPrecedence()
  {
    $output = (new View('template_with_partial_with_context.html', ['info' => 'INFO']))->render();
    $expected_output = clean_output('<div class="content">TEMPLATE INFO from within a partial </div>');
    $this->assertEquals($expected_output, clean_output($output));
  }

}
