<?php

namespace zsql\Tests;

use mysqli;
use ReflectionClass;
use PHPUnit_Framework_TestCase;
use zsql\Adapter\MysqliAdapter;
use zsql\Connection\MysqliFactory;

class Common extends PHPUnit_Framework_TestCase
{
    protected $fixtureOneRowCount = 2;
    protected $useVerboseErrorHandler = false;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if( $this->useVerboseErrorHandler ) {
            $this->setVerboseErrorHandler();
        }
    }

    protected function setVerboseErrorHandler()
    {
        $handler = function ($errorNumber, $errorString, $errorFile, $errorLine) {
            echo "ERROR INFO\nMessage: $errorString\nFile: $errorFile\nLine: $errorLine\n";
        };
        set_error_handler($handler);
    }

    protected function fixtureModelOneFactory()
    {
        $model = new Fixture\BasicModel();
        $model->setDatabase($this->createMysqliAdapter());
        return $model;
    }

    /**
     * @return MysqliAdapter
     */
    protected function createMysqliAdapter()
    {
        return new MysqliAdapter($this->createMysqliFactory()->createMysqli());
    }

    protected function databaseFactory()
    {
        return $this->createMysqliAdapter();
    }

    protected function createMysqliFactory()
    {
        return new MysqliFactory(
            ZSQL_TEST_DATABASE_HOST,
            ZSQL_TEST_DATABASE_USERNAME,
            ZSQL_TEST_DATABASE_PASSWORD,
            ZSQL_TEST_DATABASE_DBNAME
        );
    }

    public function getReflectedPropertyValue($class, $propertyName)
    {
        $reflectedClass = new ReflectionClass($class);
        $property = $reflectedClass->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($class);
    }
}
