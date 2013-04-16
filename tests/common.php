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
  
  public function test__toString_Fails()
  {
    $reporting = error_reporting(0);
    $this->assertEmpty((string) $this->_factory());
    error_reporting($reporting);
    $lastError = error_get_last();
    $this->assertEquals('No table specified', $lastError['message']);
  }
  
  public function testParts()
  {
    $this->assertEquals(true, is_array($this->_factory()->parts()));
  }
  
  public function testParams()
  {
    $this->assertEquals(true, is_array($this->_factory()->params()));
  }
  
  protected function _factory()
  {
    return new $this->_className();
  }
}
