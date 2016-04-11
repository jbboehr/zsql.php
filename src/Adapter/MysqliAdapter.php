<?php

namespace zsql\Adapter;

use mysqli;
use mysqli_result;

use zsql\Expression;
use zsql\Feature;
use zsql\QueryBuilder\Delete;
use zsql\QueryBuilder\Insert;
use zsql\QueryBuilder\Query;
use zsql\QueryBuilder\Update;
use zsql\Result\MysqliResult as Result;

class MysqliAdapter extends BaseAdapter
{
    static private $features = array(
        Feature::INSERT_SET => true,
        Feature::ON_DUPLICATE_KEY_UPDATE => true,
    );

    /**
     * The character to use to quote identifiers
     *
     * @var string
     */
    protected $quoteIdentifierChar = '`';

    /**
     * @var mysqli
     */
    protected $connection;

    /**
     * Construct a new database object.
     *
     * @param \mysqli $connection
     */
    public function __construct(mysqli $connection)
    {
        $this->setConnection($connection);
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

    public function getDriverName()
    {
        return 'mysqli';
    }

    public function getFeatures()
    {
        return self::$features;
    }

    public function hasFeature($feature)
    {
        return isset(self::$features[$feature]);
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

        // __toString is not allowed to throw exceptions
        if( $query instanceof Query ) {
            $queryString = $query->toString();
        } else {
            $queryString = (string) $query;
        }

        // Log query
        if( $this->logger ) {
            $this->logger->debug($queryString);
        }

        // Execute query
        $counter = 0;
        do {
            $retry = false;
            $ret = $connection->query($queryString, MYSQLI_STORE_RESULT);
            // Handle "MySQL server has gone away" and "Lost connection to MySQL server during query"
            if( in_array($connection->errno, array(2006, 2013)) && ++$counter <= $this->retryCount ) {
                if( $this->connectionFactory ) {
                    $connection = $this->connection = call_user_func($this->connectionFactory);
                    $retry = true;
                }
            }
        } while( $retry );

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

    public function quoteIdentifier($identifier)
    {
        $c = $this->quoteIdentifierChar;
        return $c . str_replace(
            '.',
            $c . '.' . $c,
            str_replace($c, $c . $c, $identifier)
        ) . $c;
    }
}
