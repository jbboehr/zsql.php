<?php

class Common_Test extends PHPUnit_Framework_TestCase
{
  protected $fixtureOneRowCount = 2;
  
  protected $useVerboseErrorHandler = false;
  
  public function __construct($name = NULL, array $data = array(), $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    
    if( $this->useVerboseErrorHandler ) {
      $this->setVerboseErrorHandler();
    }
  }
  
  protected function setVerboseErrorHandler() 
  {
    $handler = function($errorNumber, $errorString, $errorFile, $errorLine) {
        echo "ERROR INFO\nMessage: $errorString\nFile: $errorFile\nLine: $errorLine\n";
    };
    set_error_handler($handler);        
  }
  
  protected function fixtureModelOneFactory()
  {
    return new FixtureModelOne($this->databaseFactory());
  }
  
  protected function databaseFactory()
  {
    $mysql = new \mysqli();
    $mysql->connect('localhost', 'zsql', 'nopass', 'zsql');
    return new \zsql\Database($mysql);
  }
  
  public function getReflectedPropertyValue($class, $propertyName)
  {
    $reflectedClass = new ReflectionClass($class);
    $property = $reflectedClass->getProperty($propertyName);
    $property->setAccessible(true);
 
    return $property->getValue($class);
}
}

class Common_Query_Test extends Common_Test
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
  
  public function testMagicToString_Fails()
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
  
  public function testQuery_ThrowsException()
  {
    $query = $this->_factory();
    $exception = null;
    try {
      $query->query();
    } catch( Exception $e ) {
      $exception = $e;
    }
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testSetQuoteCallback_ThrowsException()
  {
    $query = $this->_factory();
    $exception = null;
    try {
      $query->setQuoteCallback(false);
    } catch( Exception $e ) {
      $exception = $e;
    }
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testInvalidConstructionArgThrowsException()
  {
    $exception = null;
    try {
      $query = new $this->_className('blah');
    } catch( Exception $e ) {
      $exception = $e;
    }
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  protected function _factory()
  {
    return new $this->_className();
  }
  
  protected function _getQuoteCallback()
  {
    // This is not a real quote function, just for testing
    return function($string) {
      if( is_int($string) || is_double($string) ) {
        return $string;
      } else if( is_null($string) ) {
        return 'NULL';
      } else if( is_bool($string) ) {
        return $string ? 'TRUE' : 'FALSE';
      } else {
        return "'" . addslashes($string) . "'";
      }
    };
  }
}

class FixtureModelOne extends \zsql\Model
{
  protected $tableName = 'fixture1';
  
  protected $primaryKey = 'id';
}

class FixtureModelWithResultClass extends \zsql\Model
{
    protected $tableName = 'fixture1';
    
    protected $resultClass = 'FixtureResult';
    
    protected $primaryKey = 'id';
}

class FixtureModelWithoutTableOrPrimaryKey extends \zsql\Model
{
  
}

class FixtureResult extends \stdClass {
    
}
