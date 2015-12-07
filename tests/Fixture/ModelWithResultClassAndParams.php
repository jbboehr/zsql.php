<?php

namespace zsql\Tests\Fixture;

use zsql\Table\DefaultTable;

class ModelWithResultClassAndParams extends DefaultTable
{
    protected $tableName = 'fixture1';
    protected $resultClass = 'zsql\\Tests\\Fixture\\RowWithConstructor';
    protected $resultParams = array('param1', 'param2');
    protected $primaryKey = 'id';
}
