<?php

namespace zsql;

abstract class Query
{
  /**
   * Toggle whether to interpolate parameters into the query
   * 
   * @var boolean
   */
  protected $_interpolation = false;
  
  /**
   * The table
   * 
   * @var string
   */
  protected $_table;
  
  /**
   * The parameters to bind
   * 
   * @var array
   */
  protected $_params;
  
  /**
   * Array of query fragments
   * 
   * @var array
   */
  protected $_parts;
  
  /**
   * Callback to quote a string
   * 
   * @var callback
   */
  protected $_quoteCallback;
  
  /**
   * The current query
   * 
   * @var string
   */
  protected $_query;
  
  /**
   * The function to proxy calls to query to
   * 
   * @var callback
   */
  protected $_queryCallback;
  
  /**
   * The character to use to quote strings
   * 
   * @var string
   */
  protected $_quoteChar = "'";
  
  /**
   * The character to use to quote identifiers
   * 
   * @var string
   */
  protected $_quoteIdentifierChar = '`';
  
  /**
   * Constructor 
   * 
   * @param callback $queryCallback
   */
  public function __construct($queryCallback = null)
  {
    if( $queryCallback ) {
      $this->_queryCallback = $queryCallback;
    }
  }
  
  /**
   * Toggle whether to interpolate parameters into the query
   * 
   * @return \zsql\Query
   */
  public function interpolation($interpolation = true)
  {
    $this->_interpolation = (bool) $interpolation;
    return $this;
  }
  
  /**
   * Get the array of parts
   * 
   * @return array
   */
  public function parts()
  {
    return (array) $this->_parts;
  }
  
  /**
   * Get the parameters
   * 
   * @return array
   */
  public function params()
  {
    return (array) $this->_params;
  }
  
  /**
   * Proxy to query callback
   * 
   * @return mixed
   * @throws \zsql\Exception
   */
  public function query()
  {
    if( !$this->_queryCallback ) {
      throw new \zsql\Exception('query() called when no callback set');
    }
    $query = $this->toString();
    $params = $this->params();
    if( $this->_interpolation ) {
      return call_user_func($this->_queryCallback, $query);
    } else {
      return call_user_func($this->_queryCallback, $query, $params);
    }
  }
  
  /**
   * Set the function to use to quote strings
   * 
   * @param type $callback
   * @return \zsql\Query
   * @throws \zsql\Exception
   */
  public function setQuoteCallback($callback)
  {
    if( !is_callable($callback) ) {
      throw new \zsql\Exception('Invalid callback specified');
    }
    $this->_quoteCallback = $callback;
    return $this;
  }
  
  /**
   * Set the table
   * 
   * @param mixed $table
   * @return \zsql\Query
   */
  public function table($table)
  {
    if( $table instanceof Expression ) {
      $this->_table = $table;
    } else {
      $this->_table = (string) $table;
    }
    return $this;
  }
  
  /**
   * Convert to string
   */
  public function toString()
  {
    $this->_parts = array();
    $this->_params = array();
    $this->_assemble();
    $this->_query = join(' ', $this->_parts);
    if( $this->_interpolation ) {
      $this->_interpolate();
    }
    return $this->_query;
  }
  
  /**
   * Assemble parts
   */
  abstract protected function _assemble();
  
  /**
   * Interpolate parameters into query
   * 
   * @throws \zsql\Exception
   */
  protected function _interpolate()
  {
    if( count($this->_params) <= 0 ) {
      return;
    }
    if( !$this->_quoteCallback ) {
      throw new \zsql\Exception('Interpolation not available without setting a quote callback');
    }
    if( substr_count($this->_query, '?') != count($this->_params) ) {
      throw new \zsql\Exception('Parameter count mismatch');
    }
    
    $parts = explode('?', $this->_query);
    $query = $parts[0];
    for( $i = 0, $l = count($this->_params); $i < $l; $i++ ) {
      $query .= call_user_func($this->_quoteCallback, $this->_params[$i]) . $parts[$i+1];
    }
    $this->_query = $query;
  }
  
  /**
   * Push an arbitrary string onto parts
   * 
   * @param string $string
   * @return \zsql\Query
   */
  protected function _push($string)
  {
    $this->_parts[] = $string;
    return $this;
  }
  
  /**
   * Push table onto parts
   * 
   * @return \zsql\Query
   * @throws \zsql\Exception
   */
  protected function _pushTable()
  {
    if( empty($this->_table) ) {
      throw new \zsql\Exception('No table specified');
    }
    $this->_parts[] = $this->_quoteIdentifierIfNotExpression($this->_table);
    return $this;
  }
  
  /**
   * Push values onto parts
   * 
   * @return \zsql\Query
   * @throws \zsql\Exception
   */
  protected function _pushValues()
  {
    if( empty($this->_values) ) {
      throw new \zsql\Exception('No values specified');
    }
    foreach( $this->_values as $key => $value ) {
      if( !is_int($key) ) {
        $this->_parts[] = $this->_quoteIdentifierIfNotExpression($key);
        $this->_parts[] = '=';
      }
      if( $value instanceof Expression ) {
        $this->_parts[] = (string) $value;
      } else if( !is_int($key) ) {
        $this->_parts[] = '?';
        $this->_params[] = $value;
      }
      $this->_parts[] = ',';
    }
    array_pop($this->_parts);
    
    return $this;
  }
  
  /**
   * Quotes an identifier
   * 
   * @param string $identifier
   * @return string
   */
  protected function _quoteIdentifier($identifier)
  {
    $c = $this->_quoteIdentifierChar;
    return $c . str_replace('.', 
        $c . '.' . $c, 
        str_replace($c, $c . $c, $identifier)) . $c;
  }
  
  /**
   * Quotes an identifier if not an {{\zsql\Expression}}
   * 
   * @param mixed $identifier
   * @return string
   */
  protected function _quoteIdentifierIfNotExpression($identifier)
  {
    if( $identifier instanceof Expression ) {
      return (string) $identifier;
    } else {
      return $this->_quoteIdentifier($identifier);
    }
  }
  
  /**
   * Magic string conversion. Alias of {{\zsql\Query::toString()}}
   * 
   * @return string
   */
  public function __toString()
  {
    try {
      return $this->toString();
    } catch( Exception $e ) {
      trigger_error($e->getMessage(), E_USER_WARNING);
      return '';
    }
  }
}
