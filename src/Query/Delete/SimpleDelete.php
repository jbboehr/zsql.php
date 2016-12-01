<?php

namespace zsql\Query\Delete;

use zsql\Adapter\AdapterAwareInterface;
use zsql\Adapter\AdapterAwareTrait;
use zsql\Query\Delete;

class SimpleDelete implements Delete, AdapterAwareInterface
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
     * @param int $limit
     * @return $this|SimpleDelete
     */
    public function setLimit(integer $limit) : SimpleDelete
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param string $table
     * @return $this|SimpleDelete
     */
    public function setTable(string $table) : SimpleDelete
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $where
     * @return $this|SimpleDelete
     */
    public function setWhere(array $where) : SimpleDelete
    {
        $this->where = $where;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toString() : string
    {
        $where = array();
        foreach( $this->where as $k => $v ) {
            $where[] = $this->database->quoteIdentifier($k) . ' = ' . $this->database->quote($v);
        }
        $q = 'DELETE '
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
