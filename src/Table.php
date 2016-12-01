<?php

namespace zsql;

use zsql\Adapter\AdapterAwareInterface;
use zsql\QueryBuilder\Delete;
use zsql\QueryBuilder\Insert;
use zsql\QueryBuilder\Select;
use zsql\QueryBuilder\Update;

/**
 * Interface Table
 * @package zsql
 */
interface Table extends AdapterAwareInterface
{
    /**
     * Getter method for the $tableName property.
     *
     * @return string
     */
    public function getTableName();

    /**
     * Setter method for the $tableName property.
     *
     * @param string $table
     * @return $this
     */
    public function setTableName($table);

    /**
     * Getter method for the $primaryKey property
     *
     * @return string
     */
    public function getPrimaryKey();

    /**
     * Setter method for the $primaryKey property
     *
     * @param string $primaryKey
     * @return $this
     */
    public function setPrimaryKey($primaryKey);

    /**
     * Helper function the provides the select object with the table name
     * pre-populated.
     *
     * @return Select
     */
    public function select();

    /**
     * Insert a row into the table. If no table name is specified an
     * exception will be thrown
     *
     * @return Insert
     */
    public function insert();

    /**
     * Update existing rows in the table. If no table name is specified an
     * exception will be thrown
     *
     * @return Update
     */
    public function update();

    /**
     * Deletes existing rows from the table. If no table name is specified
     * an exception will be thrown
     *
     * @return Delete
     */
    public function delete();
}
