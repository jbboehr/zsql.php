<?php

namespace zsql\Result;

use mysqli_result;

class MysqliResult implements Result
{
    /**
     * @var \mysqli_result
     */
    protected $result;

    /**
     * @var string
     */
    protected $resultClass = '\\zsql\\Row\\DefaultRow';

    /**
     * @var integer
     */
    protected $resultMode;

    /**
     * @var array
     */
    protected $resultParams;
    
    const FETCH_COLUMN = 0;
    const FETCH_OBJECT = 1;
    const FETCH_ASSOC = 2;
    const FETCH_NUM = 3;

    /**
     * Constructor
     *
     * @param mixed $object
     */
    public function __construct($object)
    {
        if( $object instanceof \mysqli_result ) {
            $this->setResult($object);
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->free();
    }

    /**
     * Frees the local mysqli_result object, and unsets it.
     *
     * @return void
     */
    public function free()
    {
        if( $this->result ) {
            $this->result->free();
            $this->result = null;
        }
    }

    /**
     * Getter function for the local mysqli_result object.
     *
     * @return \mysqli_result
     * @throws Exception
     */
    public function getResult()
    {
        if( !$this->result ) {
            throw new Exception('No result!');
        }
        return $this->result;
    }

    /**
     * Setter function for the local mysqli_result object.
     *
     * @param \mysqli_result $object
     * @return \zsql\Result
     */
    protected function setResult(mysqli_result $object)
    {
        $this->result = $object;
        return $this;
    }

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
     * @return \zsql\Result
     * @throws Exception
     */
    public function setResultClass($class)
    {
        if( !is_string($class) || !class_exists($class) ) {
            throw new Exception('Class not found');
        }
        $this->resultClass = $class;
        return $this;
    }

    /**
     * Set result params
     *
     * @param array $params
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
     * @return \zsql\Result
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

    /**
     * Fetches a single result row
     *
     * @param integer $mode This optional parameter is a constant indicating what
     * type of result should be produced from the current row data. The possible
     * values for this parameter are the constants FETCH_ASSOC, FETCH_OBJECT,
     * FETCH_COLUMN, or FETCH_NUM.
     * @return mixed
     */
    public function fetchRow($mode = null)
    {
        $spec = $this->getResult();

        if( $mode === null ) {
            $mode = $this->getResultMode();
        }

        $data = null;
        switch( $mode ) {
            case self::FETCH_ASSOC:
                $data = $spec->fetch_assoc();
                break;
            case self::FETCH_COLUMN:
                if( ($row = $spec->fetch_row()) && is_array($row) ) {
                    $data = current($row);
                } else {
                    $data = null;
                }
                break;
            case self::FETCH_NUM:
                $data = $spec->fetch_row();
                break;
            case self::FETCH_OBJECT:
                if( null !== $this->resultClass ) {
                    if( null !== $this->resultParams ) {
                        $data = $spec->fetch_object($this->resultClass, $this->resultParams);
                    } else {
                        $data = $spec->fetch_object($this->resultClass);
                    }
                } else {
                    $data = $spec->fetch_object();
                }
                break;
        }

        $this->free();
        return $data;
    }

    /**
     * Fetches all result rows
     *
     * @param integer $mode This optional parameter is a constant indicating what
     * type of result should be produced from the current row data. The possible
     * values for this parameter are the constants FETCH_ASSOC, FETCH_OBJECT,
     * FETCH_COLUMN, or FETCH_NUM.
     *
     * @return mixed
     */
    public function fetchAll($mode = null)
    {
        $spec = $this->getResult();

        if( $mode === null ) {
            $mode = $this->getResultMode();
        }

        $data = array();
        switch( $mode ) {
            case self::FETCH_ASSOC:
                while( ($row = $spec->fetch_assoc()) ) {
                    $data[] = $row;
                }
                break;
            case self::FETCH_COLUMN:
                while( ($row = $spec->fetch_row()) ) {
                    $data[] = current($row);
                }
                break;
            case self::FETCH_NUM:
                while( ($row = $spec->fetch_row()) ) {
                    $data[] = $row;
                }
                break;
            case self::FETCH_OBJECT:
                if( null !== $this->resultClass ) {
                    if( null !== $this->resultParams ) {
                        while( ($row = $spec->fetch_object($this->resultClass, $this->resultParams)) ) {
                            $data[] = $row;
                        }
                    } else {
                        while( ($row = $spec->fetch_object($this->resultClass)) ) {
                            $data[] = $row;
                        }
                    }
                } else {
                    while( ($row = $spec->fetch_object()) ) {
                        $data[] = $row;
                    }
                }
                break;
        }

        $this->free();
        return $data;
    }

    /**
     * Returns a single value from the first result row.
     *
     * @return mixed
     */
    public function fetchColumn()
    {
        $spec = $this->getResult();

        $data = $spec->fetch_row();
        if( is_array($data) ) {
            $data = current($data);
        }

        $this->free();
        return $data;
    }
}
