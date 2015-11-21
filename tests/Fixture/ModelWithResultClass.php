<?php

namespace zsql\Tests\Fixture;

use zsql\Model;

class ModelWithResultClass extends Model
{
    protected $tableName = 'fixture1';
    protected $resultClass = '\\zsql\\Tests\\Fixture\\Result';
    protected $primaryKey = 'id';
}
