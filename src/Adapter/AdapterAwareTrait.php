<?php

namespace zsql\Adapter;

trait AdapterAwareTrait
{
    /**
     * @var Adapter
     */
    protected $database;

    /**
     * @param Adapter $database
     * @return $this
     */
    public function setDatabase(Adapter $database)
    {
        $this->database = $database;
        return $this;
    }
}
