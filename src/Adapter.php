<?php

namespace zsql;

use zsql\QueryBuilder\Query;
use zsql\QueryBuilder\Delete;
use zsql\QueryBuilder\Insert;
use zsql\QueryBuilder\Select;
use zsql\QueryBuilder\Update;

interface Adapter
{
    /**
     * Get affected rows
     *
     * @return integer
     */
    public function getAffectedRows();

    /**
     * Get the last insert ID
     *
     * @return integer
     */
    public function getInsertId();

    /**
     * Gets number of queries run using this adapter.
     *
     * @return integer
     */
    public function getQueryCount();

    /**
     * Executes an SQL query
     *
     * @param string|Query $query
     * @return Result|integer|boolean
     * @throws Exception
     */
    public function query($query);

    /**
     * Quote a value
     *
     * @param string|Expression $string
     * @return string
     */
    public function quote($string);

    /**
     * Quote an identifier
     *
     * @param string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier);

    /**
     * Wrapper for Select
     *
     * @return Select
     */
    public function select();

    /**
     * Wrapper for Insert
     *
     * @return Insert
     */
    public function insert();

    /**
     * Wrapper for Update
     *
     * @return Update
     */
    public function update();

    /**
     * Wrapper for Delete
     *
     * @return Delete
     */
    public function delete();

    /**
     * @return boolean
     */
    public function ping();

    public function insertQuery(string $table, array $values);
    public function updateQuery(string $table, array $values, array $where);
    public function deleteQuery(string $table, array $where);
    public function selectQuery(string $table, array $where);
}
