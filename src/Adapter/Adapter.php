<?php

namespace zsql\Adapter;

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
     * @return Result|mixed
     */
    public function query($query);

    /**
     * Quote a value
     *
     * @param $string
     * @return string
     */
    public function quote($string);

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
}
