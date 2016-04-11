<?php

namespace zsql\Result;

abstract class BaseResult implements Result
{
    /**
     * @var string
     */
    protected $resultClass = 'zsql\\Row\\DefaultRow';

    /**
     * @var integer
     */
    protected $resultMode;

    /**
     * @var array
     */
    protected $resultParams;

    /**
     * Get result class
     *
     * @return string
     */
    public function getResultClass()
    {
        return $this->resultClass;
    }

    /**
     * Set result class
     *
     * @param string $class
     * @return $this
     * @throws Exception
     */
    public function setResultClass($class = null)
    {
        if( null !== $class && (!is_string($class) || !class_exists($class)) ) {
            throw new Exception('Class not found');
        }
        $this->resultClass = $class;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getResultParams()
    {
        return $this->resultParams;
    }

    /**
     * Set result params
     *
     * @param array|null $params
     * @return $this
     */
    public function setResultParams(array $params = null)
    {
        $this->resultParams = $params;
        return $this;
    }

    /**
     * Get current result mode
     *
     * @return integer
     */
    public function getResultMode()
    {
        return $this->resultMode !== null ? $this->resultMode : self::FETCH_OBJECT;
    }

    /**
     * Set result mode
     *
     * @param integer $mode
     * @return $this
     * @throws Exception
     */
    public function setResultMode($mode)
    {
        if( !is_int($mode) || $mode < 0 || $mode > 3 ) {
            throw new Exception("Invalid result mode");
        }
        $this->resultMode = $mode;
        return $this;
    }
}