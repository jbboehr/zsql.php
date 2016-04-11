<?php

namespace zsql\Tests;

use PDO;

use zsql\Adapter\PDOAdapter;
use zsql\Expression;

class PDOAdapterTest extends Common
{
//    public function testSetConnection(PDOAdapter $database)
//    {
//        $database1 = $this->databaseFactory();
//        $database2 = $this->databaseFactory();
//        $database1Connection = $database1->getConnection();
//        $database2Connection = $database2->getConnection();
//
//        $database1->setConnection($database2Connection);
//        $database2->setConnection();
//
//        $this->assertEquals($database2Connection, $database1->getConnection());
//        $this->assertNotEquals($database2->getConnection(), $database1Connection);
//
//        //$database1Connection->close();
//    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testGetConnection(PDOAdapter $database)
    {
        $this->assertInstanceOf('PDO', $database->getConnection());
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testGetQueryCount(PDOAdapter $database)
    {
        $this->assertEquals(0, $database->getQueryCount());

        $database->query('SELECT TRUE');

        $this->assertEquals(1, $database->getQueryCount());
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testSelect(PDOAdapter $database)
    {
        $query = $database->select();
        $this->assertInstanceOf('zsql\\QueryBuilder\\Select', $query);
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testSelectWithResultClass(PDOAdapter $database)
    {
        $result = $database->select()
            ->setResultClass('ArrayObject')
            ->from('fixture1')
            ->query()
            ->fetchRow();
        $this->assertInstanceOf('ArrayObject', $result);
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testInsert(PDOAdapter $database)
    {
        $query = $database->insert();
        $this->assertInstanceOf('zsql\\QueryBuilder\\Insert', $query);

        $prev = $database->getInsertId();

        $ret = $query->into('fixture2')
            ->set('dval', 0)
            ->query();

        // @todo fixme
        //if( $database->getDriverName() !== 'pdo_pgsql' ) {
            $this->assertTrue(is_numeric($ret));
            $this->assertNotEquals($ret, $prev);
        //}
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testUpdate(PDOAdapter $database)
    {
        $query = $database->update();
        $this->assertInstanceOf('zsql\\QueryBuilder\\Update', $query);

        $ret = $query->table('fixture2')
            ->set('dval', new Expression('dval + 1'))
            ->where('id', 2)
            ->query();

        $this->assertEquals(true, $ret);
        $this->assertEquals(1, $database->getAffectedRows());
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testDelete(PDOAdapter $database)
    {
        $query = $database->delete();
        $this->assertInstanceOf('zsql\\QueryBuilder\\Delete', $query);

        $id = $database->insert()
            ->into('fixture2')
            ->set('dval', 0)
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

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testQuery(PDOAdapter $database)
    {
        $result = $database->query('SELECT TRUE');
        $this->assertInstanceOf('zsql\\Result\\Result', $result);
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testQueryThrowsExceptionOnFailure(PDOAdapter $database)
    {
        $this->setExpectedException('zsql\\Exception');

        $database->query('SELECT foo FROM bar');
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testQueryNotSelect(PDOAdapter $database)
    {
        $result = $database->delete()
            ->from('fixture1')
            ->where('id', 234234)
            ->query();

        $this->assertNotSame(false, $result);
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testQuote(PDOAdapter $database)
    {
        $this->assertEquals('NULL', $database->quote(null));
        $this->assertEquals("'1'", $database->quote(true));
        $this->assertEquals("''", $database->quote(false));
        $this->assertEquals('"', $database->quote(new Expression('"')));
        $this->assertEquals("'100'", $database->quote(100));
        $this->assertEquals("'3.14'", $database->quote(3.14));
        $this->assertEquals("'blah'", $database->quote('blah'));
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testQueryWithLogger(PDOAdapter $database)
    {
        $logger = $this->getMock('Psr\Log\NullLogger', array('debug'));
        $logger->expects($this->once())
            ->method('debug');
        $database->setLogger($logger);
        $database->query('SELECT TRUE');
    }

    /**
     * @param PDOAdapter $database
     * @dataProvider adapterProvider
     */
    public function testQueryWithLoggerWithFailedQuery(PDOAdapter $database)
    {
        $this->setExpectedException('zsql\\Exception');
        $logger = $this->getMock('Psr\Log\NullLogger', array('debug', 'error'));
        $logger->expects($this->once())
            ->method('debug');
        $logger->expects($this->once())
            ->method('error');
        $database->setLogger($logger);
        $database->query('SELECT fakesomething');
    }

    public function adapterProvider()
    {
        if( defined('HHVM_VERSION') ) {
            $this->markTestSkipped('HHVM does not support PDO?');
        }

        $pdo1 = new PDOAdapter(new PDO(sprintf('mysql:host=%s;dbname=%s;', ZSQL_TEST_DATABASE_HOST, ZSQL_TEST_DATABASE_DBNAME),
            ZSQL_TEST_DATABASE_USERNAME, ZSQL_TEST_DATABASE_PASSWORD));
        $pdo2 = new PDOAdapter(new PDO(sprintf('pgsql:host=%s;dbname=%s;', ZSQL_TEST_DATABASE_HOST, ZSQL_TEST_DATABASE_DBNAME),
            ZSQL_TEST_DATABASE_USERNAME, ZSQL_TEST_DATABASE_PASSWORD));
        return array(array($pdo1), array($pdo2));
    }
}
