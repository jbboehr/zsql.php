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
   * Assemble parts
   * 
   * @return void
   */
  protected function _assemble()
  { 
    $this->_push('DELETE FROM')
         ->_pushTable()
         ->_pushWhere()
         ->_pushOrder()
         ->_pushLimit();
    
    $this->_query = join(' ', $this->_parts);
  }
}

