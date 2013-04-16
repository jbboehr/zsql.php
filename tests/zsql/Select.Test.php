<?php

class Select_Test extends Common_Test
{
  protected $_className = '\\zsql\\Select';
  
  public function testColumns_Expression()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->columns(new \zsql\Expression('SUM(number)'));
    $this->assertEquals('SELECT SUM(number) FROM `tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testColumns_String()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->columns('columnName');
    $this->assertEquals('SELECT `columnName` FROM `tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testColumns_InvalidValueThrowsException()
  {
    $query = new \zsql\Select();
    try {
      $query->columns(false);
      $this->assertTrue(false); // -_-
    } catch( Exception $e ) {
      $this->assertInstanceOf('\\zsql\\Exception', $e);
    }
  }
  
  public function testDistinct()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->distinct()
      ->where('columnName', 'value');
    $this->assertEquals('SELECT DISTINCT * FROM `tableName` WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
  }
  
  public function testDistinct_False()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->distinct(true)
      ->distinct(false)
      ->where('columnName', 'value');
    $this->assertEquals('SELECT * FROM `tableName` WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
  }
  
  public function testFrom_WithColumns()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName', array('a', 'b', 'c'));
    $this->assertEquals('SELECT `a`, `b`, `c` FROM `tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testGroup()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->where('a', 'b')
      ->group('id');
    $this->assertEquals('SELECT * FROM `tableName` ' 
        . 'WHERE `a` = ? GROUP BY `id`', $query->toString());
    $this->assertEquals(array('b'), $query->params());
  }
  
  public function testHint()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->where('columnOne', 'a')
      ->where('columnTwo', 'b')
      ->hint('columnTwo', 'FORCE');
    $this->assertEquals('SELECT * FROM `tableName` FORCE INDEX (`columnTwo`) ' 
        . 'WHERE `columnOne` = ? && `columnTwo` = ?', $query->toString());
    $this->assertEquals(array('a', 'b'), $query->params());
  }
  
  public function testHint_Array()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->where('columnOne', 'a')
      ->where('columnTwo', 'b')
      ->hint(array('columnTwo', 'columnThree'), 'IGNORE');
    $this->assertEquals('SELECT * FROM `tableName` IGNORE INDEX (`columnTwo`, `columnThree`) ' 
        . 'WHERE `columnOne` = ? && `columnTwo` = ?', $query->toString());
    $this->assertEquals(array('a', 'b'), $query->params());
  }
  
  public function testSelect()
  {
    $query = new \zsql\Select();
    $query->select('columnName')
        ->from('tableName')
        ->where('columnName', 'columnValue');
    $this->assertEquals('SELECT `columnName` FROM `tableName` ' 
        . 'WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array('columnValue'), $query->params());
  }
  
  public function testWhereIn_Empty()
  {
    $query = new \zsql\Select();
    $query->select('columnName')
        ->from('tableName')
        ->whereIn('columnName', array());
    $this->assertEquals('SELECT `columnName` FROM `tableName` ' 
        . 'WHERE FALSE', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testWhereIn_String()
  {
    $query = new \zsql\Select();
    $query->select('columnName')
        ->from('tableName')
        ->whereIn('columnName', 'columnValue');
    $this->assertEquals('SELECT `columnName` FROM `tableName` ' 
        . 'WHERE `columnName` IN (?)', $query->toString());
    $this->assertEquals(array('columnValue'), $query->params());
  }
}
