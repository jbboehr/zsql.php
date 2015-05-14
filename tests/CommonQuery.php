<?php

namespace zsql\Tests;

class CommonQuery extends Common
{
    protected $className;

    public function testClassExists()
    {
        $this->assertEquals(class_exists($this->className), true);
    }

    public function testConstruction()
    {
        $this->assertInstanceOf($this->className, $this->_factory());
    }

    public function testMagicToStringFails()
    {
        $reporting = error_reporting(0);
        $this->assertEmpty((string) $this->_factory());
        error_reporting($reporting);
        $lastError = error_get_last();
        $this->assertEquals('No table specified', $lastError['message']);
    }

    public function testParts()
    {
        $this->assertEquals(true, is_array($this->_factory()->parts()));
    }

    public function testParams()
    {
        $this->assertEquals(true, is_array($this->_factory()->params()));
    }

    public function testQueryThrowsException()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $query = $this->_factory();
        $query->query();
    }

    public function testSetQuoteCallbackThrowsException()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        $query = $this->_factory();
        $query->setQuoteCallback(false);
    }

    public function testInvalidConstructionArgThrowsException()
    {
        $this->setExpectedException('\\zsql\\Exception');
        
        new $this->className('blah');
    }

    protected function _factory()
    {
        return new $this->className();
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
