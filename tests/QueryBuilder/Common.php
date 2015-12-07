<?php

namespace zsql\Tests\QueryBuilder;

use zsql\Tests\Common as BaseCommon;

class Common extends BaseCommon
{
    protected $className;

    public function testClassExists()
    {
        $this->assertEquals(class_exists($this->className), true);
    }

    public function testConstruction()
    {
        $this->assertInstanceOf($this->className, $this->queryFactory());
    }

    public function testMagicToStringFails()
    {
        $reporting = error_reporting(0);
        $this->assertEmpty((string) $this->queryFactory());
        error_reporting($reporting);
        $lastError = error_get_last();
        $this->assertEquals('No table specified', $lastError['message']);
    }

    public function testParts()
    {
        $this->assertEquals(true, is_array($this->queryFactory()->parts()));
    }

    public function testParams()
    {
        $this->assertEquals(true, is_array($this->queryFactory()->params()));
    }

    public function testQueryThrowsException()
    {
        $this->setExpectedException('zsql\\Exception');
        
        $query = $this->queryFactory();
        $query->query();
    }

    public function testSetQuoteCallbackThrowsException()
    {
        $this->setExpectedException('zsql\\Exception');
        
        $query = $this->queryFactory();
        $query->setQuoteCallback(false);
    }

    public function testInvalidConstructionArgThrowsException()
    {
        $this->setExpectedException('zsql\\Exception');
        
        new $this->className('blah');
    }

    /**
     * @param mixed $arg
     * @return mixed
     */
    protected function queryFactory($arg = null)
    {
        return new $this->className($arg);
    }

    protected function _getQuoteCallback()
    {
        // This is not a real quote function, just for testing
        return function ($string) {
            if( is_int($string) || is_double($string) ) {
                return $string;
            } else if( is_null($string) ) {
                return 'NULL';
            } else if( is_bool($string) ) {
                return $string ? 'TRUE' : 'FALSE';
            } else {
                return "'" . addslashes($string) . "'";
            }
        };
    }
}
