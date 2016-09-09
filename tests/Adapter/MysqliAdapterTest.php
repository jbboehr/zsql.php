<?php

namespace zsql\Tests;

use Psr\Log\NullLogger;
use zsql\Adapter\Exception;
use zsql\Adapter\MysqliAdapter;
use zsql\Expression;

class MysqliAdapterTest extends Common
{
    public function testConstructInvalid()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new MysqliAdapter('foo');
    }

    public function testGetConnection()
    {
        $database = $this->createMysqliAdapter();

        $this->assertInstanceOf('mysqli', $database->getConnection());
    }

    public function testSetConnection()
    {
        $database1 = $this->createMysqliAdapter();
        $database2 = $this->createMysqliAdapter();
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
        $database = $this->createMysqliAdapter();

        $this->assertEquals(0, $database->getQueryCount());

        $database->query('SELECT TRUE');

        $this->assertEquals(1, $database->getQueryCount());
    }

    public function testSelect()
    {
        $database = $this->createMysqliAdapter();

        $query = $database->select();
        $this->assertInstanceOf('zsql\\QueryBuilder\\Select', $query);
    }

    public function testSelectWithResultClass()
    {
        $database = $this->createMysqliAdapter();

        $result = $database->select()
            ->setResultClass('ArrayObject')
            ->from('fixture1')
            ->query()
            ->fetchRow();
        $this->assertInstanceOf('ArrayObject', $result);
    }

    public function testInsert()
    {
        $database = $this->createMysqliAdapter();

        $query = $database->insert();
        $this->assertInstanceOf('zsql\\QueryBuilder\\Insert', $query);

        $prev = $database->getInsertId();

        $ret = $query->into('fixture2')->set('double', 0)->query();

        $this->assertEquals(true, $ret);
        $this->assertNotEquals($ret, $prev);
    }

    public function testUpdate()
    {
        $database = $this->createMysqliAdapter();

        $query = $database->update();
        $this->assertInstanceOf('zsql\\QueryBuilder\\Update', $query);

        $ret = $query->table('fixture2')
            ->set('double', new Expression('`double` + 1'))
            ->where('id', 2)
            ->query();

        $this->assertEquals(true, $ret);
        $this->assertEquals(1, $database->getAffectedRows());
    }

    public function testDelete()
    {
        $database = $this->createMysqliAdapter();

        $query = $database->delete();
        $this->assertInstanceOf('zsql\\QueryBuilder\\Delete', $query);


        $id = $database->insert()
            ->into('fixture2')
            ->set('double', 0)
            ->query();
        $idAlt = $database->getInsertId();

        $ret = $query->table('fixture2')
            ->where('id', $id)
            ->query();

        $this->assertEquals(true, $ret);
        $this->assertEquals(1, $database->getAffectedRows());
        $this->assertNotEmpty($id);
        $this->assertEquals($id, $idAlt);
    }

    public function testQuery()
    {
        $database = $this->createMysqliAdapter();

        $result = $database->query('SELECT TRUE');
        $this->assertInstanceOf('zsql\\Result\\Result', $result);
    }

    public function testQueryThrowsExceptionOnFailure()
    {
        $this->setExpectedException('zsql\\Exception');

        $database = $this->createMysqliAdapter();
        $database->query('SELECT foo FROM bar');
    }

    public function testQueryNotSelect()
    {
        $database = $this->createMysqliAdapter();

        $result = $database->query('DELETE FROM fixture1 WHERE id = 234234');
        $this->assertEquals(true, $result);
    }

    public function testQuote()
    {
        $database = $this->createMysqliAdapter();

        $this->assertEquals('NULL', $database->quote(null));
        $this->assertEquals('1', $database->quote(true));
        $this->assertEquals('0', $database->quote(false));
        $this->assertEquals('"', $database->quote(new Expression('"')));
        $this->assertEquals('100', $database->quote(100));
        $this->assertEquals('3.14', $database->quote(3.14));
        $this->assertEquals("'blah'", $database->quote('blah'));
    }

    public function testQueryWithLogger()
    {
        $database = $this->createMysqliAdapter();
        $logger = $this->getMock('Psr\Log\NullLogger', array('debug'));
        $logger->expects($this->once())
            ->method('debug');
        $database->setLogger($logger);
        $database->query('SELECT TRUE');
    }

    public function testQueryWithLoggerWithFailedQuery()
    {
        $this->setExpectedException('zsql\\Exception');
        $database = $this->createMysqliAdapter();
        $logger = $this->getMock('Psr\Log\NullLogger', array('debug', 'error'));
        $logger->expects($this->once())
            ->method('debug');
        $logger->expects($this->once())
            ->method('error');
        $database->setLogger($logger);
        $database->query('SELECT fakesomething');
    }

    public function testReconnect()
    {
        $database = new MysqliAdapter($this->createMysqliFactory());

        $mysqli = $database->getConnection();

        // Please kill yourself
        $mysqli->kill($mysqli->thread_id);

        $this->assertEquals(true, $database->query('SELECT TRUE')->fetchColumn());
    }

    public function testReconnect2()
    {
        if( !getenv('ZSQL_LONG_TESTS') ) {
            $this->markTestSkipped();
        }

        //$database = new MysqliAdapter($this->createMysqliFactory());
        $database = $this->createMysqliAdapter();
        $database->setConnectionFactory($this->createMysqliFactory());

        $database->query('SET @@session.wait_timeout=1');

        $mysqli = $database->getConnection();
        sleep(2);

        $result = @$database->query('SELECT TRUE');
        $this->assertEquals(true, $result->fetchColumn());
    }
}
