<?php

namespace zsql\Adapter;

use zsql\Adapter as AdapterInterface;

trait AdapterAwareTrait
{
    /**
     * @var Adapter
     */
    protected $database;

    /**
     * @param AdapterInterface $database
     * @return $this
     */
    public function setDatabase(AdapterInterface $database)
    {
        $this->database = $database;
        return $this;
    }
}
