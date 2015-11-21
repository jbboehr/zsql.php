<?php

namespace zsql\QueryBuilder;

use zsql\Expression;
use zsql\Result\Result;
use zsql\Scanner\ScannerGenerator;
use zsql\Scanner\ScannerIterator;

/**
 * Select query generator
 */
class Select extends ExtendedQuery
{
    /**
     * Columns clause
     *
     * @var mixed
     */
    protected $columns;

    /**
     * Distinct clause
     *
     * @var boolean
     */
    protected $distinct;

    /**
     * Index hint clause (column)
     *
     * @var string
     */
    protected $hint;

    /**
     * Index hint clause (mode)
     *
     * @var string
     */
    protected $hintMode;

    /**
     * The result class
     *
     * @var string
     */
    protected $resultClass;

    /**
     * @var array
     */
    protected $resultParams;

    protected $joins;
    private $currentJoin;

    /**
     * Set the columns
     *
     * @param mixed $columns
     * @return $this
     * @throws Exception
     */
    public function columns($columns)
    {
        if( is_array($columns) ||
            is_string($columns) ||
            $columns instanceof Expression ) {
            $this->columns = $columns;
        } else {
            throw new Exception('Invalid columns parameter');
        }
        return $this;
    }

    /**
     * Create a table scanner iterator object
     *
     * @return ScannerGenerator|ScannerIterator
     */
    public function scan()
    {
        // @codeCoverageIgnoreStart
        if( PHP_VERSION_ID < 50500 || defined('HHVM_VERSION') ) {
            return new ScannerIterator($this);
        }
        // @codeCoverageIgnoreEnd
        return new ScannerGenerator($this);
    }

    /**
     * Set distinct clause
     *
     * @param boolean $distinct
     * @return $this
     */
    public function distinct($distinct = true)
    {
        $this->distinct = (bool) $distinct;
        return $this;
    }

    /**
     * Alias for {@link Query::table()} and {@link Select::columns()}
     *
     * @param mixed $table
     * @param mixed $columns
     * @return $this
     */
    public function from($table, $columns = null)
    {
        $this->table($table);
        if( !empty($columns) ) {
            $this->columns($columns);
        }
        return $this;
    }

    /**
     * Hint at index to use
     *
     * @param mixed $columns
     * @param string $mode
     * @return $this
     */
    public function hint($columns, $mode = null)
    {
        $this->hint = $columns;
        $this->hintMode = $mode;
        return $this;
    }

    /**
     * Join to a table
     *
     * @param string $table
     * @return $this
     */
    public function join($table)
    {
        if( !empty($this->currentJoin) ) {
            $this->joins[] = $this->currentJoin;
        }
        $this->currentJoin = array();
        $this->currentJoin['table'] = $table;
        $this->currentJoin['direction'] = 'INNER';
        return $this;
    }

    /**
     * Set the current join direction to left
     *
     * @return $this
     */
    public function left()
    {
        $this->currentJoin['direction'] = 'LEFT';
        return $this;
    }

    /**
     * Set the current join direction to right
     *
     * @return $this
     */
    public function right()
    {
        $this->currentJoin['direction'] = 'RIGHT';
        return $this;
    }

    /**
     * Set the current join direction to inner
     *
     * @return $this
     */
    public function inner()
    {
        $this->currentJoin['direction'] = 'INNER';
        return $this;
    }

    /**
     * Set the current join direction to outer
     *
     * @return $this
     */
    public function outer()
    {
        $this->currentJoin['direction'] = 'OUTER';
        return $this;
    }

    /**
     * Set the current join relation. Takes one to three args:
     *  - One arg: expression
     *  - Two args: (tableA.columnA) = (tableB.columnB) (quoted)
     *  - Three args: (tableA.columnA) (operator) (tableB.columnB) (quoted)
     *
     * @return $this
     */
    public function on()
    {
        switch( func_num_args() ) {
            case 1:
                $this->currentJoin['expr'] = func_get_arg(0);
                break;
            case 2:
                $this->currentJoin['expr'] = $this->quoteIdentifier(func_get_arg(0))
                    . ' = '
                    . $this->quoteIdentifier(func_get_arg(1));
                break;
            case 3:
                $this->currentJoin['expr'] = $this->quoteIdentifier(func_get_arg(0))
                    . ' ' . func_get_arg(1) . ' '
                    . $this->quoteIdentifier(func_get_arg(2));
                break;
            default:
                throw new Exception('Please specify 1-3 arguments to on');
        }
        return $this;
    }

    /**
     * Alias for {@link Select::columns()}
     *
     * @param mixed $columns
     * @return $this
     */
    public function select($columns)
    {
        $this->columns($columns);
        return $this;
    }

    /**
     * Get the current set limit
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get the current set offset
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Get the result class
     *
     * @return string
     */
    public function getResultClass()
    {
        return $this->resultClass;
    }

    /**
     * Set result class
     *
     * @param string $class
     * @return $this
     */
    public function setResultClass($class)
    {
        $this->resultClass = $class;
        return $this;
    }

    /**
     * Get the result params
     *
     * @return array
     */
    public function getResultParams()
    {
        return $this->resultParams;
    }

    /**
     * Set result params
     *
     * @param array $params
     * @return $this
     */
    public function setResultParams(array $params = null)
    {
        $this->resultParams = $params;
        return $this;
    }

    /**
     * Execute a query
     *
     * @return Result
     */
    public function query()
    {
        $result = parent::query();
        if( $this->resultClass ) {
            $result->setResultClass($this->getResultClass());
            $result->setResultParams($this->getResultParams());
        }
        return $result;
    }

    /**
     * Assemble parts
     *
     * @return void
     */
    protected function assemble()
    {
        $this->push('SELECT')
            ->pushDistinct()
            ->pushColumns()
            ->push('FROM')
            ->pushTable()
            ->pushHint()
            ->pushJoin()
            ->pushWhere()
            ->pushGroup()
            ->pushOrder()
            ->pushLimit();
    }

    /**
     * Push columns onto parts
     *
     * @return $this
     */
    protected function pushColumns()
    {
        if( !$this->columns || $this->columns == '*' ) {
            $this->parts[] = '*';
        } else if( is_array($this->columns) ) {
            $cols = array();
            foreach( $this->columns as $col ) {
                $cols[] = $this->quoteIdentifierIfNotExpression($col);
            }
            $this->parts[] = join(', ', $cols);
        } else if( is_string($this->columns) ) {
            $this->parts[] = $this->quoteIdentifierIfNotExpression($this->columns);
        } else if( $this->columns instanceof Expression ) {
            $this->parts[] = (string) $this->columns;
        }
        return $this;
    }

    /**
     * Push distinct onto parts
     *
     * @return $this
     */
    protected function pushDistinct()
    {
        if( $this->distinct ) {
            $this->parts[] = 'DISTINCT';
        }
        return $this;
    }

    /**
     * Push hint onto parts
     *
     * @return $this
     */
    protected function pushHint()
    {
        if( $this->hint ) {
            $this->parts[] = $this->hintMode ? : 'USE';
            $this->parts[] = 'INDEX';
            if( is_array($this->hint) ) {
                $this->parts[] = '(' . join(', ', array_map(array($this, 'quoteIdentifierIfNotExpression'), $this->hint)) . ')';
            } else {
                $this->parts[] = '(' . $this->quoteIdentifierIfNotExpression($this->hint) . ')';
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function pushJoin()
    {
        if( !empty($this->currentJoin) ) {
            $this->joins[] = $this->currentJoin;
        }
        $this->currentJoin = array();
        if( !empty($this->joins) ) {
            foreach( $this->joins as $join ) {
                $this->parts[] = $join['direction'];
                $this->parts[] = 'JOIN';
                $this->parts[] = $this->quoteIdentifier($join['table']);
                $this->parts[] = 'ON';
                $this->parts[] = $join['expr'];
            }
        }
        return $this;
    }
}
