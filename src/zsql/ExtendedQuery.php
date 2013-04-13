<?php

namespace zsql;

abstract class ExtendedQuery extends Query
{
  /**
   * Order direction
   * 
   * @var string
   */
  protected $_direction;
  
  /**
   * Group by column
   * 
   * @var string
   */
  protected $_group;
  
  /**
   * Limit number
   * 
   * @var integer
   */
  protected $_limit;
  
  /**
   * Offset number
   * 
   * @var integer
   */
  protected $_offset;
  
  /**
   * Order by column
   * 
   * @var string
   */
  protected $_order;
  
  /**
   * Where expressions
   * 
   * @var array
   */
  protected $_where;
  
  /**
   * Set where
   * 
   * @param mixed $where
   * @param mixed $value
   * @return \zsql\ExtendedQuery
   */
  public function where($where, $value = null)
  {
    $nArgs = func_num_args();
    if( $nArgs >= 2 ) {
      $this->_where[] = array($where, $value);
    } else if( $nArgs >= 1 ) {
      $this->_where[] = array($where);
    }
    return $this;
  }
  
  /**
   * Set where in
   * 
   * @param mixed $where
   * @param array $value
   * @return \zsql\ExtendedQuery
   */
  public function whereIn($where, $value)
  {
    if( !is_array($value) ) {
      $value = (array) $value;
    }
    if( count($value) <= 0 ) {
      $this->_where[] = array(new Expression('FALSE'));
    } else {
      $this->_where[] = array($where, $value, 'IN');
    }
    return $this;
  }
  
  /**
   * Set where expression. Shorthand for:
   * where(new Expression('string'))
   * 
   * @param string $where
   * @return \zsql\ExtendedQuery
   */
  public function whereExpr($where)
  {
    $this->_where[] = array(new Expression((string) $where));
    return $this;
  }
  
  /**
   * Set group by
   * 
   * @param string $column
   * @return \zsql\ExtendedQuery
   */
  public function group($column)
  {
    $this->_group = $column;
    return $this;
  }
  
  /**
   * Set order
   * 
   * @param string $order
   * @param string $direction
   * @return \zsql\ExtendedQuery
   */
  public function order($order, $direction = 'ASC')
  {
    $this->_order = $order;
    $this->_direction = $direction;
    return $this;
  }
  
  /**
   * Set limit
   * 
   * @param integer $limit
   * @param integer $offset
   * @return \zsql\ExtendedQuery
   */
  public function limit($limit, $offset = null)
  {
    $this->_limit = (int) $limit;
    if( null !== $offset ) {
      $this->_offset = (int) $offset;
    }
    return $this;
  }
  
  /**
   * Set offset
   * 
   * @param integer $offset
   * @param integer $limit
   * @return \zsql\ExtendedQuery
   */
  public function offset($offset, $limit = null)
  {
    $this->_offset = (int) $offset;
    if( null !== $limit ) {
      $this->_limit = (int) $limit;
    }
    return $this;
  }
  
  /**
   * Push where clause onto parts
   * 
   * @return \zsql\ExtendedQuery
   */
  protected function _pushWhere()
  {
    if( empty($this->_where) ) {
      return $this;
    }
    
    $this->_parts[] = 'WHERE';
    foreach( $this->_where as $w ) {
      $where = $w[0];
      $value = @$w[1];
      $type = @$w[2];
      if( $where instanceof Expression ) {
        $this->_parts[] = (string) $where;
      } else if( count($w) == 3 ) {
        $this->_parts[] = $this->_quoteIdentifierIfNotExpression($where);
        $this->_parts[] = $type;
        $tmp = '';
        foreach( $value as $val ) {
          $tmp .= '?, ';
          $this->_params[] = $val;
        }
        $this->_parts[] = '(' . substr($tmp, 0, -2) . ')';
      } else if( false !== strpos($where, '?') ) {
        $this->_parts[] = $where; //$this->_quoteIdentifierIfNotExpression($where);
        $this->_params[] = $value;
      } else {
        $this->_parts[] = $this->_quoteIdentifierIfNotExpression($where);
        $this->_parts[] = '=';
        if( $value instanceof Expression ) {
          $this->_parts[] = (string) $value;
        } else {
          $this->_parts[] = '?';
          $this->_params[] = $value;
        }
      }
      $this->_parts[] = '&&';
    }
    array_pop($this->_parts);
    return $this;
  }
  
  /**
   * Push group by clause onto parts
   * 
   * @return \zsql\ExtendedQuery
   */
  protected function _pushGroup()
  {
    if( $this->_group ) {
      $this->_parts[] = 'GROUP BY';
      $this->_parts[] = $this->_quoteIdentifierIfNotExpression($this->_group);
    }
    return $this;
  }
  
  /**
   * Push order by clause onto parts
   * 
   * @return \zsql\ExtendedQuery
   */
  protected function _pushOrder()
  {
    if( $this->_order ) {
      $this->_parts[] = 'ORDER BY';
      $this->_parts[] = $this->_quoteIdentifierIfNotExpression($this->_order);
      $this->_parts[] = $this->_direction == 'DESC' ? 'DESC' : 'ASC';
    }
    return $this;
  }
  
  /**
   * Push limit clause onto parts
   * 
   * @return \zsql\ExtendedQuery
   */
  protected function _pushLimit()
  {
    if( null !== $this->_limit ) {
      if( null !== $this->_offset ) {
        $this->_parts[] = 'LIMIT ?, ?';
        $this->_params[] = $this->_offset;
        $this->_params[] = $this->_limit;
      } else {
        $this->_parts[] = 'LIMIT ?';
        $this->_params[] = $this->_limit;
      }
    }
    return $this;
  }
}