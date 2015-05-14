<?php

namespace zsql\Tests;

use PHPUnit_Framework_TestCase;
use ReflectionClass;

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
        return new Fixture\BasicModel($this->databaseFactory());
    }

    protected function databaseFactory()
    {
        $mysql = new \mysqli();
        $mysql->connect('127.0.0.1', 'zsql', 'nopass', 'zsql');
        return new \zsql\Database($mysql);
    }

    public function getReflectedPropertyValue($class, $propertyName)
    {
        $reflectedClass = new ReflectionClass($class);
        $property = $reflectedClass->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($class);
    }
}
