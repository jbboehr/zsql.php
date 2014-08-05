<?php

class Update_Test extends Common_Query_Test
{
  protected $_className = '\\zsql\Update';
  
  public function testToString_ThrowsExceptionWithNoTable()
  {
    $query = new \zsql\Update();
    try {
      $query->toString();
      $this->assertTrue(false); // -_-
    } catch( Exception $e ) {
      $this->assertInstanceOf('\\zsql\\Exception', $e);
    }
  }
  
  public function testToString_ThrowsExceptionWithNoValues()
  {
    $query = new \zsql\Update();
    try {
      $query->table('tableName')->toString();
      $this->assertTrue(false); // -_-
    } catch( Exception $e ) {
      $this->assertInstanceOf('\\zsql\\Exception', $e);
    }
  }
  
  public function testUpdate()
  {
    $query = new \zsql\Update();
    $query
      ->update('tableName', array('columnName' => 'val'));
    $this->assertEquals('UPDATE `tableName` SET `columnName` = ?', $query->toString());
    $this->assertEquals(array('val'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("UPDATE `tableName` SET `columnName` = 'val'", $query->toString());
  }
  
  public function testABunchOfStuffTogether()
  {
    $query = new \zsql\Update();
    $query
      ->update('tableName')
      ->set(array('a3' => 'b4')) // does not get ignored any more
      ->values(array(
        'a' => 'b',
        'd' => 'e',
      ))
      ->set('f', 'g')
      ->value('h', new \zsql\Expression('NOW()'))
      ->value(new \zsql\Expression('z = SHA1(0)'))
      ->where('i', 'j')
      ->whereIn('k', array('l', 'm', 'n'))
      ->whereExpr('LENGTH(o) > 0')
      ;
    $this->assertEquals('UPDATE `tableName` SET `a3` = ? , `a` = ? , `d` = ? , `f` = ? , `h` = NOW() , z = SHA1(0) WHERE `i` = ? && `k` IN (?, ?, ?) && LENGTH(o) > 0', $query->toString());
    $this->assertEquals(array('b4', 'b', 'e', 'g', 'j', 'l', 'm', 'n'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("UPDATE `tableName` SET `a3` = 'b4' , `a` = 'b' , `d` = 'e' , `f` = 'g' , `h` = NOW() , z = SHA1(0) WHERE `i` = 'j' && `k` IN ('l', 'm', 'n') && LENGTH(o) > 0", $query->toString());
  }
  
  public function test_interpolate_ThrowsException()
  {
    $query = new \zsql\Update();
    $exception = null;
    try {
      $query->table('tableName')->set('a', 'b')->where('c', 'd');
      $query->interpolation();
      $query->toString();
    } catch( Exception $e ) {
      $exception = $e;
    }
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function test_interpolate_ThrowsException2()
  {
    $query = new \zsql\Update();
    $exception = null;
    try {
      $query->table('tableName')->set('a??', 'b')->where('c??', 'd');
      $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
      $query->toString();
    } catch( Exception $e ) {
      $exception = $e;
    }
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testQuery_WithInterpolation()
  {
    $expectedQuery = "UPDATE `tableName` SET `columnName` = 'value'";
    $testObject = $this;
    $callback = function($actualQuery)use($expectedQuery, $testObject) {
      $testObject->assertEquals($expectedQuery, $actualQuery);
      return $actualQuery;
    };
    $query = new \zsql\Update($callback);
    $query->table('tableName')->set('columnName', 'value');
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals($expectedQuery, $query->query());
  }
  
  public function testQuery_WithoutInterpolation()
  {
    $expectedQuery = "UPDATE `tableName` SET `columnName` = ?";
    $expectedParams = array('value');
    $testObject = $this;
    $callback = function($actualQuery, $actualParams)use($expectedQuery, $expectedParams, $testObject) {
      $testObject->assertEquals($expectedQuery, $actualQuery);
      $testObject->assertEquals($expectedParams, $actualParams);
      return $actualQuery;
    };
    $query = new \zsql\Update($callback);
    $query->table('tableName')->set('columnName', 'value');
    $this->assertEquals($expectedQuery, $query->query());
    $this->assertEquals($expectedParams, $query->params());
  }
}
