<?php

namespace zsql\Adapter;

use zsql\Adapter;

interface AdapterAwareInterface
{
    /**
     * @param Adapter $adapter
     * @return $this
     */
    public function setDatabase(Adapter $adapter);
}
