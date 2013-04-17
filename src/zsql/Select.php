<?php

namespace zsql;

class Select extends ExtendedQuery
{
  /**
   * Columns clause
   * 
   * @var mixed
   */
  protected $_columns;
  
  /**
   * Distinct clause
   * 
   * @var boolean
   */
  protected $_distinct;
  
  /**
   * Index hint clause (column)
   * 
   * @var string
   */
  protected $_hint;
  
  /**
   * Index hint clause (mode)
   * 
   * @var string
   */
  protected $_hintMode;
  
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
      $this->_columns = $columns;
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
    $this->_distinct = (bool) $distinct;
    return $this;
  }
  
  /**
   * Alias for {{\zsql\Query::table()}} and {{\zsql\Select::columns()}}
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
    $this->_hint = $columns;
    $this->_hintMode = $mode;
    return $this;
  }
  
  /**
   * Alias for {{\zsql\Select::columns()}}
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
   * Assemble parts
   * 
   * @return void
   */
  protected function _assemble()
  {
    $this->_push('SELECT')
         ->_pushDistinct()
         ->_pushColumns()
         ->_push('FROM')
         ->_pushTable()
         ->_pushHint()
         ->_pushWhere()
         ->_pushGroup()
         ->_pushOrder()
         ->_pushLimit();
  }
  
  /**
   * Push columns onto parts
   * 
   * @return \zsql\Select
   */
  protected function _pushColumns()
  {
    if( !$this->_columns || $this->_columns == '*' ) {
      $this->_parts[] = '*';
    } else if( is_array($this->_columns) ) {
      $cols = array();
      foreach( $this->_columns as $col ) {
        $cols[] = $this->_quoteIdentifierIfNotExpression($col);
      }
      $this->_parts[] = join(', ', $cols);
    } else if( is_string($this->_columns) ) {
      $this->_parts[] = $this->_quoteIdentifierIfNotExpression($this->_columns);
    } else if( $this->_columns instanceof Expression ) {
      $this->_parts[] = (string) $this->_columns;
    }
    return $this;
  }
  
  /**
   * Push distinct onto parts
   * 
   * @return \zsql\Select
   */
  protected function _pushDistinct()
  {
    if( $this->_distinct ) {
      $this->_parts[] = 'DISTINCT';
    }
    return $this;
  }
  
  /**
   * Push hint onto parts
   * 
   * @return \zsql\Select
   */
  protected function _pushHint()
  {
    if( $this->_hint ) {
      $this->_parts[] = $this->_hintMode ?: 'USE';
      $this->_parts[] = 'INDEX';
      if( is_array($this->_hint) ) {
        $this->_parts[] = '(' . join(', ', array_map(array($this, '_quoteIdentifierIfNotExpression'), $this->_hint)) . ')';
      } else {
        $this->_parts[] = '(' . $this->_quoteIdentifierIfNotExpression($this->_hint) . ')';
      }
    }
    return $this;
  }
}
