<?php

namespace zsql\Connection;

class MysqliFactory implements MysqliFactoryInterface
{
    private $host;
    private $username;
    private $passwd;
    private $dbname;
    private $port;
    private $socket;

    public function __construct(
        $host,
        $username,
        $passwd,
        $dbname,
        $port = null,
        $socket = null
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->passwd = $passwd;
        $this->dbname = $dbname;
        $this->port = $port;
        $this->socket = $socket;
    }

    public function createMysqli()
    {
        return new \mysqli(
            $this->host,
            $this->username,
            $this->passwd,
            $this->dbname,
            $this->port,
            $this->socket
        );
    }
}
