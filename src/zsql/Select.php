<?php

namespace zsql;

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
   * Set the columns
   * 
   * @param mixed $columns
   * @return \zsql\Select
   * @throws \zsql\Exception
   */
  public function columns($columns)
  {
    if( is_array($columns) || 
        is_string($columns) ||
        $columns instanceof Expression ) {
      $this->columns = $columns;
    } else {
      throw new \zsql\Exception('Invalid columns parameter');
    }
    return $this;
  }
  
  /**
   * Set distinct clause
   * 
   * @param type $distinct
   * @return \zsql\Select
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
   * @return \zsql\Select
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
   * @return \zsql\Select
   */
  public function hint($columns, $mode = null)
  {
    $this->hint = $columns;
    $this->hintMode = $mode;
    return $this;
  }
  
  /**
   * Alias for {@link Select::columns()}
   * 
   * @param mixed $columns
   * @return \zsql\Select
   */
  public function select($columns)
  {
    $this->columns($columns);
    return $this;
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
   * @return \zsql\Select
   */
  public function setResultClass($class)
  {
    $this->resultClass = $class;
    return $this;
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
         ->pushWhere()
         ->pushGroup()
         ->pushOrder()
         ->pushLimit();
  }
  
  /**
   * Push columns onto parts
   * 
   * @return \zsql\Select
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
   * @return \zsql\Select
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
   * @return \zsql\Select
   */
  protected function pushHint()
  {
    if( $this->hint ) {
      $this->parts[] = $this->hintMode ?: 'USE';
      $this->parts[] = 'INDEX';
      if( is_array($this->hint) ) {
        $this->parts[] = '(' . join(', ', array_map(array($this, 'quoteIdentifierIfNotExpression'), $this->hint)) . ')';
      } else {
        $this->parts[] = '(' . $this->quoteIdentifierIfNotExpression($this->hint) . ')';
      }
    }
    return $this;
  }
}
