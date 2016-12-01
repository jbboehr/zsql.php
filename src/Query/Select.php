<?php

namespace zsql\Query;

use zsql\Query;

interface Select extends Query
{
    /**
     * Get the result class
     *
     * @return string
     */
    public function getResultClass();

    /**
     * Set result class
     *
     * @param string $class
     * @return $this
     */
    public function setResultClass($class);
}
