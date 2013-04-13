<?php

class Common_Test extends PHPUnit_Framework_TestCase
{
  protected $_className;
  
  public function testClassExists()
  {
    $this->assertEquals(class_exists($this->_className), true);
  }
  
  public function testConstruction()
  {
    $this->assertInstanceOf($this->_className, $this->_factory());
  }
  
  protected function _factory()
  {
    return new $this->_className();
  }
}
