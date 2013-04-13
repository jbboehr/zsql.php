<?php

namespace zsql;

class Expression
{
  /**
   * The expression
   * 
   * @var string 
   */
  protected $_expr;
  
  /**
   * Constructor
   * 
   * @param string $expr
   */
  public function __construct($expr)
  {
    $this->_expr = $expr;
  }
  
  /**
   * Convert to string
   * 
   * @return string
   */
  public function __toString()
  {
    return (string) $this->_expr;
  }
}