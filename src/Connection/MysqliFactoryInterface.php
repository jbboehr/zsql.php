<?php

namespace zsql\Connection;

interface MysqliFactoryInterface
{
    /**
     * @return \mysqli
     */
    public function createMysqli();
}
