<?php

namespace zsql\Result;

use mysqli_result;
use zsql\Exception\LogicException;

/**
 * Class MysqliResult
 * @package zsql\Result
 */
class MysqliResult extends AbstractResult
{
    /**
     * @var mysqli_result
     */
    protected $result;

    /**
     * Constructor
     *
     * @param mysqli_result $result
     */
    public function __construct(mysqli_result $result = null)
    {
        $this->result = $result;
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
     * @return mysqli_result
     * @throws LogicException
     */
    public function getResult()
    {
        if( !$this->result ) {
            throw new LogicException('No result!');
        }
        return $this->result;
    }

    /**
     * Fetches a single result row
     *
     * @param integer $mode This optional parameter is a constant indicating what
     * type of result should be produced from the current row data. The possible
     * values for this parameter are the constants FETCH_ASSOC, FETCH_OBJECT,
     * FETCH_COLUMN, or FETCH_NUM.
     * @return array|object
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
     * @return array
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
     * @return string|integer|float|null
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
