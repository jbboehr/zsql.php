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
   * Set on duplicate key update clause
   * 
   * @var array
   */
  protected $_onDuplicateKeyUpdate;
  
  /**
   * Use replace instead of insert
   * 
   * @var boolean
   */
  protected $_replace;
  
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
   * Set on duplicate key update clause
   * 
   * @param array $values
   */
  public function onDuplicateKeyUpdate($key, $value = null)
  {
    if( func_num_args() == 2 ) {
      $this->_onDuplicateKeyUpdate[$key] = $value;
    } else if( $key instanceof Expression ) {
      $this->_onDuplicateKeyUpdate[] = $key;
    } else if( is_array($key) ) {
      $this->_onDuplicateKeyUpdate = $key;
    }
    return $this;
  }
  
  /**
   * Use replace instead of insert
   * 
   * @param boolean $replace
   * @return \zsql\Insert
   */
  public function replace($replace = true)
  {
    $this->_replace = (bool) $replace;
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
   * Assemble parts
   * 
   * @return void
   */
  protected function _assemble()
  {
    $this->_push($this->_replace ? 
                  'REPLACE' : 
                  'INSERT')
         ->_pushIgnoreDelayed()
         ->_push('INTO')
         ->_pushTable()
         ->_push('SET')
         ->_pushValues()
         ->_pushOnDuplicateKeyUpdate();
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
    if( $this->_ignore && !$this->_replace ) {
      $this->_parts[] = 'IGNORE';
    }
    return $this;
  }
  
  /**
   * Push on duplicate key update clause
   * 
   * @return \zsql\Insert
   */
  protected function _pushOnDuplicateKeyUpdate()
  {
    if( $this->_onDuplicateKeyUpdate ) {
      $this->_parts[] = 'ON DUPLICATE KEY UPDATE';
      $tmp = $this->_values;
      $this->_values = $this->_onDuplicateKeyUpdate;
      $this->_pushValues();
      $this->_values = $tmp;
    }
    return $this;
  }
}
