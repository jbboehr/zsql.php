<?php

namespace zsql\Query\Insert;

use zsql\Adapter\AdapterAwareInterface;
use zsql\Adapter\AdapterAwareTrait;
use zsql\Query\Insert;

class SimpleInsert implements Insert, AdapterAwareInterface
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
     * @param string $table
     * @return $this|SimpleInsert
     */
    public function setTable(string $table) : SimpleInsert
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $values
     * @return $this|SimpleInsert
     */
    public function setValues(array $values) : SimpleInsert
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toString() : string
    {
        $keys = array();
        $values = array();
        foreach( $this->values as $k => $v ) {
            $keys[] = $this->database->quoteIdentifier($k);
            $values[] = $this->database->quote($v);
        }
        return 'INSERT '
            . 'INTO ' . $this->database->quoteIdentifier($this->table) . ' '
            . '(' . join(', ', $keys) . ') '
            . 'VALUES (' . join(', ', $values) . ')';
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
