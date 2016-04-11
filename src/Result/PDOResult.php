<?php

namespace zsql\Result;

use PDO;
use PDOStatement;

class PDOResult extends BaseResult
{
    /**
     * @var PDOStatement
     */
    protected $result;

    /**
     * Constructor
     *
     * @param PDOStatement $object
     */
    public function __construct(PDOStatement $object = null)
    {
        if( null !== $object ) {
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
     * Frees the local result object, and unsets it.
     *
     * @return void
     */
    public function free()
    {
        if( $this->result ) {
            //$this->result->free();
            $this->result = null;
            // Could potentially contain an object reference
            $this->resultParams = null;
        }
    }

    /**
     * Getter function for the local result object.
     *
     * @return PDOStatement
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
     * @param PDOStatement $object
     * @return $this
     */
    protected function setResult(PDOStatement $object = null)
    {
        $this->result = $object;
        return $this;
    }

    public function fetchRow($mode = null)
    {
        $spec = $this->getResult();

        if( $mode === null ) {
            $mode = $this->getResultMode();
        }

        $data = null;
        switch( $mode ) {
            case self::FETCH_ASSOC:
                $data = $spec->fetch(PDO::FETCH_ASSOC);
                break;
            case self::FETCH_COLUMN:
                $data = $spec->fetch(PDO::FETCH_COLUMN);
                break;
            case self::FETCH_NUM:
                $data = $spec->fetch(PDO::FETCH_NUM);
                break;
            case self::FETCH_OBJECT:
                if( null !== $this->resultClass ) {
                    if( null !== $this->resultParams ) {
                        $data = $spec->fetchObject($this->resultClass, $this->resultParams);
                    } else {
                        $data = $spec->fetchObject($this->resultClass);
                    }
                } else {
                    $data = $spec->fetchObject();
                }
                break;
        }

        //$this->free();
        return $data;
    }


    public function fetchAll($mode = null)
    {
        $spec = $this->getResult();

        if( $mode === null ) {
            $mode = $this->getResultMode();
        }

        $data = array();
        switch( $mode ) {
            case self::FETCH_ASSOC:
                $data = $spec->fetchAll(PDO::FETCH_ASSOC);
                break;
            case self::FETCH_COLUMN:
                $data = $spec->fetchAll(PDO::FETCH_COLUMN);
                break;
            case self::FETCH_NUM:
                $data = $spec->fetchAll(PDO::FETCH_NUM);
                break;
            case self::FETCH_OBJECT:
                if( null !== $this->resultClass ) {
                    if( null !== $this->resultParams ) {
                        while( ($row = $spec->fetchObject($this->resultClass, $this->resultParams)) ) {
                            $data[] = $row;
                        }
                    } else {
                        while( ($row = $spec->fetchObject($this->resultClass)) ) {
                            $data[] = $row;
                        }
                    }
                } else {
                    while( ($row = $spec->fetchObject()) ) {
                        $data[] = $row;
                    }
                }
                break;
        }

        //$this->free();
        return $data;
    }

    public function fetchColumn()
    {
        return $this->result->fetchColumn();
    }
}
