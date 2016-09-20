<?php

namespace zsql\Adapter;

use mysqli;
use mysqli_result;
use Psr\Log\LoggerInterface;

use zsql\Connection\MysqliFactoryInterface;
use zsql\Expression;
use zsql\QueryBuilder\Delete;
use zsql\QueryBuilder\Insert;
use zsql\QueryBuilder\Query;
use zsql\QueryBuilder\Select;
use zsql\QueryBuilder\Update;
use zsql\Result\MysqliResult as Result;

class MysqliAdapter extends AbstractAdapter
{
    /**
     * @var mysqli
     */
    protected $connection;

    /**
     * @var MysqliFactoryInterface
     */
    protected $connectionFactory;

    /**
     * Construct a new database object.
     *
     * @param \mysqli $connection
     */
    public function __construct($connection)
    {
        if( $connection instanceof mysqli ) {
            $this->connection = $connection;
        } else if( $connection instanceof MysqliFactoryInterface ) {
            $this->connectionFactory = $connection;
            $this->connection = $connection->createMysqli();
        } else {
            throw new \InvalidArgumentException('Argument must be instance of mysqli or MysqliFactoryInterface');
        }
    }

    /**
     * Close the mysql connection on destruct
     */
    public function __destruct()
    {
        if( $this->getConnection() ) {
            $this->getConnection()->close();
        }
    }

    /**
     * Exposes the local connection object
     *
     * @return mysqli
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sets the local mysqli object
     *
     * @param mysqli $connection
     * @return $this
     */
    public function setConnection(mysqli $connection = null)
    {
        $this->connection = $connection;
        return $this;
    }

    public function setConnectionFactory(MysqliFactoryInterface $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
        return $this;
    }

    public function ping()
    {
        $ret = $this->connection->ping();

        // Try to reconnect
        if( !$ret && $this->connectionFactory ) {
            $this->connection = $this->connectionFactory->createMysqli();
            $ret = $this->ping();
        }

        return $ret;
    }

    /**
     * Executes an SQL query.
     *
     * When given a QueryBuilder instance as an argument, the return value is based on the class:
     * Select queries produce an instance of Select
     * Insert returns the insert ID
     * Update and Delete return the affected rows
     * string queries will return the value returned by the internal adapter
     *
     * @param string|Query $query
     * @return Result|integer|boolean
     * @throws Exception
     */
    public function query($query)
    {
        $connection = $this->getConnection();

        $this->affectedRows = null;
        $this->insertId = null;
        $this->queryCount++;

        $queryString = (string) $query;

        // Log query
        if( $this->logger ) {
            $this->logger->debug($queryString);
        }

        // Execute query
        $ret = $connection->query($queryString, MYSQLI_STORE_RESULT);
        if( ($connection->errno === 2006 || $connection->errno === 2013) && $this->connectionFactory ) {
            // Log the connection error
            if( $this->logger ) {
                $this->logger->debug('Attempting to reconnect after error: ' . $connection->error);
            }
            // Reconnect
            $connection = $this->connection = $this->connectionFactory->createMysqli();
            // Retry once
            $ret = $connection->query($queryString, MYSQLI_STORE_RESULT);
        }

        // Save insert ID if instance of insert
        if( $query instanceof Insert ) {
            $this->insertId = $connection->insert_id;
        }

        // Save affected rows
        $this->affectedRows = $connection->affected_rows;

        // Handle result
        if( $ret !== false ) {
            if( $ret instanceof mysqli_result ) {
                return new Result($ret);
            } else if( $query instanceof Insert ) {
                return $this->getInsertId();
            } else if( $query instanceof Update ||
                $query instanceof Delete ) {
                return $this->getAffectedRows();
            }
            return $ret;
        } else {
            $message = sprintf(
                "%s: %s\n%s",
                $connection->errno,
                $connection->error,
                $query
            );
            // Log error
            if( $this->logger ) {
                $this->logger->error($message);
            }
            // Query failed, throw exception
            throw new Exception($message);
        }
    }

    /**
     * Quote a raw string
     *
     * @param string $value
     * @return string
     */
    public function quote($value)
    {
        if( null === $value ) {
            return 'NULL';
        } else if( is_bool($value) ) {
            return ( $value ? '1' : '0' );
        } else if( $value instanceof Expression ) {
            return (string) $value;
        } else if( is_integer($value) ) {
            return sprintf('%d', $value);
        } else if( is_float($value) ) {
            return sprintf('%f', $value); // @todo make sure precision is right
        } else {
            return "'" . $this->connection->real_escape_string($value) . "'";
        }
    }
}
