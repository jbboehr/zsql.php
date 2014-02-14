<?php

class Delete_Test extends Common_Query_Test
{
  protected $_className = '\\zsql\\Delete';
  
  public function testTable_ContainsDatabase()
  {
    $query = new \zsql\Delete();
    $query->table('dbName.tableName');
    $this->assertEquals('DELETE FROM `dbName`.`tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('DELETE FROM `dbName`.`tableName`', $query->toString());
  }
  
  public function testTable_Expression()
  {
    $query = new \zsql\Delete();
    $query->table(new \zsql\Expression('`tableName` as `otherTableName`'));
    $this->assertEquals('DELETE FROM `tableName` as `otherTableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('DELETE FROM `tableName` as `otherTableName`', $query->toString());
  }
  
  public function testTable_String()
  {
    $query = new \zsql\Delete();
    $query->table('tableName');
    $this->assertEquals('DELETE FROM `tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('DELETE FROM `tableName`', $query->toString());
  }
  
  public function testWhere_KeyIsExpr()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where(new \zsql\Expression('columnName < NOW()'));
    $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
  }
  
  public function testWhere_KeyContainsQuestionMark()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('columnName > ?', 2);
    $this->assertEquals('DELETE FROM `tableName` WHERE columnName > ?', $query->toString());
    $this->assertEquals(array(2), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('DELETE FROM `tableName` WHERE columnName > 2', $query->toString());
  }
  
  public function testWhere_KeyContainsTable()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('tableName.columnName', 2);
    $this->assertEquals('DELETE FROM `tableName` WHERE `tableName`.`columnName` = ?', $query->toString());
    $this->assertEquals(array(2), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("DELETE FROM `tableName` WHERE `tableName`.`columnName` = 2", $query->toString());
  }
  
  public function testWhere_ValueIsExpression()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('columnName', new \zsql\Expression('NOW()'));
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = NOW()', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = NOW()', $query->toString());
  }
  
  public function testWhere_ValueIsString()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('columnName', 'value');
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("DELETE FROM `tableName` WHERE `columnName` = 'value'", $query->toString());
  }
  
  public function testWhere_ValueIsInteger()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('columnName', 2);
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array(2), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("DELETE FROM `tableName` WHERE `columnName` = 2", $query->toString());
  }
  
  public function testWhereIn()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->whereIn('columnName', array(2, 4, 6, 8));
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` IN (?, ?, ?, ?)', $query->toString());
    $this->assertEquals(array(2, 4, 6, 8), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("DELETE FROM `tableName` WHERE `columnName` IN (2, 4, 6, 8)", $query->toString());
  }
  
  public function testWhereExpr()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->whereExpr('columnName < NOW()');
    $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
  }
  
  public function testOrder_Asc()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->order('columnName', 'ASC');
    $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` ASC', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` ASC', $query->toString());
  }
  
  public function testOrder_Desc()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->order('columnName', 'DESC');
    $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` DESC', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` DESC', $query->toString());
  }
  
  public function testLimit_WithOffset()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->limit(10, 20);
    $this->assertEquals('DELETE FROM `tableName` LIMIT ?, ?', $query->toString());
    $this->assertEquals(array(20, 10), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("DELETE FROM `tableName` LIMIT 20, 10", $query->toString());
  }
  
  public function testLimit_WithoutOffset()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->limit(30);
    $this->assertEquals('DELETE FROM `tableName` LIMIT ?', $query->toString());
    $this->assertEquals(array(30), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("DELETE FROM `tableName` LIMIT 30", $query->toString());
  }
  
  public function testOffset()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->offset(10, 20);
    $this->assertEquals('DELETE FROM `tableName` LIMIT ?, ?', $query->toString());
    $this->assertEquals(array(10, 20), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("DELETE FROM `tableName` LIMIT 10, 20", $query->toString());
  }
  
  public function testABunchOfStuffTogether()
  {
    $query = new \zsql\Delete();
    $query->from('tableName')
        ->where('columnOne', 1324)
        ->where('columnTwo < ?', 9)
        ->where(new \zsql\Expression('LENGTH(columnThree) > 0'))
        ->whereExpr('columnFour IS NULL')
        ->whereIn('columnFive', array('red', 'blue', 'green'))
        ->limit(50, 100)
        ->order('columnSix', 'DESC');
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnOne` = ? && '
        . 'columnTwo < ? && LENGTH(columnThree) > 0 && ' 
        . 'columnFour IS NULL && '
        . '`columnFive` IN (?, ?, ?) ORDER BY `columnSix` DESC ' 
        . 'LIMIT ?, ?', $query->toString());
    $this->assertEquals(array(1324, 9, 'red', 'blue', 'green', 100, 50), 
        $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("DELETE FROM `tableName` WHERE `columnOne` = 1324 && "
        . "columnTwo < 9 && "
        . "LENGTH(columnThree) > 0 && "
        . "columnFour IS NULL && "
        . "`columnFive` IN ('red', 'blue', 'green') "
        . "ORDER BY `columnSix` DESC "
        . "LIMIT 100, 50", $query->toString());
  }
  
  public function test_interpolate_ThrowsException()
  {
    $query = new \zsql\Delete();
    $exception = null;
    try {
      $query->table('tableName')->where('a', 'b');
      $query->interpolation();
      $query->toString();
    } catch( Exception $e ) {
      $exception = $e;
    }
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function test_interpolate_ThrowsException2()
  {
    $query = new \zsql\Delete();
    $exception = null;
    try {
      $query->table('tableName')->where('a??', 'b');
      $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
      $query->toString();
    } catch( Exception $e ) {
      $exception = $e;
    }
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testQuery_WithInterpolation()
  {
    $expectedQuery = "DELETE FROM `tableName` WHERE `columnName` = 'value'";
    $testObject = $this;
    $callback = function($actualQuery)use($expectedQuery, $testObject) {
      $testObject->assertEquals($expectedQuery, $actualQuery);
      return $actualQuery;
    };
    $query = new \zsql\Delete($callback);
    $query->from('tableName')->where('columnName', 'value');
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals($expectedQuery, $query->query());
  }
  
  public function testQuery_WithoutInterpolation()
  {
    $expectedQuery = "DELETE FROM `tableName` WHERE `columnName` = ?";
    $expectedParams = array('value');
    $testObject = $this;
    $callback = function($actualQuery, $actualParams)use($expectedQuery, $expectedParams, $testObject) {
      $testObject->assertEquals($expectedQuery, $actualQuery);
      $testObject->assertEquals($expectedParams, $actualParams);
      return $actualQuery;
    };
    $query = new \zsql\Delete($callback);
    $query->from('tableName')->where('columnName', 'value');
    $this->assertEquals($expectedQuery, $query->query());
    $this->assertEquals($expectedParams, $query->params());
  }
}
