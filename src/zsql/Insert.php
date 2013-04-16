<?php

namespace zsql;

class Insert extends Query
{
  /**
   * Set delayed clause
   * 
   * @var boolean
   */
  protected $_delayed;
  
  /**
   * Set ignore clause
   * 
   * @var boolean
   */
  protected $_ignore;
  
  /**
   * Values
   * 
   * @var array
   */
  protected $_values;
  
  /**
   * Set delayed clause
   * 
   * @param boolean $delayed
   * @return \zsql\Insert
   */
  public function delayed($delayed = true)
  {
    $this->_delayed = (bool) $delayed;
    return $this;
  }
  
  /**
   * Set ignore clause
   * 
   * @param boolean $ignore
   * @return \zsql\Insert
   */
  public function ignore($ignore = true)
  {
    $this->_ignore = (bool) $ignore;
    return $this;
  }
  
  /**
   * Alias for {{\zsql\Insert::values()}}
   * 
   * @param array $values
   * @return \zsql\Insert
   */
  public function insert($values)
  {
    $this->values($values);
    return $this;
  }
  
  /**
   * Alias for {{\zsql\Query::table()}}
   * 
   * @param string $table
   * @return \zsql\Insert
   */
  public function into($table)
  {
    $this->table($table);
    return $this;
  }
  
  /**
   * Alias for {{\zsql\Insert::value()}} or {{\zsql\Insert::values()}}
   * 
   * @param mixed $key
   * @param mixed $value
   * @return \zsql\Insert
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
   * Convert to string
   * 
   * @return string
   * @throws \zsql\Exception
   */
  public function toString()
  {
    $this->_parts = array();
    $this->_params = array();
    
    $this->_push('INSERT')
         ->_pushIgnoreDelayed()
         ->_push('INTO')
         ->_pushTable()
         ->_push('SET')
         ->_pushValues();
    
    return join(' ', $this->_parts);
  }
  
  /**
   * Set a value
   * 
   * @param mixed $key
   * @param mixed $value
   * @return \zsql\Insert
   */
  public function value($key, $value = null)
  {
    if( null === $value && $key instanceof Expression ) {
      $this->_values[] = $key;
    } else {
      $this->_values[$key] = $value;
    }
    return $this;
  }
  
  /**
   * Set values
   * 
   * @param array $values
   * @return \zsql\Insert
   */
  public function values(array $values)
  {
    $this->_values = $values;
    return $this;
  }
  
  /**
   * Push ignore or delayed onto parts
   * 
   * @return \zsql\Insert
   */
  protected function _pushIgnoreDelayed()
  {
    if( $this->_delayed ) {
      $this->_parts[] = 'DELAYED';
    }
    if( $this->_ignore ) {
      $this->_parts[] = 'IGNORE';
    }
    return $this;
  }
}