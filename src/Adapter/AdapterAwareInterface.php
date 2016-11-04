<?php

namespace zsql\Adapter;

use zsql\Adapter as AdapterInterface;

interface AdapterAwareInterface
{
    /**
     * @param AdapterInterface $adapter
     * @return $this
     */
    public function setDatabase(AdapterInterface $adapter);
}
