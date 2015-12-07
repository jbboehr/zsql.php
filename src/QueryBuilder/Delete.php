<?php

namespace zsql\QueryBuilder;

/**
 * Class Delete
 * Delete query builder
 * @package zsql\QueryBuilder
 */
class Delete extends ExtendedQuery
{
    /**
     * Assemble parts
     *
     * @return void
     */
    protected function assemble()
    {
        $this->push('DELETE FROM')
            ->pushTable()
            ->pushWhere()
            ->pushOrder()
            ->pushLimit();

        $this->query = join(' ', $this->parts);
    }

    /**
     * Alias for {@link Query::table()}
     *
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        $this->table($table);
        return $this;
    }
}
