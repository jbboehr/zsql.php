<?php

class Result_Test extends Common_Test
{
  public function testConstructor()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $this->assertInstanceOf('\\mysqli_result', $result->getResult());
  }
  
  public function testFree()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    $result->free();
    
    $exception = null;
    try {
      $this->assertEquals(null, $result->getResult());
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testGetSetResultClass()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $this->assertEquals(null, $result->getResultClass());
    $result->setResultClass('ArrayObject');
    $this->assertEquals('ArrayObject', $result->getResultClass());
  }
  
  public function testSetResultClass_ThrowExceptionInvalidClass()
  {
    $result = new \zsql\Result(null);
    
    $exception = null;
    try {
      $result->setResultClass('InvalidClassNameNoob');
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testGetResultMode()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $this->assertEquals(\zsql\Result::FETCH_OBJECT, $result->getResultMode());
  }
  
  public function testSetResultMode()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $result->setResultMode(\zsql\Result::FETCH_ASSOC);
    $this->assertEquals(\zsql\Result::FETCH_ASSOC, $result->getResultMode());
  }
  
  public function testSetResultMode_ThrowsOnInvalidMode()
  {
    $result = new \zsql\Result(null);
    
    $exception = null;
    try {
      $result->setResultMode(9001);
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testFetchRow()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $row = $result->fetchRow();
    
    $this->assertInstanceOf('\\stdClass', $row);
  }
  
  public function testFetchRow_EmptyResult()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->where('id', 123)->query();
    
    $row = $result->fetchRow();
    
    $this->assertEquals(true, null === $row);
  }
  
  public function testFetchRow_FetchModeAssoc()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $row = $result->fetchRow(\zsql\Result::FETCH_ASSOC);
    
    $this->assertEquals(true, is_array($row));
  }
  
  public function testFetchRow_FetchModeColumn()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->columns('id')->query();
    
    $col = $result->fetchRow(\zsql\Result::FETCH_COLUMN);
    
    $this->assertEquals(true, is_scalar($col));
  }
  
  public function testFetchRow_FetchModeColumn_EmptyResult()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->columns('id')->where('id', 123)->query();
    
    $col = $result->fetchRow(\zsql\Result::FETCH_COLUMN);
    
    $this->assertEquals(true, null === $col);
  }
  
  public function testFetchRow_FetchModeNum()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->columns('id')->query();
    
    $row = $result->fetchRow(\zsql\Result::FETCH_NUM);
    
    foreach( $row as $k => $v ) {
      $this->assertEquals(true, is_int($k));
    }
  }
  
  public function testFetchAll()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $rows = $result->fetchAll();
    
    $this->assertCount($this->fixtureOneRowCount, $rows);
    
    foreach( $rows as $row ) {
      $this->assertInstanceOf('\\stdClass', $row);
    }
  }
  
  public function testFetchAll_FetchModeAssoc()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $rows = $result->fetchAll(\zsql\Result::FETCH_ASSOC);
    
    $this->assertEquals(true, is_array($rows));
    
    foreach( $rows as $row ) {
      $this->assertEquals(true, is_array($row));
      foreach( $row as $k => $v ) {
        $this->assertEquals(false, is_int($k));
      }
    }
  }
  
  public function testFetchAll_FetchModeColumn()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->columns('id')->query();
    
    $cols = $result->fetchAll(\zsql\Result::FETCH_COLUMN);
    
    $this->assertCount($this->fixtureOneRowCount, $cols);
    
    foreach( $cols as $col ) {
      $this->assertEquals(true, is_scalar($col));
    }
  }
  
  public function testFetchAll_FetchModeNum()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $rows = $result->fetchAll(\zsql\Result::FETCH_NUM);
    
    $this->assertCount($this->fixtureOneRowCount, $rows);
    
    foreach( $rows as $row ) {
      $this->assertEquals(true, is_array($row));
      foreach( $row as $k => $v ) {
        $this->assertEquals(true, is_int($k));
      }
    }
  }
  
  public function testFetchAll_FetchModeObject_WithResultClass()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->query();
    
    $rows = $result->setResultClass('\\ArrayObject')->fetchAll(\zsql\Result::FETCH_OBJECT);
    
    $this->assertEquals(true, is_array($rows));
    
    foreach( $rows as $row ) {
      $this->assertInstanceOf('\\ArrayObject', $row);
    }
  }
  
  public function testFetchColumn()
  {
    $database = $this->databaseFactory();
    $result = $database->select()->from('fixture1')->columns('id')->query()->fetchColumn();
    
    $this->assertEquals(true, is_scalar($result));
  }
  
  
}
