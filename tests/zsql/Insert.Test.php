<?php

class Insert_Test extends Common_Test
{
  protected $_className = '\\zsql\\Insert';
  
  public function testDelayed()
  {
    $query = new \zsql\Insert();
    $query
      ->into('tableName')
      ->delayed()
      ->set('columnName', 'value');
    $this->assertEquals('INSERT DELAYED INTO `tableName` SET `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("INSERT DELAYED INTO `tableName` SET `columnName` = 'value'", $query->toString());
  }
  
  public function testDelayed_False()
  {
    $query = new \zsql\Insert();
    $query
      ->into('tableName')
      ->delayed(true)
      ->delayed(false)
      ->set('columnName', 'value');
    $this->assertEquals('INSERT INTO `tableName` SET `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("INSERT INTO `tableName` SET `columnName` = 'value'", $query->toString());
  }
  
  public function testIgnore()
  {
    $query = new \zsql\Insert();
    $query
      ->into('tableName')
      ->ignore()
      ->set('columnName', 'value');
    $this->assertEquals('INSERT IGNORE INTO `tableName` SET `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("INSERT IGNORE INTO `tableName` SET `columnName` = 'value'", $query->toString());
  }
  
  public function testIgnore_False()
  {
    $query = new \zsql\Insert();
    $query
      ->into('tableName')
      ->ignore(true)
      ->ignore(false)
      ->set('columnName', 'value');
    $this->assertEquals('INSERT INTO `tableName` SET `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("INSERT INTO `tableName` SET `columnName` = 'value'", $query->toString());
  }
  
  public function testToString_ThrowsExceptionWithNoTable()
  {
    $query = new \zsql\Insert();
    try {
      $query->toString();
      $this->assertTrue(false); // -_-
    } catch( Exception $e ) {
      $this->assertInstanceOf('\\zsql\\Exception', $e);
    }
  }
  
  public function testToString_ThrowsExceptionWithNoValues()
  {
    $query = new \zsql\Insert();
    try {
      $query->into('tableName')->toString();
      $this->assertTrue(false); // -_-
    } catch( Exception $e ) {
      $this->assertInstanceOf('\\zsql\\Exception', $e);
    }
  }
  
  public function testABunchOfStuffTogether()
  {
    $query = new \zsql\Insert();
    $query
      ->into('tableName')
      ->ignore()
      ->insert(array('1' => '2')) // gets ignored
      ->set(array('3' => '4')) // gets ignored
      ->values(array(
        'a' => 'b',
        'd' => 'e',
      ))
      ->set('f', 'g')
      ->value('h', new \zsql\Expression('NOW()'))
      ->value(new \zsql\Expression('z = SHA1(0)'))
      ;
    $this->assertEquals('INSERT IGNORE INTO `tableName` SET `a` = ? , ' 
        . '`d` = ? , `f` = ? , `h` = NOW() , z = SHA1(0)', $query->toString());
    $this->assertEquals(array('b', 'e', 'g'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("INSERT IGNORE INTO `tableName` SET "
        . "`a` = 'b' , `d` = 'e' , `f` = 'g' , `h` = NOW() , z = SHA1(0)", $query->toString());
  }
}
