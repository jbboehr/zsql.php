<?php

namespace zsql\Tests\Fixture;

class ModelWithResultClass extends \zsql\Model
{
    protected $tableName = 'fixture1';
    protected $resultClass = '\\zsql\\Tests\\Fixture\\Result';
    protected $primaryKey = 'id';
}
