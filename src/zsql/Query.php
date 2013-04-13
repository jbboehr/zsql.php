<?php

namespace zsql;

abstract class Query
{
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
  abstract public function toString();
  
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
      } else {
        // ignore because it will just be ? by itself
        continue;
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
