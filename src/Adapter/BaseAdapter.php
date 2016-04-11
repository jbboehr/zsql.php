<?php

namespace zsql\Adapter;

use Psr\Log\LoggerInterface;

use zsql\QueryBuilder\Delete;
use zsql\QueryBuilder\Insert;
use zsql\QueryBuilder\Select;
use zsql\QueryBuilder\Update;

abstract class BaseAdapter implements Adapter
{
    /**
     * @var integer
     */
    protected $affectedRows;

    /**
     * @var callable Connection factory function. Will be called on connection timeout to establish a new connection.
     */
    public $connectionFactory;

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
     * @var integer The number of times reconnection should be attempted
     */
    public $retryCount = 1;


    /**
     * Get affected rows
     *
     * @return integer
     */
    public function getAffectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * Get the last insert ID
     *
     * @return integer
     */
    public function getInsertId()
    {
        return $this->insertId;
    }

    /**
     * Gets number of queries run using this adapter.
     *
     * @return integer
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
