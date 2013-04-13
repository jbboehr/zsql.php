<?php

namespace zsql;

class Delete extends ExtendedQuery
{
  /**
   * Alias for table()
   * 
   * @param string $table
   * @return \zsql\Delete
   */
  public function from($table)
  {
    $this->table($table);
    return $this;
  }
  
  /**
   * Convert to string
   * 
   * @return string
   */
  public function toString()
  {
    $this->_parts = array();
    $this->_params = array();
    
    $this->_push('DELETE FROM')
         ->_pushTable()
         ->_pushWhere()
         ->_pushOrder()
         ->_pushLimit();
    
    return join(' ', $this->_parts);
  }
}

