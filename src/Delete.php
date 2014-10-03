<?php

namespace zsql;

/**
 * Delete query generator
 */
class Delete extends ExtendedQuery
{
  /**
   * Assemble parts
   * 
   * @return void
   */
  protected function assemble()
  { 
    $this->push('DELETE FROM')
         ->pushTable()
         ->pushWhere()
         ->pushOrder()
         ->pushLimit();
    
    $this->query = join(' ', $this->parts);
  }
  
  /**
   * Alias for {@link Query::table()}
   * 
   * @param string $table
   * @return \zsql\Delete
   */
  public function from($table)
  {
    $this->table($table);
    return $this;
  }
}
