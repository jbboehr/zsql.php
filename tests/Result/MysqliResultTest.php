<?php

namespace zsql\Tests\Result;

use zsql\Result\Result;
use zsql\Result\MysqliResult;
use zsql\Tests\Common;

/**
 * Class ResultTest
 * @package zsql\Tests\Result
 */
class ResultTest extends Common
{
    public function testConstructor()
    {
        /** @var MysqliResult $result */
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $this->assertInstanceOf('mysqli_result', $result->getResult());
    }

    public function testFree()
    {
        /** @var MysqliResult $result */
        $this->setExpectedException('zsql\\Result\\Exception');
        
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();
        $result->free();

        $this->assertEquals(null, $result->getResult());
    }

    public function testGetSetResultClass()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $this->assertEquals('zsql\\Row\\DefaultRow', $result->getResultClass());
        $result->setResultClass('ArrayObject');
        $this->assertEquals('ArrayObject', $result->getResultClass());
    }

    public function testSetResultClassThrowExceptionInvalidClass()
    {
        $this->setExpectedException('zsql\\Result\\Exception');

        $result = new MysqliResult(null);
        $result->setResultClass('InvalidClassNameNoob');
    }

    public function testSetResultParams()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();
        $result->setResultClass('zsql\\Tests\\Fixture\\RowWithConstructor');

        $params = array('param1', 'param2');
        $result->setResultParams($params);

        $r = $result->fetchRow();
        $this->assertEquals($r->params, $params);
    }

    public function testGetResultMode()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $this->assertEquals(Result::FETCH_OBJECT, $result->getResultMode());
    }

    public function testSetResultMode()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $result->setResultMode(Result::FETCH_ASSOC);
        $this->assertEquals(Result::FETCH_ASSOC, $result->getResultMode());
    }

    public function testSetResultModeThrowsOnInvalidMode()
    {
        $this->setExpectedException('zsql\\Result\\Exception');
        
        $result = new MysqliResult(null);
        $result->setResultMode(9001);
    }

    public function testFetchRow()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $row = $result->fetchRow();

        $this->assertInstanceOf('zsql\\Row\\DefaultRow', $row);
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

        $row = $result->fetchRow(Result::FETCH_ASSOC);

        $this->assertEquals(true, is_array($row));
    }

    public function testFetchRowFetchModeColumn()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->columns('id')->query();

        $col = $result->fetchRow(Result::FETCH_COLUMN);

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

        $col = $result->fetchRow(Result::FETCH_COLUMN);

        $this->assertEquals(true, null === $col);
    }

    public function testFetchRowFetchModeNum()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->columns('id')->query();

        $row = $result->fetchRow(Result::FETCH_NUM);

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
            $this->assertInstanceOf('zsql\\Row\\DefaultRow', $row);
        }
    }

    public function testFetchAllFetchModeAssoc()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $rows = $result->fetchAll(Result::FETCH_ASSOC);

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

        $cols = $result->fetchAll(Result::FETCH_COLUMN);

        $this->assertCount($this->fixtureOneRowCount, $cols);

        foreach( $cols as $col ) {
            $this->assertEquals(true, is_scalar($col));
        }
    }

    public function testFetchAllFetchModeNum()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->query();

        $rows = $result->fetchAll(Result::FETCH_NUM);

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

        $rows = $result->setResultClass('ArrayObject')->fetchAll(Result::FETCH_OBJECT);

        $this->assertEquals(true, is_array($rows));

        foreach( $rows as $row ) {
            $this->assertInstanceOf('ArrayObject', $row);
        }
    }

    public function testFetchColumn()
    {
        $database = $this->databaseFactory();
        $result = $database->select()->from('fixture1')->columns('id')->query()->fetchColumn();

        $this->assertEquals(true, is_scalar($result));
    }
}
