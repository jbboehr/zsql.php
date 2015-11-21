<?php

namespace zsql\Tests\Fixture;

class RowWithConstructor extends \stdClass
{
    public function __construct()
    {
        $this->params = func_get_args();
    }

    public function getParams()
    {
        return $this->params;
    }
}
