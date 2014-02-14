<?php

class Database_Test extends Common_Test
{
  public function testGetConnection()
  {
    $database = $this->databaseFactory();
    
    $this->assertInstanceOf('\\mysqli', $database->getConnection());
  }
  
  public function testSetConnection()
  {
    $database1 = $this->databaseFactory();
    $database2 = $this->databaseFactory();
    $database1Connection = $database1->getConnection();
    $database2Connection = $database2->getConnection();
    
    $database1->setConnection($database2Connection);
    $database2->setConnection();
    
    $this->assertEquals($database2Connection, $database1->getConnection());
    $this->assertNotEquals($database2->getConnection(), $database1Connection);
    
    $database1Connection->close();
  }
  
  public function testGetQueryCount()
  {
    $database = $this->databaseFactory();
    
    $this->assertEquals(0, $database->getQueryCount());
    
    $database->query('SELECT TRUE');
    
    $this->assertEquals(1, $database->getQueryCount());
  }
  
  public function testSelect()
  {
    $database = $this->databaseFactory();
    
    $query = $database->select();
    $this->assertInstanceOf('\\zsql\\Select', $query);
  }
  
  public function testSelect_WithResultClass()
  {
    
    $database = $this->databaseFactory();
    
    $result = $database->select()->setResultClass('\\ArrayObject')->from('fixture1')->query()->fetchRow();
    $this->assertInstanceOf('\\ArrayObject', $result);
  }
  
  public function testInsert()
  {
    $database = $this->databaseFactory();
    
    $query = $database->insert();
    $this->assertInstanceOf('\\zsql\\Insert', $query);
    
    $prev = $database->getConnection()->insert_id;
    
    $ret = $query->into('fixture2')->set('double', 0)->query();
    
    $this->assertEquals(true, $ret);
    $this->assertNotEquals($ret, $prev);
  }
  
  public function testUpdate()
  {
    $database = $this->databaseFactory();
    
    $query = $database->update();
    $this->assertInstanceOf('\\zsql\\Update', $query);
    
    $ret = $query->table('fixture2')
            ->set('double', new \zsql\Expression('`double` + 1'))
            ->where('id', 2)
            ->query();
    
    $this->assertEquals(true, $ret);
    $this->assertEquals(1, $database->getConnection()->affected_rows);
  }
  
  public function testDelete()
  {
    $database = $this->databaseFactory();
    
    $query = $database->delete();
    $this->assertInstanceOf('\\zsql\\Delete', $query);
    
    
    $id = $database->insert()
            ->into('fixture2')
            ->set('double', 0)
            ->query();
    $idAlt = $database->getInsertId();
    
    $ret = $query->table('fixture2')
            ->where('id', $id)
            ->query();
    
    $this->assertEquals(true, $ret);
    $this->assertEquals(1, $database->getConnection()->affected_rows);
    $this->assertNotEmpty($id);
    $this->assertEquals($id, $idAlt);
  }
  
  public function testQuery()
  {
    $database = $this->databaseFactory();
    
    $result = $database->query('SELECT TRUE');
    $this->assertInstanceOf('\\zsql\\Result', $result);
  }
  
  public function testQuery_ThrowsExceptionOnFailure()
  {
    $database = $this->databaseFactory();
    
    
    $exception = null;
    $result = null;
    try {
      $result = $database->query('SELECT foo FROM bar');
    } catch( \Exception $e ) {
      $exception = $e;
    }
    
    $this->assertEquals(null, $result);
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testQuery_NotSelect()
  {
    $database = $this->databaseFactory();
    
    $result = $database->query('DELETE FROM fixture1 WHERE id = 234234');
    $this->assertEquals(null, $result);
  }
  
  public function testQuote()
  {
    $database = $this->databaseFactory();
    
    $this->assertEquals('NULL', $database->quote(null));
    $this->assertEquals('1', $database->quote(true));
    $this->assertEquals('0', $database->quote(false));
    $this->assertEquals('"', $database->quote(new \zsql\Expression('"')));
    $this->assertEquals('100', $database->quote(100));
    $this->assertEquals('3.14', $database->quote(3.14));
    $this->assertEquals("'blah'", $database->quote('blah'));
  }
}
