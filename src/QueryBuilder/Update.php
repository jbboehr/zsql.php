<?php

namespace zsql\QueryBuilder;

use zsql\Expression;

/**
 * Class Update
 * Update query builder
 * @package zsql\QueryBuilder
 */
class Update extends ExtendedQuery
{
    /**
     * Values
     *
     * @var array
     */
    protected $values;

    /**
     * Assemble parts
     *
     * @return void
     */
    protected function assemble()
    {
        $this->push('UPDATE');
        $this->pushTable();
        $this->push('SET');
        $this->pushSetValues();
        $this->pushWhere();
        $this->pushOrder();
        $this->pushLimit();
    }

    /**
     * Clear current values
     *
     * @return $this
     */
    public function clearValues()
    {
        $this->values = null;
        return $this;
    }

    /**
     * Get a value that has been set
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if( isset($this->values[$key]) ) {
            return $this->values[$key];
        } else {
            return null;
        }
    }

    /**
     * Alias for {@link Update::value()} or {@link Update::values()}
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        if( is_array($key) ) {
            $this->values($key);
        } else {
            $this->value($key, $value);
        }
        return $this;
    }

    /**
     * Alias for {@link Update::table()} and {@link Update::values()}
     *
     * @param string $table
     * @param array $values
     * @return $this
     */
    public function update($table, array $values = null)
    {
        $this->table($table);
        if( !empty($values) ) {
            $this->values($values);
        }
        return $this;
    }

    /**
     * Set a value
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function value($key, $value = null)
    {
        if( null === $value && $key instanceof Expression ) {
            $this->values[] = $key;
        } else {
            $this->values[$key] = $value;
        }
        return $this;
    }

    /**
     * Set values. Merges into existing values.
     *
     * @param array $values
     * @return $this
     */
    public function values(array $values)
    {
        foreach( $values as $k => $v ) {
            $this->value($k, $v);
        }
        return $this;
    }
}
