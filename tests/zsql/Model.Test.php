<?php

class Model_Test extends Common_Test
{
  public function testFind()
  {
    $model = $this->fixtureModelOneFactory();
    
    $row = $model->find(1);
    
    $this->assertInstanceOf('\\stdClass', $row);
  }
  
  public function testFind_ThrowsWithNoPrimaryKey()
  {
    $model = new FixtureModelWithoutTableOrPrimaryKey($this->databaseFactory());
    
    $exception = null;
    try {
      $model->find(1);
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testFindMany()
  {
    $model = $this->fixtureModelOneFactory();
    
    $rows = $model->findMany(array(1, 2));
    
    $this->assertCount(2, $rows);
  }
  
  public function testFindMany_ThrowsWithNoPrimaryKey()
  {
    $model = new FixtureModelWithoutTableOrPrimaryKey($this->databaseFactory());
    
    $exception = null;
    try {
      $model->findMany(array(1));
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testGetDatabase()
  {
    $model = $this->fixtureModelOneFactory();
    
    $this->assertInstanceOf('\\zsql\\Database', $model->getDatabase());
  }
  
  public function testSetDatabase()
  {
    $model = $this->fixtureModelOneFactory();
    
    $database1 = $model->getDatabase();
    $database2 = $this->databaseFactory();
    
    $model->setDatabase($database2);
    
    $this->assertEquals(true, $database2 === $model->getDatabase());
    $this->assertEquals(false, $database1 === $model->getDatabase());
  }
  
  public function testGetTableName()
  {
    $model = $this->fixtureModelOneFactory();
    
    $this->assertEquals('fixture1', $model->getTableName());
  }
  
  public function testSetTableName()
  {
    $model = $this->fixtureModelOneFactory();
    $original = $model->getTableName();
    $expected = 'blah';
    
    $model->setTableName($expected);
    
    $this->assertEquals($expected, $model->getTableName());
    $this->assertNotEquals($original, $model->getTableName());
  }
  
  public function testGetPrimaryKey()
  {
    $model = $this->fixtureModelOneFactory();
    
    $this->assertEquals('id', $model->getPrimaryKey());
  }
  
  public function testSetPrimaryKey()
  {
    $model = $this->fixtureModelOneFactory();
    $original = $model->getPrimaryKey();
    $expected = 'double';
    
    $model->setPrimaryKey($expected);
    
    $this->assertEquals($expected, $model->getPrimaryKey());
    $this->assertNotEquals($original, $model->getPrimaryKey());
  }
  
  public function testSelect()
  {
    $model = $this->fixtureModelOneFactory();
    $class = '\\zsql\\Select';
    $query = $model->select();
    $string = (string) $query;
    
    $this->assertInstanceOf($class, $query);
    $this->assertContains($model->getTableName(), $string);
    $this->assertInstanceOf('\\zsql\\Database', $this->getReflectedPropertyValue($query, 'database'));
  }
  
  public function testSelect_ThrowsWithNoTable()
  {
    $model = new FixtureModelWithoutTableOrPrimaryKey($this->databaseFactory());
    
    $exception = null;
    try {
      $model->select();
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testInsert()
  {
    $model = $this->fixtureModelOneFactory();
    $class = '\\zsql\\Insert';
    $query = $model->insert()->value('double', '2');
    $string = (string) $query;
    
    $this->assertInstanceOf($class, $query);
    $this->assertContains($model->getTableName(), $string);
    $this->assertInstanceOf('\\zsql\\Database', $this->getReflectedPropertyValue($query, 'database'));
  }
  
  public function testInsert_ThrowsWithNoTable()
  {
    $model = new FixtureModelWithoutTableOrPrimaryKey($this->databaseFactory());
    
    $exception = null;
    try {
      $model->insert();
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testUpdate()
  {
    $model = $this->fixtureModelOneFactory();
    $class = '\\zsql\\Update';
    $query = $model->update()->value('double', '2')->where('id', 54);
    $string = (string) $query;
    
    $this->assertInstanceOf($class, $query);
    $this->assertContains($model->getTableName(), $string);
    $this->assertInstanceOf('\\zsql\\Database', $this->getReflectedPropertyValue($query, 'database'));
  }
  
  public function testUpdate_ThrowsWithNoTable()
  {
    $model = new FixtureModelWithoutTableOrPrimaryKey($this->databaseFactory());
    
    $exception = null;
    try {
      $model->update();
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testDelete()
  {
    $model = $this->fixtureModelOneFactory();
    $class = '\\zsql\\Delete';
    $query = $model->delete()->where('id', 54);
    $string = (string) $query;
    
    $this->assertInstanceOf($class, $query);
    $this->assertContains($model->getTableName(), $string);
    $this->assertInstanceOf('\\zsql\\Database', $this->getReflectedPropertyValue($query, 'database'));
  }
  
  public function testDelete_ThrowsWithNoTable()
  {
    $model = new FixtureModelWithoutTableOrPrimaryKey($this->databaseFactory());
    
    $exception = null;
    try {
      $model->delete();
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
}
