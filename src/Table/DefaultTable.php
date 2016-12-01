<?php

namespace zsql\Table;

use zsql\Adapter;
use zsql\Exception\LogicException;
use zsql\QueryBuilder\Delete;
use zsql\QueryBuilder\Insert;
use zsql\QueryBuilder\Select;
use zsql\QueryBuilder\Update;
use zsql\Row;
use zsql\Table as TableInterface;

/**
 * Class DefaultTable
 * @package zsql\Table
 */
class DefaultTable implements TableInterface
{
    /**
     * @var Adapter
     */
    protected $database;

    /**
     * Name of the primary key column for a table, if one exists
     *
     * @var string
     */
    protected $primaryKey;

    /**
     * Class to use for results
     *
     * @var string
     */
    protected $resultClass = 'zsql\\Row\\DefaultRow';

    /**
     * @var array|null
     */
    protected $resultParams;

    /**
     * Name of the table associated with the model
     *
     * @var string
     */
    protected $tableName;

    /**
     * Find a record by primary key
     *
     * @param mixed $identity
     * @return Row
     * @throws LogicException
     */
    public function find($identity)
    {
        if( !$this->primaryKey ) {
            throw new LogicException('No primary key!');
        }
        return $this->select()
                ->where($this->primaryKey, $identity)
                ->limit(1)
                ->query()
                ->fetchRow();
    }

    /**
     * Find many records by identity
     *
     * @param array $identities
     * @return array
     * @throws LogicException
     */
    public function findMany(array $identities)
    {
        if( !$this->primaryKey ) {
            throw new LogicException('No primary key!');
        }
        return $this->select()
                ->whereIn($this->primaryKey, $identities)
                ->query()
                ->fetchAll();
    }

    /**
     * Getter method for the database adapter
     *
     * @return Adapter
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Setter method for the database adapter
     *
     * @param Adapter $database
     * @return $this
     */
    public function setDatabase(Adapter $database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * Getter method for the $tableName property.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Setter method for the $tableName property.
     *
     * @param string $table
     * @return $this
     */
    public function setTableName($table)
    {
        $this->tableName = $table;
        return $this;
    }

    /**
     * Getter method for the $primaryKey property
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Setter method for the $primaryKey property
     *
     * @param string $primaryKey
     * @return $this
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    /**
     * Helper function the provides the \zsql\Select object with the table name
     * pre-populated.
     *
     * @return Select
     * @throws LogicException
     */
    public function select()
    {
        if( !$this->tableName ) {
            throw new LogicException('No table name specified');
        }
        $q = $this->database->select();
        $q->table($this->tableName);
        if( $this->resultClass ) {
            $q->setResultClass($this->resultClass);
            $q->setResultParams($this->resultParams);
        }
        return $q;
    }

    /**
     * Insert a row into the model's table. If no table name is specified an
     * exception will be thrown
     *
     * @return Insert
     * @throws LogicException
     */
    public function insert()
    {
        if( !$this->tableName ) {
            throw new LogicException('No table name specified');
        }
        return $this->database->insert()
                ->table($this->tableName);
    }

    /**
     * Update existing rows in the model's table. If no table name is specified an
     * exception will be thrown
     *
     * @return Update
     * @throws LogicException
     */
    public function update()
    {
        if( !$this->tableName ) {
            throw new LogicException('No table name specified');
        }
        return $this->database->update()
                ->table($this->tableName);
    }

    /**
     * Deletes existing rows from the model's table. If no table name is specified
     * an exception will be thrown
     *
     * @return Delete
     * @throws LogicException
     */
    public function delete()
    {
        if( !$this->tableName ) {
            throw new LogicException('No table name specified');
        }
        return $this->database->delete()
                ->table($this->tableName);
    }
}
