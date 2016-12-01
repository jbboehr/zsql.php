<?php

namespace zsql\Adapter;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use zsql\Adapter as AdapterInterface;
use zsql\QueryBuilder;

abstract class AbstractAdapter implements AdapterInterface, LoggerAwareInterface
{
    /**
     * @var integer
     */
    protected $affectedRows;

    /**
     * @var callable Connection factory function. Will be called on connection timeout to establish a new connection.
     */
    protected $connectionFactory;

    /**
     * @var integer
     */
    protected $insertId;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Holds the total number of queries ran for the database object's lifetime.
     *
     * @var integer
     */
    protected $queryCount = 0;

    /**
     * @var string
     */
    protected $quoteIdentifierChar = '`';

    /**
     * @inheritdoc
     */
    public function getAffectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * @inheritdoc
     */
    public function getInsertId($name = null)
    {
        return $this->insertId;
    }

    /**
     * @inheritdoc
     */
    public function getQueryCount()
    {
        return $this->queryCount;
    }

    /**
     * Set a query logger
     *
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @param $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        $c = $this->quoteIdentifierChar;
        return $c . str_replace(
            '.',
            $c . '.' . $c,
            str_replace($c, $c . $c, $identifier)
        ) . $c;
    }

    /**
     * Wrapper for Select
     *
     * @return QueryBuilder\Select
     */
    public function select()
    {
        return new QueryBuilder\Select($this);
    }

    /**
     * Wrapper for Insert
     *
     * @return QueryBuilder\Insert
     */
    public function insert()
    {
        return new QueryBuilder\Insert($this);
    }

    /**
     * Wrapper for Update
     *
     * @return QueryBuilder\Update
     */
    public function update()
    {
        return new QueryBuilder\Update($this);
    }

    /**
     * Wrapper for Delete
     *
     * @return QueryBuilder\Delete
     */
    public function delete()
    {
        return new QueryBuilder\Delete($this);
    }

    public function insertQuery(string $table, array $values)
    {
        return $this->insert()
            ->table($table)
            ->values($values)
            ->query();
    }

    public function updateQuery(string $table, array $values, array $where)
    {
        $q = $this->update()
            ->table($table)
            ->values($values);
        foreach( $where as $k => $v ) {
            $q->where($k, $v);
        }
        return $q->query();
    }

    public function deleteQuery(string $table, array $where)
    {
        $q = $this->delete()
            ->table($table);
        foreach( $where as $k => $v ) {
            $q->where($k, $v);
        }
        return $q->query();
    }

    public function selectQuery(string $table, array $where)
    {
        $q = $this->select()
            ->table($table);
        foreach( $where as $k => $v ) {
            $q->where($k, $v);
        }
        return $q->query();
    }
}
