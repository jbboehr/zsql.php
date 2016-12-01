<?php

namespace zsql\Query\Update;

use zsql\Adapter\AdapterAwareInterface;
use zsql\Adapter\AdapterAwareTrait;
use zsql\Query\Update;

class SimpleUpdate implements Update, AdapterAwareInterface
{
    use AdapterAwareTrait;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var array
     */
    protected $where;

    /**
     * @var integer
     */
    protected $limit;

    /**
     * The result class
     *
     * @var string
     */
    protected $resultClass;

    /**
     * @param int $limit
     * @return $this|SimpleUpdate
     */
    public function setLimit(integer $limit) : SimpleUpdate
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param string $table
     * @return $this|SimpleUpdate
     */
    public function setTable(string $table) : SimpleUpdate
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $values
     * @return $this|SimpleUpdate
     */
    public function setValues(array $values) : SimpleUpdate
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param array $where
     * @return $this|SimpleUpdate
     */
    public function setWhere(array $where) : SimpleUpdate
    {
        $this->where = $where;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toString() : string
    {
        // Make set
        $set = array();
        foreach( $this->values as $k => $v ) {
            $set[] = $this->database->quoteIdentifier($k) . ' = ' . $this->database->quote($v);
        }
        // Make where
        $where = array();
        foreach( $this->where as $k => $v ) {
            $where[] = $this->database->quoteIdentifier($k) . ' = ' . $this->database->quote($v);
        }
        $q = 'UPDATE ' . $this->database->quoteIdentifier($this->table) . ' '
            . 'SET ' . join(', ', $set)
            . 'WHERE ' . join(' && ', $where);
        if( null !== $this->limit ) {
            $q .= ' LIMIT ' . $this->limit;
        }
        return $q;
    }

    /**
     * @inheritdoc
     */
    public function __toString() : string
    {
        try {
            return $this->toString();
        } catch( \Throwable $e ) {
            error_log($e);
            return '';
        }
    }

    /**
     * @inheritdoc
     */
    public function params() : array
    {
        return array();
    }
}
