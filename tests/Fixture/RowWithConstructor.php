<?php

namespace zsql\Tests\Fixture;

use zsql\Row\DefaultRow;

class RowWithConstructor extends DefaultRow
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
