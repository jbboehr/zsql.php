<?php

namespace zsql\Tests;

use zsql\Adapter\NullAdapter;
use zsql\Expression;

class NullAdapterTest extends Common
{
    public function testQuery()
    {
        $logger = $this->getMock('Psr\Log\NullLogger', array('debug'));
        $logger->expects($this->once())
            ->method('debug');

        $database = new NullAdapter();
        $database->setLogger($logger);
        $database->query('SELECT TRUE');
    }

    public function testQuery2()
    {
        $logger = $this->getMock('Psr\Log\NullLogger', array('debug'));
        $logger->expects($this->once())
            ->method('debug');

        $database = new NullAdapter();
        $database->setLogger($logger);
        $result = $database->query($database->select()
            ->table('fixture1')
            ->columns(new Expression('TRUE')));
        $this->assertInstanceOf('zsql\\Result\\NulLResult', $result);
        $this->assertSame(array(), $result->fetchAll());
        $this->assertNull($result->fetchRow());
        $this->assertNull($result->fetchColumn());
        $this->assertNull($result->fetchColumn());
    }

    public function testQuote()
    {
        $this->setExpectedException('Exception');
        $database = new NullAdapter();
        $database->quote('meh');
    }
}
