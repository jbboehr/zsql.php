<?php

namespace zsql\Tests;

use zsql\Adapter\MultiplexAdapter;
use zsql\Adapter\MysqliAdapter;
use zsql\Adapter\NullAdapter;
use zsql\Expression;

class MultiplexAdapterTest extends Common
{
    public function testConstruct()
    {
        $reader = $this->createMysqliAdapter();
        $writer = $this->createMysqliAdapter();
        $adapter = new MultiplexAdapter($reader, $writer);

        $this->assertTrue($adapter->hasReader());
        $this->assertTrue($adapter->hasWriter());
        $this->assertSame($reader, $adapter->getReader());
        $this->assertSame($writer, $adapter->getWriter());
    }

    public function testSetReader()
    {
        $adapter1 = $this->createMysqliAdapter();
        $adapter2 = $this->createMysqliAdapter();
        $adapter = new MultiplexAdapter($adapter1, $adapter2);;

        $orig = $adapter->getReader();
        $adapter->setReader($adapter2);

        $this->assertNotSame($orig, $adapter->getReader());
        $this->assertSame($adapter2, $adapter->getReader());
    }

    public function testSetWriter()
    {
        $adapter1 = $this->createMysqliAdapter();
        $adapter2 = $this->createMysqliAdapter();
        $adapter = new MultiplexAdapter($adapter1, $adapter2);

        $orig = $adapter->getWriter();
        $adapter->setWriter($adapter1);

        $this->assertNotSame($orig, $adapter->getWriter());
        $this->assertSame($adapter1, $adapter->getWriter());
    }

    public function testForceWriter()
    {
        $reader = $this->getMock('zsql\\Adapter\\NullAdapter', array('query'));
        $reader->expects($this->never())
            ->method('query');

        $writer = $this->getMock('zsql\\Adapter\\NullAdapter', array('query'));
        $writer->expects($this->once())
            ->method('query');

        $adapter = new MultiplexAdapter($reader, $writer);

        $adapter->useWriter();
        $adapter->query($adapter->select()->columns(new Expression('TRUE')));
    }

    public function testSelect()
    {
        $reader = $this->getMock('zsql\\Adapter\\NullAdapter', array('query'));
        $reader->expects($this->once())
            ->method('query');

        $writer = $this->getMock('zsql\\Adapter\\NullAdapter', array('query'));
        $writer->expects($this->never())
            ->method('query');

        $adapter = new MultiplexAdapter($reader, $writer);

        $adapter->query($adapter->select()->columns(new Expression('TRUE')));
    }

    public function testInsert()
    {
        $reader = $this->getMock('zsql\\Adapter\\NullAdapter', array('query'));
        $reader->expects($this->never())
            ->method('query');

        $writer = $this->getMock('zsql\\Adapter\\NullAdapter', array('query'));
        $writer->expects($this->once())
            ->method('query');

        $adapter = new MultiplexAdapter($reader, $writer);

        $adapter->useWriter();
        $adapter->query($adapter->insert());
    }

    public function testQuote()
    {
        $reader = $this->createMysqliAdapter();
        $writer = $this->createMysqliAdapter();
        $adapter = new MultiplexAdapter($reader, $writer);

        $this->assertEquals($reader->quote(null), $adapter->quote(null));
        $this->assertEquals($reader->quote('1'), $adapter->quote('1'));
        $this->assertEquals($reader->quote(new Expression('"')), $adapter->quote(new Expression('"')));
    }

    public function testLogging()
    {
        $logger = $this->getMock('Psr\Log\NullLogger', array('debug', 'error'));
        $logger->expects($this->once())
            ->method('debug');
        $logger->expects($this->once())
            ->method('error');

        $this->setExpectedException('zsql\\Adapter\\Exception');

        $reader = $this->createMysqliAdapter();
        $writer = $this->createMysqliAdapter();
        $adapter = new MultiplexAdapter($reader, $writer);
        $adapter->setLogger($logger);
        $adapter->query('SKWLKEJFRKWKSEDRTJFKSDFR broken query');
    }

    public function testLogging2()
    {
        $logger = $this->getMock('Psr\Log\NullLogger', array('debug', 'error'));
        $logger->expects($this->once())
            ->method('debug');

        $reader = $this->createMysqliAdapter();
        $writer = $this->createMysqliAdapter();
        $adapter = new MultiplexAdapter($reader, $writer);
        $adapter->setLogger($logger);
        $adapter->query($adapter->select()->columns(new Expression('TRUE'))->table('fixture1'));
    }

    public function testPing()
    {
        $reader = $this->createMysqliAdapter();
        $writer = $this->createMysqliAdapter();
        $adapter = new MultiplexAdapter($reader, $writer);
        $this->assertTrue($adapter->ping());
    }

    public function testPing_DisconnectFailure()
    {
        $reader = $this->createMysqliAdapter();
        $writer = $this->createMysqliAdapter();
        $adapter = new MultiplexAdapter($reader, $writer);

        $mysqliReader = $reader->getConnection();
        $mysqliReader->kill($mysqliReader->thread_id);

        $this->assertFalse($adapter->ping());
    }

    public function testPing_WithReconnect()
    {
        $reader = new MysqliAdapter($this->createMysqliFactory());
        $writer = new MysqliAdapter($this->createMysqliFactory());
        $adapter = new MultiplexAdapter($reader, $writer);

        $mysqli = $reader->getConnection();
        $mysqli->kill($mysqli->thread_id);

        $this->assertTrue($adapter->ping());
    }
}
