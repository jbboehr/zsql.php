<?php

namespace zsql\Result;

interface Result
{
    const FETCH_COLUMN = 0;
    const FETCH_OBJECT = 1;
    const FETCH_ASSOC = 2;
    const FETCH_NUM = 3;

    /**
     * @param integer $mode
     * @return array
     */
    public function fetchAll($mode = null);

    /**
     * @return string|integer|float|null
     */
    public function fetchColumn();

    /**
     * @param integer $mode
     * @return array|object
     */
    public function fetchRow($mode = null);

    /**
     * Get result class
     *
     * @return string
     */
    public function getResultClass();

    /**
     * Set result class
     *
     * @param string $class
     * @return $this
     * @throws Exception
     */
    public function setResultClass($class);

    /**
     * @return integer
     */
    public function getResultMode();

    /**
     * @param integer $mode
     * @return $this
     * @throws Exception
     */
    public function setResultMode($mode);

    /**
     * Get result params
     *
     * @return array
     */
    public function getResultParams();

    /**
     * Set result params
     *
     * @param array $params
     * @return $this
     */
    public function setResultParams(array $params = null);
}
