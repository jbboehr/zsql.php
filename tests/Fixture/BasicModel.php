<?php

namespace zsql\Tests\Fixture;

use zsql\Table\DefaultTable;

class BasicModel extends DefaultTable
{
    protected $tableName = 'fixture1';
    protected $primaryKey = 'id';
}
