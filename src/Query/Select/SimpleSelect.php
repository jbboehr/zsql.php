<?php

namespace zsql\Query\Select;

use zsql\Adapter\AdapterAwareInterface;
use zsql\Adapter\AdapterAwareTrait;
use zsql\Query\Select;

class SimpleSelect implements Select, AdapterAwareInterface
{
    use AdapterAwareTrait;

    /**
     * @var string
     */
    protected $table;

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
     * @return $this|SimpleSelect
     */
    public function setLimit(integer $limit) : SimpleSelect
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param string $table
     * @return $this|SimpleSelect
     */
    public function setTable(string $table) : SimpleSelect
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $where
     * @return $this|SimpleSelect
     */
    public function setWhere(array $where) : SimpleSelect
    {
        $this->where = $where;
        return $this;
    }

    /**
     * Get the result class
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
     */
    public function setResultClass($class)
    {
        $this->resultClass = $class;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toString() : string
    {
        $where = array();
        foreach( $this->where as $k => $v ) {
            if( is_array($v) ) {
                $where[] = $this->database->quoteIdentifier($k) . ' IN(' . array_map(function($v) {
                    return $this->database->quote($v);
                }, $v) . ')';
            } else {
                $where[] = $this->database->quoteIdentifier($k) . ' = ' . $this->database->quote($v);
            }
        }
        $q = 'SELECT * '
            . 'FROM ' . $this->database->quoteIdentifier($this->table) . ' '
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
