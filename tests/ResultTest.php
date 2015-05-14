<?php

namespace zsql\Tests;

class ResultTest extends Common
{
    public function testConstructor()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $this->assertInstanceOf('\\mysqli_result', $result->getResult());
    }

    public function testFree()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();
        $result->free();

        $this->assertEquals(null, $result->getResult());
    }

    public function testGetSetResultClass()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $this->assertEquals(null, $result->getResultClass());
        $result->setResultClass('ArrayObject');
        $this->assertEquals('ArrayObject', $result->getResultClass());
    }

    public function testSetResultClassThrowExceptionInvalidClass()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $result = new \zsql\Result(null);
        $result->setResultClass('InvalidClassNameNoob');
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

    public function testSetResultModeThrowsOnInvalidMode()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $result = new \zsql\Result(null);
        $result->setResultMode(9001);
    }

    public function testFetchRow()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $row = $result->fetchRow();

        $this->assertInstanceOf('\\stdClass', $row);
    }

    public function testFetchRowEmptyResult()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->where('id', 123)->query();

        $row = $result->fetchRow();

        $this->assertEquals(true, null === $row);
    }

    public function testFetchRowFetchModeAssoc()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $row = $result->fetchRow(\zsql\Result::FETCH_ASSOC);

        $this->assertEquals(true, is_array($row));
    }

    public function testFetchRowFetchModeColumn()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->columns('id')->query();

        $col = $result->fetchRow(\zsql\Result::FETCH_COLUMN);

        $this->assertEquals(true, is_scalar($col));
    }

    public function testFetchRowFetchModeColumnEmptyResult()
    {
        $database = $this->databaseFactory();
        $result = $database->select()
            ->from('fixture1')
            ->columns('id')
            ->where('id', 123)
            ->query();

        $col = $result->fetchRow(\zsql\Result::FETCH_COLUMN);

        $this->assertEquals(true, null === $col);
    }

    public function testFetchRowFetchModeNum()
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

    public function testFetchAllFetchModeAssoc()
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

    public function testFetchAllFetchModeColumn()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->columns('id')->query();

        $cols = $result->fetchAll(\zsql\Result::FETCH_COLUMN);

        $this->assertCount($this->fixtureOneRowCount, $cols);

        foreach( $cols as $col ) {
            $this->assertEquals(true, is_scalar($col));
        }
    }

    public function testFetchAllFetchModeNum()
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

    public function testFetchAllFetchModeObjectWithResultClass()
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
