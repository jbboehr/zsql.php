<?php

namespace zsql;

/**
 * Abstract class for queries that support select-like functionality (delete,
 * select, update)
 */
abstract class ExtendedQuery extends Query
{
    /**
     * Order direction
     *
     * @var string
     */
    protected $direction;

    /**
     * Group by column
     *
     * @var string
     */
    protected $group;

    /**
     * Limit number
     *
     * @var integer
     */
    protected $limit;

    /**
     * Offset number
     *
     * @var integer
     */
    protected $offset;

    /**
     * Order by column
     *
     * @var string
     */
    protected $order;

    /**
     * Where expressions
     *
     * @var array
     */
    protected $where;

    /**
     * Get the specified where
     *
     * @param mixed $key
     * @return mixed
     */
    public function getWhere($key)
    {
        foreach( $this->where as $w ) {
            if( $w[0] === $key ) {
                return $w[1];
            }
        }
        return null;
    }

    /**
     * Set group by
     *
     * @param string $column
     * @return \zsql\ExtendedQuery
     */
    public function group($column)
    {
        $this->group = $column;
        return $this;
    }

    /**
     * Set limit
     *
     * @param integer $limit
     * @param integer $offset
     * @return \zsql\ExtendedQuery
     */
    public function limit($limit, $offset = null)
    {
        $this->limit = (int) $limit;
        if( null !== $offset ) {
            $this->offset = (int) $offset;
        }
        return $this;
    }

    /**
     * Set offset
     *
     * @param integer $offset
     * @param integer $limit
     * @return \zsql\ExtendedQuery
     */
    public function offset($offset, $limit = null)
    {
        $this->offset = (int) $offset;
        if( null !== $limit ) {
            $this->limit = (int) $limit;
        }
        return $this;
    }

    /**
     * Set order
     *
     * @param string $order
     * @param string $direction
     * @return \zsql\ExtendedQuery
     */
    public function order($order, $direction = 'ASC')
    {
        $this->order = $order;
        $this->direction = $direction;
        return $this;
    }

    /**
     * Push limit clause onto parts
     *
     * @return \zsql\ExtendedQuery
     */
    protected function pushLimit()
    {
        if( null !== $this->limit ) {
            if( null !== $this->offset ) {
                $this->parts[] = 'LIMIT ?, ?';
                $this->params[] = $this->offset;
                $this->params[] = $this->limit;
            } else {
                $this->parts[] = 'LIMIT ?';
                $this->params[] = $this->limit;
            }
        }
        return $this;
    }

    /**
     * Push group by clause onto parts
     *
     * @return \zsql\ExtendedQuery
     */
    protected function pushGroup()
    {
        if( $this->group ) {
            $this->parts[] = 'GROUP BY';
            $this->parts[] = $this->quoteIdentifierIfNotExpression($this->group);
        }
        return $this;
    }

    /**
     * Push order by clause onto parts
     *
     * @return \zsql\ExtendedQuery
     */
    protected function pushOrder()
    {
        if( $this->order ) {
            $this->parts[] = 'ORDER BY';
            $this->parts[] = $this->quoteIdentifierIfNotExpression($this->order);
            $this->parts[] = $this->direction == 'DESC' ? 'DESC' : 'ASC';
        }
        return $this;
    }

    /**
     * Push where clause onto parts
     *
     * @return \zsql\ExtendedQuery
     */
    protected function pushWhere()
    {
        if( empty($this->where) ) {
            return $this;
        }

        $this->parts[] = 'WHERE';
        foreach( $this->where as $w ) {
            $where = $w[0];
            $value = isset($w[1]) ? $w[1] : null;
            $type = isset($w[2]) ? $w[2] : null;
            if( $where instanceof Expression ) {
                $this->parts[] = (string) $where;
            } else if( count($w) == 3 ) {
                $this->parts[] = $this->quoteIdentifierIfNotExpression($where);
                $this->parts[] = $type;
                $tmp = '';
                foreach( $value as $val ) {
                    $tmp .= '?, ';
                    $this->params[] = $val;
                }
                $this->parts[] = '(' . substr($tmp, 0, -2) . ')';
            } else if( false !== strpos($where, '?') ) {
                $this->parts[] = $where; //$this->quoteIdentifierIfNotExpression($where);
                $this->params[] = $value;
            } else {
                $this->parts[] = $this->quoteIdentifierIfNotExpression($where);
                $this->parts[] = '=';
                if( $value instanceof Expression ) {
                    $this->parts[] = (string) $value;
                } else {
                    $this->parts[] = '?';
                    $this->params[] = $value;
                }
            }
            $this->parts[] = '&&';
        }
        array_pop($this->parts);
        return $this;
    }

    /**
     * Set where
     *
     * @param mixed $where
     * @param mixed $value
     * @return \zsql\ExtendedQuery
     */
    public function where($where, $value = null)
    {
        $nArgs = func_num_args();
        if( $nArgs >= 2 ) {
            $this->where[] = array($where, $value);
        } else if( $nArgs >= 1 ) {
            $this->where[] = array($where);
        }
        return $this;
    }

    /**
     * Set where expression. Shorthand for:
     * where(new Expression('string'))
     *
     * @param string $where
     * @return \zsql\ExtendedQuery
     */
    public function whereExpr($where)
    {
        $this->where[] = array(new Expression((string) $where));
        return $this;
    }

    /**
     * Set where in
     *
     * @param mixed $where
     * @param array $value
     * @return \zsql\ExtendedQuery
     */
    public function whereIn($where, $value)
    {
        if( !is_array($value) ) {
            $value = (array) $value;
        }
        if( count($value) <= 0 ) {
            $this->where[] = array(new Expression('FALSE'));
        } else {
            $this->where[] = array($where, $value, 'IN');
        }
        return $this;
    }

    /**
     * Set where not in
     *
     * @param mixed $where
     * @param array $value
     * @return \zsql\ExtendedQuery
     */
    public function whereNotIn($where, $value)
    {
        if( !is_array($value) ) {
            $value = (array) $value;
        }
        if( count($value) <= 0 ) {
            $this->where[] = array(new Expression('TRUE'));
        } else {
            $this->where[] = array($where, $value, 'NOT IN');
        }
        return $this;
    }
}
