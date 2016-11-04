<?php

namespace zsql\Adapter;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use zsql\Adapter as AdapterInterface;
use zsql\QueryBuilder\Select;
use zsql\QueryBuilder\Insert;
use zsql\QueryBuilder\Update;
use zsql\QueryBuilder\Delete;

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
     * @return Select
     */
    public function select()
    {
        return new Select($this);
    }

    /**
     * Wrapper for Insert
     *
     * @return Insert
     */
    public function insert()
    {
        return new Insert($this);
    }

    /**
     * Wrapper for Update
     *
     * @return Update
     */
    public function update()
    {
        return new Update($this);
    }

    /**
     * Wrapper for Delete
     *
     * @return Delete
     */
    public function delete()
    {
        return new Delete($this);
    }
}
