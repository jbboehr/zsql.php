<?php

class Update_Test extends Common_Test
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
  }
  
  public function testABunchOfStuffTogether()
  {
    $query = new \zsql\Update();
    $query
      ->update('tableName')
      ->set(array('3' => '4')) // gets ignored
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
    $this->assertEquals('UPDATE `tableName` SET `a` = ? , ' 
        . '`d` = ? , `f` = ? , `h` = NOW() , z = SHA1(0) ' 
        . 'WHERE `i` = ? && `k` IN (?, ?, ?) && LENGTH(o) > 0', $query->toString());
    $this->assertEquals(array('b', 'e', 'g', 'j', 'l', 'm', 'n'), $query->params());
  }
}
