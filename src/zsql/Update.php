<?php

namespace zsql;

class Update extends ExtendedQuery
{
  /**
   * Values
   * 
   * @var array
   */
  protected $_values;
  
  /**
   * Alias for {{\zsql\Update::value()}} or {{\zsql\Update::values()}}
   * 
   * @param mixed $key
   * @param mixed $value
   * @return \zsql\Update
   */
  public function set($key, $value = null)
  {
    if( is_array($key) ) {
      return $this->values($key);
    } else {
      return $this->value($key, $value);
    }
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
    
    $this->_push('UPDATE')
         ->_pushTable()
         ->_push('SET')
         ->_pushValues()
         ->_pushWhere()
         ->_pushOrder()
         ->_pushLimit();
    
    return join(' ', $this->_parts);
  }
  
  /**
   * Alias for {{\zsql\Update::table()}} and {{\zsql\Update::values()}}
   * 
   * @param string $table
   * @param array $values
   * @return \zsql\Update
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
   * @return \zsql\Update
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
   * @return \zsql\Update
   */
  public function values(array $values)
  {
    $this->_values = $values;
    return $this;
  }
}
