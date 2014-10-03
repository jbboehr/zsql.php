<?php

namespace zsql;

/**
 * Represents a string that should not be quoted
 */
class Expression
{
  /**
   * The expression
   * 
   * @var string 
   */
  protected $expression;
  
  /**
   * Constructor
   * 
   * @param string $expr
   */
  public function __construct($expr)
  {
    $this->expression = $expr;
  }
  
  /**
   * Convert to string
   * 
   * @return string
   */
  public function __toString()
  {
    return (string) $this->expression;
  }
}
