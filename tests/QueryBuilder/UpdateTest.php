<?php

namespace zsql\Tests\QueryBuilder;

use zsql\Expression;
use zsql\QueryBuilder\Query;
use zsql\QueryBuilder\Update;

/**
 * Class UpdateTest
 * @package zsql\Tests\QueryBuilder
 * @method Update queryFactory queryFactory($arg = null)
 */
class UpdateTest extends Common
{
    protected $className = 'zsql\\QueryBuilder\\Update';

    public function testAfter()
    {
        $bit = null;
        $bit2 = null;
        $cb = function ($result) use (&$bit) {
            if( $result === 23 ) {
                $bit = true;
            }
        };
        $queryCallback = function () use (&$bit2) {
            $bit2 = true;
            return 23;
        };
        $query = $this->queryFactory($queryCallback);
        $query
            ->table('tableName')
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
            if( $query instanceof Query && !$bit2 ) {
                $bit = true;
            }
        };
        $queryCallback = function () use (&$bit2) {
            $bit2 = true;
            return 'fakeInsertId';
        };
        $query = $this->queryFactory($queryCallback);
        $query
            ->table('tableName')
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
            ->update('tableName')
            ->set('a3', 'b4')
            ->clearValues()
            ->set('a1', 'b2');
        $query->toString(); // sigh
        $this->assertEquals(array('b2'), $query->params());
    }

    public function testGet()
    {
        $query = $this->queryFactory();
        $query
            ->set('columnName', 'value');
        $this->assertEquals('value', $query->get('columnName'));
        $this->assertEquals(null, $query->get('otherColumnName'));
    }

    public function testGetWhere()
    {
        $query = $this->queryFactory();
        $query
            ->set('columnName', 'value')
            ->where('otherColumnName', 'otherValue');
        $this->assertEquals('otherValue', $query->getWhere('otherColumnName'));
        $this->assertEquals(null, $query->getWhere('columnName'));
    }

    public function testToStringThrowsExceptionWithNoTable()
    {
        $this->setExpectedException('zsql\\QueryBuilder\\Exception');
        
        $query = $this->queryFactory();
        $query->toString();
    }

    public function testToStringThrowsExceptionWithNoValues()
    {
        $this->setExpectedException('zsql\\QueryBuilder\\Exception');
        
        $query = $this->queryFactory();
        $query->table('tableName')->toString();
    }

    public function testUpdate()
    {
        $query = $this->queryFactory();
        $query
            ->update('tableName', array('columnName' => 'val'));
        $this->assertEquals('UPDATE `tableName` SET `columnName` = ?', $query->toString());
        $this->assertEquals(array('val'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("UPDATE `tableName` SET `columnName` = 'val'", $query->toString());
    }

    public function testABunchOfStuffTogether()
    {
        $query = $this->queryFactory();
        $query
            ->update('tableName')
            ->set(array('a3' => 'b4')) // does not get ignored any more
            ->values(array(
                'a' => 'b',
                'd' => 'e',
            ))
            ->set('f', 'g')
            ->value('h', new Expression('NOW()'))
            ->value(new Expression('z = SHA1(0)'))
            ->where('i', 'j')
            ->whereIn('k', array('l', 'm', 'n'))
            ->whereExpr('LENGTH(o) > 0')
        ;
        $this->assertEquals('UPDATE `tableName` SET `a3` = ? , `a` = ? , `d` = ? , `f` = ? , `h` = NOW() , z = SHA1(0) WHERE `i` = ? && `k` IN (?, ?, ?) && LENGTH(o) > 0', $query->toString());
        $this->assertEquals(array('b4', 'b', 'e', 'g', 'j', 'l', 'm', 'n'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("UPDATE `tableName` SET `a3` = 'b4' , `a` = 'b' , `d` = 'e' , `f` = 'g' , `h` = NOW() , z = SHA1(0) WHERE `i` = 'j' && `k` IN ('l', 'm', 'n') && LENGTH(o) > 0", $query->toString());
    }

    public function testInterpolateThrowsException()
    {
        $this->setExpectedException('zsql\\QueryBuilder\\Exception');
        
        $query = $this->queryFactory();
        $query->table('tableName')->set('a', 'b')->where('c', 'd');
        $query->interpolation();
        $query->toString();
    }

    public function testInterpolateThrowsException2()
    {
        $this->setExpectedException('zsql\\QueryBuilder\\Exception');
        
        $query = $this->queryFactory();
        $query->table('tableName')->set('a??', 'b')->where('c??', 'd');
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $query->toString();
    }

    public function testQueryWithInterpolation()
    {
        $expectedQuery = "UPDATE `tableName` SET `columnName` = 'value'";
        $testObject = $this;
        $callback = function ($actualQuery) use ($expectedQuery, $testObject) {
            $testObject->assertEquals($expectedQuery, $actualQuery);
            return $actualQuery;
        };
        $query = $this->queryFactory($callback);
        $query->table('tableName')->set('columnName', 'value');
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals($expectedQuery, $query->query());
    }

    public function testQueryWithoutInterpolation()
    {
        $expectedQuery = "UPDATE `tableName` SET `columnName` = ?";
        $expectedParams = array('value');
        $testObject = $this;
        $callback = function ($actualQuery, $actualParams) use ($expectedQuery, $expectedParams, $testObject) {
            $testObject->assertEquals($expectedQuery, $actualQuery);
            $testObject->assertEquals($expectedParams, $actualParams);
            return $actualQuery;
        };
        $query = $this->queryFactory($callback);
        $query->table('tableName')->set('columnName', 'value');
        $this->assertEquals($expectedQuery, $query->query());
        $this->assertEquals($expectedParams, $query->params());
    }
}
