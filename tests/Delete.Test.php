<?php

class Delete_Test extends Common_Test
{
  protected $_className = '\\zsql\\Delete';
  
  public function testTable_ContainsDatabase()
  {
    $query = new \zsql\Delete();
    $query->table('dbName.tableName');
    $this->assertEquals('DELETE FROM `dbName`.`tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testTable_Expression()
  {
    $query = new \zsql\Delete();
    $query->table(new \zsql\Expression('`tableName` as `otherTableName`'));
    $this->assertEquals('DELETE FROM `tableName` as `otherTableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testTable_String()
  {
    $query = new \zsql\Delete();
    $query->table('tableName');
    $this->assertEquals('DELETE FROM `tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testWhere_KeyIsExpr()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where(new \zsql\Expression('columnName < NOW()'));
    $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testWhere_KeyContainsQuestionMark()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('columnName > ?', 2);
    $this->assertEquals('DELETE FROM `tableName` WHERE columnName > ?', $query->toString());
    $this->assertEquals(array(2), $query->params());
  }
  
  public function testWhere_KeyContainsTable()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('tableName.columnName', 2);
    $this->assertEquals('DELETE FROM `tableName` WHERE `tableName`.`columnName` = ?', $query->toString());
    $this->assertEquals(array(2), $query->params());
  }
  
  public function testWhere_ValueIsExpression()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('columnName', new \zsql\Expression('NOW()'));
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = NOW()', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testWhere_ValueIsString()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('columnName', 'value');
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
  }
  
  public function testWhere_ValueIsInteger()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->where('columnName', 2);
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array(2), $query->params());
  }
  
  public function testWhereIn()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->whereIn('columnName', array(2, 4, 6, 8));
    $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` IN (?, ?, ?, ?)', $query->toString());
    $this->assertEquals(array(2, 4, 6, 8), $query->params());
  }
  
  public function testWhereExpr()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->whereExpr('columnName < NOW()');
    $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testOrder_Asc()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->order('columnName', 'ASC');
    $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` ASC', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testOrder_Desc()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->order('columnName', 'DESC');
    $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` DESC', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testLimit_WithOffset()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->limit(10, 20);
    $this->assertEquals('DELETE FROM `tableName` LIMIT ?, ?', $query->toString());
    $this->assertEquals(array(20, 10), $query->params());
  }
  
  public function testLimit_WithoutOffset()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->limit(30);
    $this->assertEquals('DELETE FROM `tableName` LIMIT ?', $query->toString());
    $this->assertEquals(array(30), $query->params());
  }
  
  public function testOffset()
  {
    $query = new \zsql\Delete();
    $query->table('tableName')
        ->offset(10, 20);
    $this->assertEquals('DELETE FROM `tableName` LIMIT ?, ?', $query->toString());
    $this->assertEquals(array(10, 20), $query->params());
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
  }
}
