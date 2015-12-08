<?php

namespace zsql\QueryBuilder;

use Exception as BaseException;
use Throwable;
use zsql\Expression;
use zsql\Adapter\Adapter;
use zsql\Result\Result;

/**
 * Class Query
 * Base query builder
 * @package zsql\QueryBuilder
 */
abstract class Query
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * The database adapter for this query
     *
     * @var Adapter
     */
    protected $database;

    /**
     * Toggle whether to interpolate parameters into the query
     *
     * @var boolean
     */
    protected $interpolation = false;

    /**
     * The table
     *
     * @var string
     */
    protected $table;

    /**
     * The parameters to bind
     *
     * @var array
     */
    protected $params;

    /**
     * Array of query fragments
     *
     * @var array
     */
    protected $parts;

    /**
     * Array of callbacks to execute after query is executed
     *
     * @var callable[]
     */
    protected $postExecuteCallbacks;

    /**
     * Array of callbacks to execute before the query is executed
     *
     * @var callable[]
     */
    protected $preExecuteCallbacks;

    /**
     * Callback to quote a string
     *
     * @var callback
     */
    protected $quoteCallback;

    /**
     * The current query
     *
     * @var string
     */
    protected $query;

    /**
     * The function to proxy calls to query to
     *
     * @var callback
     */
    protected $queryCallback;

    /**
     * The character to use to quote strings
     *
     * @var string
     */
    protected $quoteChar = "'";

    /**
     * The character to use to quote identifiers
     *
     * @var string
     */
    protected $quoteIdentifierChar = '`';

    /**
     * Constructor
     *
     * @param callback|Adapter|null $queryCallback
     */
    public function __construct($queryCallback = null)
    {
        if( $queryCallback instanceof Adapter ) {
            $this->database = $queryCallback;
            $this->interpolation();
        } else if( is_callable($queryCallback) ) {
            $this->queryCallback = $queryCallback;
        } else if( $queryCallback !== null ) {
            throw new Exception('Invalid query executor');
        }
    }

    /**
     * Magic string conversion. Alias of {@link Query::toString()}
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->toString();
        } catch( BaseException $e ) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        } catch( Throwable $e ) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        }
    }

    /**
     * A callback to execute when the query is executed
     *
     * @param callable $callable
     * @return $this
     */
    public function after($callable)
    {
        if( is_callable($callable) ) {
            $this->postExecuteCallbacks[] = $callable;
        }
        return $this;
    }

    /**
     * Assemble parts
     */
    abstract protected function assemble();

    /**
     * A callback to execute before the query is executed
     *
     * @param callable $callable
     * @return $this
     */
    public function before($callable)
    {
        if( is_callable($callable) ) {
            $this->preExecuteCallbacks[] = $callable;
        }
        return $this;
    }

    /**
     * Interpolate parameters into query
     *
     * @throws Exception
     */
    protected function interpolateParams()
    {
        if( count($this->params) <= 0 ) {
            return;
        }
        if( $this->quoteCallback ) {
            $cb = $this->quoteCallback;
        } else if( $this->database ) {
            $cb = array($this->database, 'quote');
        } else {
            throw new Exception('Interpolation not available without '
            . 'setting a quote callback or database adapter');
        }
        if( substr_count($this->query, '?') != count($this->params) ) {
            throw new Exception('Parameter count mismatch: ' . $this->query);
        }

        $parts = explode('?', $this->query);
        $query = $parts[0];
        for( $i = 0, $l = count($this->params); $i < $l; $i++ ) {
            $query .= call_user_func($cb, $this->params[$i]) . $parts[$i + 1];
        }
        $this->query = $query;
    }

    /**
     * Toggle whether to interpolate parameters into the query
     *
     * @param boolean $interpolation
     * @return $this
     */
    public function interpolation($interpolation = true)
    {
        $this->interpolation = (bool) $interpolation;
        return $this;
    }

    /**
     * Get the parameters
     *
     * @return array
     */
    public function params()
    {
        return (array) $this->params;
    }

    /**
     * Get the array of parts
     *
     * @return array
     */
    public function parts()
    {
        return (array) $this->parts;
    }

    /**
     * Push an arbitrary string onto parts
     *
     * @param string $string
     * @return void
     */
    protected function push($string)
    {
        $this->parts[] = $string;
    }

    /**
     * Push table onto parts
     *
     * @return void
     * @throws Exception
     */
    protected function pushTable()
    {
        if( empty($this->table) ) {
            throw new Exception('No table specified');
        }
        $this->parts[] = $this->quoteIdentifierIfNotExpression($this->table);
    }

    /**
     * Push values onto parts
     *
     * @return void
     * @throws Exception
     */
    protected function pushValues()
    {
        if( empty($this->values) ) {
            throw new Exception('No values specified');
        }
        foreach( $this->values as $key => $value ) {
            if( !is_int($key) ) {
                $this->parts[] = $this->quoteIdentifierIfNotExpression($key);
                $this->parts[] = '=';
            }
            if( $value instanceof Expression ) {
                $this->parts[] = (string) $value;
            } else if( !is_int($key) ) {
                $this->parts[] = '?';
                $this->params[] = $value;
            }
            $this->parts[] = ',';
        }
        array_pop($this->parts);
    }

    /**
     * Proxy to query callback
     *
     * @return Result|integer|boolean
     * @throws Exception
     */
    public function query()
    {
        // Pre-execute callbacks
        if( !empty($this->preExecuteCallbacks) ) {
            foreach( $this->preExecuteCallbacks as $callable ) {
                call_user_func($callable, $this);
            }
        }
        // Execute
        if( $this->database ) {
            //$this->interpolateParams();
            $result = $this->database->query($this);
        } else if( $this->queryCallback ) {
            $query = $this->toString();
            $params = $this->params();
            if( $this->interpolation ) {
                $result = call_user_func($this->queryCallback, $query);
            } else {
                $result = call_user_func($this->queryCallback, $query, $params);
            }
        } else {
            throw new Exception('query() called when no callback or database adapter set');
        }
        // Post-execute callbacks
        if( !empty($this->postExecuteCallbacks) ) {
            foreach( $this->postExecuteCallbacks as $callable ) {
                call_user_func($callable, $result, $this);
            }
        }
        return $result;
    }

    /**
     * Quotes an identifier
     *
     * @param string $identifier
     * @return string
     */
    protected function quoteIdentifier($identifier)
    {
        $c = $this->quoteIdentifierChar;
        return $c . str_replace(
            '.',
            $c . '.' . $c,
            str_replace($c, $c . $c, $identifier)
        ) . $c;
    }

    /**
     * Quotes an identifier if not an {@link Expression}
     *
     * @param mixed $identifier
     * @return string
     */
    protected function quoteIdentifierIfNotExpression($identifier)
    {
        if( $identifier instanceof Expression ) {
            return (string) $identifier;
        } else {
            return $this->quoteIdentifier($identifier);
        }
    }

    /**
     * Set the function to use to quote strings
     *
     * @param callable $callback
     * @return $this
     * @throws Exception
     */
    public function setQuoteCallback($callback)
    {
        if( !is_callable($callback) ) {
            throw new Exception('Invalid callback specified');
        }
        $this->quoteCallback = $callback;
        return $this;
    }

    /**
     * Set the table
     *
     * @param mixed $table
     * @return $this
     */
    public function table($table)
    {
        if( $table instanceof Expression ) {
            $this->table = $table;
        } else {
            $this->table = (string) $table;
        }
        return $this;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function toString()
    {
        $this->parts = array();
        $this->params = array();
        $this->assemble();
        $this->query = join(' ', $this->parts);
        if( $this->interpolation ) {
            $this->interpolateParams();
        }
        return $this->query;
    }
}
