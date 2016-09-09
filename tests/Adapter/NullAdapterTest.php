<?php

namespace zsql\Tests;

use zsql\Adapter\NullAdapter;

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
}
