<?php

namespace zsql\Adapter;

use PDO;
use PDOStatement;

use zsql\Expression;
use zsql\QueryBuilder\Delete;
use zsql\QueryBuilder\Insert;
use zsql\QueryBuilder\Query;
use zsql\QueryBuilder\Select;
use zsql\QueryBuilder\Update;
use zsql\Result\PDOResult as Result;

class PDOAdapter extends BaseAdapter
{
    private $driverName;

    /**
     * @var PDO
     */
    protected $connection;

    protected $quoteIdentifierChar;
    
    /**
     * Construct a new database object.
     *
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->setConnection($connection);
    }

    /**
     * Close the connection on destruct
     */
    public function __destruct()
    {
        if( $this->getConnection() ) {
            //$this->getConnection()->close();
        }
    }

    /**
     * Exposes the local connection object
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sets the local mysqli object
     *
     * @param PDO $connection
     * @return $this
     */
    public function setConnection(PDO $connection = null)
    {
        $this->connection = $connection;
        $this->driverName = $connection ? $connection->getAttribute(PDO::ATTR_DRIVER_NAME) : null;
        if( $this->driverName === 'mysql' ) {
            $this->quoteIdentifierChar = '`';
        } else {
            $this->quoteIdentifierChar = '"';
        }
        return $this;
    }

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
        $ret = $connection->query($queryString);

        // Save insert ID if instance of insert
        if( $query instanceof Insert ) {
            $this->insertId = $connection->lastInsertId();
        }

        // Save affected rows
        if( $ret instanceof PDOStatement ) {
            $this->affectedRows = $ret->rowCount();
        }

        // Handle result
        if( $ret !== false ) {
            if( $query instanceof Insert ) {
                return $this->getInsertId();
            } else if( $query instanceof Update || $query instanceof Delete ) {
                return $this->getAffectedRows();
            } else if( $ret instanceof PDOStatement ) {
                return new Result($ret);
            }
            return $ret;
        } else {
            $err = $connection->errorInfo();
            $message = sprintf(
                "%s: %s\n%s",
                $connection->errorCode(),
                $err[2],
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

    public function quote($value)
    {
        if( $value instanceof Expression ) {
            return (string) $value;
        } else if ( null === $value ) {
            return 'NULL';
        }

        switch( gettype($value) ) {
            case 'boolean': $type = PDO::PARAM_BOOL; break;
            case 'integer': $type = PDO::PARAM_INT; break;
            case 'float': $type = PDO::PARAM_STR; break;
            case 'object': settype($value, 'string'); break;
            default: $type = PDO::PARAM_STR; break;
        }
        return $this->connection->quote($value, $type);
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
