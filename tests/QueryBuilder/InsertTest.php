<?php

namespace zsql\Tests\QueryBuilder;

use zsql\Expression;
use zsql\QueryBuilder\Insert;

/**
 * Class InsertTest
 * @package zsql\Tests\QueryBuilder
 * @method Insert queryFactory queryFactory($arg = null)
 */
class InsertTest extends Common
{
    protected $className = 'zsql\\QueryBuilder\\Insert';

    public function testAfter()
    {
        $bit = null;
        $bit2 = null;
        $cb = function ($result) use (&$bit) {
            if( $result === 'fakeInsertId' ) {
                $bit = true;
            }
        };
        $queryCallback = function () use (&$bit2) {
            $bit2 = true;
            return 'fakeInsertId';
        };
        $query = $this->queryFactory($queryCallback);
        $query
            ->into('tableName')
            ->set('a3', 'b4')
            ->after($cb)
            ->query();
        $this->assertEquals($bit, true);
        $this->assertTrue($bit2, true);
    }

    public function testBefore()
    {
        $bit = null;
        $bit2 = null;
        $cb = function ($query) use (&$bit, &$bit2) {
            if( $query instanceof Insert && !$bit2 ) {
                $bit = true;
            }
        };
        $queryCallback = function () use (&$bit2) {
            $bit2 = true;
            return 'fakeInsertId';
        };
        $query = $this->queryFactory($queryCallback);
        $query
            ->into('tableName')
            ->set('a3', 'b4')
            ->before($cb)
            ->query();
        $this->assertEquals($bit, true);
        $this->assertTrue($bit2, true);
    }

    public function testClearValues()
    {
        $query = $this->queryFactory();
        $query
            ->into('tableName')
            ->set('a3', 'b4')
            ->clearValues()
            ->set('a1', 'b2');
        $query->toString(); // sigh
        $this->assertEquals(array('b2'), $query->params());
    }

    public function testDelayed()
    {
        $query = $this->queryFactory();
        $query
            ->into('tableName')
            ->delayed()
            ->set('columnName', 'value');
        $this->assertEquals('INSERT DELAYED INTO `tableName` SET `columnName` = ?', $query->toString());
        $this->assertEquals(array('value'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("INSERT DELAYED INTO `tableName` SET `columnName` = 'value'", $query->toString());
    }

    public function testDelayedFalse()
    {
        $query = $this->queryFactory();
        $query
            ->into('tableName')
            ->delayed(true)
            ->delayed(false)
            ->set('columnName', 'value');
        $this->assertEquals('INSERT INTO `tableName` SET `columnName` = ?', $query->toString());
        $this->assertEquals(array('value'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("INSERT INTO `tableName` SET `columnName` = 'value'", $query->toString());
    }

    public function testGet()
    {
        $query = $this->queryFactory();
        $query
            ->set('columnName', 'value');
        $this->assertEquals('value', $query->get('columnName'));
        $this->assertEquals(null, $query->get('otherColumnName'));
    }

    public function testIgnore()
    {
        $query = $this->queryFactory();
        $query
            ->into('tableName')
            ->ignore()
            ->set('columnName', 'value');
        $this->assertEquals('INSERT IGNORE INTO `tableName` SET `columnName` = ?', $query->toString());
        $this->assertEquals(array('value'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("INSERT IGNORE INTO `tableName` SET `columnName` = 'value'", $query->toString());
    }

    public function testIgnoreFalse()
    {
        $query = $this->queryFactory();
        $query
            ->into('tableName')
            ->ignore(true)
            ->ignore(false)
            ->set('columnName', 'value');
        $this->assertEquals('INSERT INTO `tableName` SET `columnName` = ?', $query->toString());
        $this->assertEquals(array('value'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("INSERT INTO `tableName` SET `columnName` = 'value'", $query->toString());
    }

    public function testOnDuplicateKeyUpdate()
    {
        $query = $this->queryFactory();
        $query
            ->into('tableName')
            ->set('columnName', 'value')
            ->onDuplicateKeyUpdate(array('a' => 'b'))
            ->onDuplicateKeyUpdate(new Expression('columnName = VALUE(columnName)'))
            ->onDuplicateKeyUpdate('c', 'd');
        $this->assertEquals('INSERT INTO `tableName` SET `columnName` = ? '
            . 'ON DUPLICATE KEY UPDATE '
            . '`a` = ? , columnName = VALUE(columnName) , `c` = ?', $query->toString());
        $this->assertEquals(array('value', 'b', 'd'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("INSERT INTO `tableName` SET `columnName` = 'value' "
            . 'ON DUPLICATE KEY UPDATE '
            . "`a` = 'b' , columnName = VALUE(columnName) , `c` = 'd'", $query->toString());
    }

    public function testReplace()
    {
        $query = $this->queryFactory();
        $query
            ->into('tableName')
            ->replace()
            ->set('columnName', 'value');
        $this->assertEquals('REPLACE INTO `tableName` SET `columnName` = ?', $query->toString());
        $this->assertEquals(array('value'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("REPLACE INTO `tableName` SET `columnName` = 'value'", $query->toString());
    }

    public function testReplaceFalse()
    {
        $query = $this->queryFactory();
        $query
            ->into('tableName')
            ->replace(true)
            ->replace(false)
            ->set('columnName', 'value');
        $this->assertEquals('INSERT INTO `tableName` SET `columnName` = ?', $query->toString());
        $this->assertEquals(array('value'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("INSERT INTO `tableName` SET `columnName` = 'value'", $query->toString());
    }

    public function testToStringThrowsExceptionWithNoTable()
    {
        $this->setExpectedException('zsql\\Exception\\LogicException');

        $query = $this->queryFactory();
        $query->toString();
    }

    public function testToStringThrowsExceptionWithNoValues()
    {
        $this->setExpectedException('zsql\\Exception\\LogicException');

        $query = $this->queryFactory();
        $query->into('tableName')->toString();
    }

    public function testABunchOfStuffTogether()
    {
        $query = $this->queryFactory();
        $query
            ->into('tableName')
            ->ignore()
            ->insert(array('a1' => 'b2')) // does not get ignored any more
            ->set(array('c3' => 'd4')) // does not get ignored any more
            ->values(array(
                'a' => 'b',
                'd' => 'e',
            ))
            ->set('f', 'g')
            ->value('h', new Expression('NOW()'))
            ->value(new Expression('z = SHA1(0)'))
        ;
        $this->assertEquals('INSERT IGNORE INTO `tableName` SET `a1` = ? , `c3` = ? , `a` = ? , `d` = ? , `f` = ? , `h` = NOW() , z = SHA1(0)', $query->toString());
        $this->assertEquals(array('b2', 'd4', 'b', 'e', 'g'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("INSERT IGNORE INTO `tableName` SET `a1` = 'b2' , `c3` = 'd4' , `a` = 'b' , `d` = 'e' , `f` = 'g' , `h` = NOW() , z = SHA1(0)", $query->toString());
    }

    public function testInterpolateThrowsException()
    {
        $this->setExpectedException('zsql\\Exception\\LogicException');

        $query = $this->queryFactory();
        $query->table('tableName')->set('a', 'b');
        $query->interpolation();
        $query->toString();
    }

    public function testInterpolateThrowsException2()
    {
        $this->setExpectedException('zsql\\Exception\\LogicException');

        $query = $this->queryFactory();
        $query->table('tableName')->set('a??', 'b');
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $query->toString();
    }

    public function testQueryWithInterpolation()
    {
        $expectedQuery = "INSERT INTO `tableName` SET `columnName` = 'value'";
        $testObject = $this;
        $callback = function ($actualQuery) use ($expectedQuery, $testObject) {
            $testObject->assertEquals($expectedQuery, $actualQuery);
            return $actualQuery;
        };
        $query = $this->queryFactory($callback);
        $query->into('tableName')->set('columnName', 'value');
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals($expectedQuery, $query->query());
    }

    public function testQueryWithoutInterpolation()
    {
        $expectedQuery = "INSERT INTO `tableName` SET `columnName` = ?";
        $expectedParams = array('value');
        $testObject = $this;
        $callback = function ($actualQuery, $actualParams) use ($expectedQuery, $expectedParams, $testObject) {
            $testObject->assertEquals($expectedQuery, $actualQuery);
            $testObject->assertEquals($expectedParams, $actualParams);
            return $actualQuery;
        };
        $query = $this->queryFactory($callback);
        $query->into('tableName')->set('columnName', 'value');
        $this->assertEquals($expectedQuery, $query->query());
        $this->assertEquals($expectedParams, $query->params());
    }
}
