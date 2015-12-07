<?php

namespace zsql\Tests\Fixture;

use zsql\Table\DefaultTable;

class ModelWithResultClass extends DefaultTable
{
    protected $tableName = 'fixture1';
    protected $resultClass = 'zsql\\Tests\\Fixture\\Result';
    protected $primaryKey = 'id';
}
