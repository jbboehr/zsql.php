<?php

namespace zsql\Tests\Fixture;

use zsql\Model;

class ModelWithResultClassAndParams extends Model
{
    protected $tableName = 'fixture1';
    protected $resultClass = '\\zsql\\Tests\\Fixture\\RowWithConstructor';
    protected $resultParams = array('param1', 'param2');
    protected $primaryKey = 'id';
}
