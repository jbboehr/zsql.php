<?php

namespace zsql\Tests\QueryBuilder;

use zsql\Expression;
use zsql\QueryBuilder\Delete;

/**
 * Class DeleteTest
 * @package zsql\Tests\QueryBuilder
 * @method Delete queryFactory queryFactory($arg = null)
 */
class DeleteTest extends Common
{
    protected $className = 'zsql\\QueryBuilder\\Delete';

    public function testTableContainsDatabase()
    {
        $query = $this->queryFactory();
        $query->table('dbName.tableName');
        $this->assertEquals('DELETE FROM `dbName`.`tableName`', $query->toString());
        $this->assertEquals(array(), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals('DELETE FROM `dbName`.`tableName`', $query->toString());
    }

    public function testTableExpression()
    {
        $query = $this->queryFactory();
        $query->table(new Expression('`tableName` as `otherTableName`'));
        $this->assertEquals('DELETE FROM `tableName` as `otherTableName`', $query->toString());
        $this->assertEquals(array(), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals('DELETE FROM `tableName` as `otherTableName`', $query->toString());
    }

    public function testTableString()
    {
        $query = $this->queryFactory();
        $query->table('tableName');
        $this->assertEquals('DELETE FROM `tableName`', $query->toString());
        $this->assertEquals(array(), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals('DELETE FROM `tableName`', $query->toString());
    }

    public function testWhereKeyIsExpr()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->where(new Expression('columnName < NOW()'));
        $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
        $this->assertEquals(array(), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
    }

    public function testWhereKeyContainsQuestionMark()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->where('columnName > ?', 2);
        $this->assertEquals('DELETE FROM `tableName` WHERE columnName > ?', $query->toString());
        $this->assertEquals(array(2), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals('DELETE FROM `tableName` WHERE columnName > 2', $query->toString());
    }

    public function testWhereKeyContainsTable()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->where('tableName.columnName', 2);
        $this->assertEquals('DELETE FROM `tableName` WHERE `tableName`.`columnName` = ?', $query->toString());
        $this->assertEquals(array(2), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("DELETE FROM `tableName` WHERE `tableName`.`columnName` = 2", $query->toString());
    }

    public function testWhereValueIsExpression()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->where('columnName', new Expression('NOW()'));
        $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = NOW()', $query->toString());
        $this->assertEquals(array(), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = NOW()', $query->toString());
    }

    public function testWhereValueIsString()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->where('columnName', 'value');
        $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = ?', $query->toString());
        $this->assertEquals(array('value'), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("DELETE FROM `tableName` WHERE `columnName` = 'value'", $query->toString());
    }

    public function testWhereValueIsInteger()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->where('columnName', 2);
        $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` = ?', $query->toString());
        $this->assertEquals(array(2), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("DELETE FROM `tableName` WHERE `columnName` = 2", $query->toString());
    }

    public function testWhereIn()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->whereIn('columnName', array(2, 4, 6, 8));
        $this->assertEquals('DELETE FROM `tableName` WHERE `columnName` IN (?, ?, ?, ?)', $query->toString());
        $this->assertEquals(array(2, 4, 6, 8), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("DELETE FROM `tableName` WHERE `columnName` IN (2, 4, 6, 8)", $query->toString());
    }

    public function testWhereExpr()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->whereExpr('columnName < NOW()');
        $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
        $this->assertEquals(array(), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals('DELETE FROM `tableName` WHERE columnName < NOW()', $query->toString());
    }

    public function testOrderAsc()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->order('columnName', 'ASC');
        $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` ASC', $query->toString());
        $this->assertEquals(array(), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` ASC', $query->toString());
    }

    public function testOrderDesc()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->order('columnName', 'DESC');
        $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` DESC', $query->toString());
        $this->assertEquals(array(), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals('DELETE FROM `tableName` ORDER BY `columnName` DESC', $query->toString());
    }

    public function testLimitWithOffset()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->limit(10, 20);
        $this->assertEquals('DELETE FROM `tableName` LIMIT ?, ?', $query->toString());
        $this->assertEquals(array(20, 10), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("DELETE FROM `tableName` LIMIT 20, 10", $query->toString());
    }

    public function testLimitWithoutOffset()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->limit(30);
        $this->assertEquals('DELETE FROM `tableName` LIMIT ?', $query->toString());
        $this->assertEquals(array(30), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("DELETE FROM `tableName` LIMIT 30", $query->toString());
    }

    public function testOffset()
    {
        $query = $this->queryFactory();
        $query->table('tableName')
            ->offset(10, 20);
        $this->assertEquals('DELETE FROM `tableName` LIMIT ?, ?', $query->toString());
        $this->assertEquals(array(10, 20), $query->params());

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("DELETE FROM `tableName` LIMIT 10, 20", $query->toString());
    }

    public function testABunchOfStuffTogether()
    {
        $query = $this->queryFactory();
        $query->from('tableName')
            ->where('columnOne', 1324)
            ->where('columnTwo < ?', 9)
            ->where(new Expression('LENGTH(columnThree) > 0'))
            ->whereExpr('columnFour IS NULL')
            ->whereIn('columnFive', array('red', 'blue', 'green'))
            ->limit(50, 100)
            ->order('columnSix', 'DESC');
        $this->assertEquals('DELETE FROM `tableName` WHERE `columnOne` = ? && '
            . 'columnTwo < ? && LENGTH(columnThree) > 0 && '
            . 'columnFour IS NULL && '
            . '`columnFive` IN (?, ?, ?) ORDER BY `columnSix` DESC '
            . 'LIMIT ?, ?', $query->toString());
        $this->assertEquals(
            array(1324, 9, 'red', 'blue', 'green', 100, 50),
            $query->params()
        );

        // Test interpolation
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals("DELETE FROM `tableName` WHERE `columnOne` = 1324 && "
            . "columnTwo < 9 && "
            . "LENGTH(columnThree) > 0 && "
            . "columnFour IS NULL && "
            . "`columnFive` IN ('red', 'blue', 'green') "
            . "ORDER BY `columnSix` DESC "
            . "LIMIT 100, 50", $query->toString());
    }

    public function testInterpolateThrowsException()
    {
        $this->setExpectedException('zsql\\IllegalStateException');

        $query = $this->queryFactory();
        $query->table('tableName')->where('a', 'b');
        $query->interpolation();
        $query->toString();
    }

    public function testInterpolateThrowsException2()
    {
        $this->setExpectedException('zsql\\RuntimeException');

        $query = $this->queryFactory();
        $query->table('tableName')->where('a??', 'b');
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $query->toString();
    }

    public function testQueryWithInterpolation()
    {
        $expectedQuery = "DELETE FROM `tableName` WHERE `columnName` = 'value'";
        $testObject = $this;
        $callback = function ($actualQuery) use ($expectedQuery, $testObject) {
            $testObject->assertEquals($expectedQuery, $actualQuery);
            return $actualQuery;
        };
        $query = $this->queryFactory($callback);
        $query->from('tableName')->where('columnName', 'value');
        $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
        $this->assertEquals($expectedQuery, $query->query());
    }

    public function testQueryWithoutInterpolation()
    {
        $expectedQuery = "DELETE FROM `tableName` WHERE `columnName` = ?";
        $expectedParams = array('value');
        $testObject = $this;
        $callback = function ($actualQuery, $actualParams) use ($expectedQuery, $expectedParams, $testObject) {
            $testObject->assertEquals($expectedQuery, $actualQuery);
            $testObject->assertEquals($expectedParams, $actualParams);
            return $actualQuery;
        };
        $query = $this->queryFactory($callback);
        $query->from('tableName')->where('columnName', 'value');
        $this->assertEquals($expectedQuery, $query->query());
        $this->assertEquals($expectedParams, $query->params());
    }
}
