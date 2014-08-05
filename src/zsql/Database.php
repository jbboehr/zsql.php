<?php

namespace zsql;

/**
 * Import classes from the global namespace
 */
use \mysqli;
use \mysqli_result;

class Database 
{
  /**
   * @var integer
   */
  protected $affectedRows;
  
  /**
   * @var mysqli
   */
  protected $connection;
  
  /**
   * @var integer
   */
  protected $insertId;
  
  /**
   * Holds the total number of queries ran for the database object's lifetime.
   *
   * @var integer
   */
  protected $queryCount = 0;
  
  /**
   * Construct a new database object.
   * 
   * @param \mysqli $connection 
   */
  public function __construct(mysqli $connection)
  {
    $this->setConnection($connection);
  }
  
  /**
   * Close the mysql connection on destruct 
   */
  public function __destruct()
  {
    if( $this->getConnection() ) {
      $this->getConnection()->close();
    }
  }
  
  /**
   * Get affected rows
   * 
   * @return integer
   */
  public function getAffectedRows()
  {
    return $this->affectedRows;
  }
  
  /**
   * Exposes the local connection object
   *
   * @return \mysqli
   */
  public function getConnection()
  {
    return $this->connection;
  }
  
  /**
   * Sets the local mysqli object
   *
   * @param \mysqli $connection 
   * @return \zsql\Database
   */
  public function setConnection(mysqli $connection = null)
  {
    $this->connection = $connection;
    return $this;
  }
  
  /**
   * Get the last insert ID
   * 
   * @return integer
   */
  public function getInsertId()
  {
    return $this->insertId;
  }
  
  /**
   * Gets number of queries run using this adapter.
   * 
   * @return integer
   */
  public function getQueryCount()
  {
    return $this->queryCount;
  }
  
  /**
   * Wrapper for \zsql\Select
   *
   * @return \zsql\Select
   */
  public function select()
  {
    return new Select($this);
  }
  
  /**
   * Wrapper for \zsql\Insert
   *
   * @return \zsql\Insert
   */
  public function insert()
  {
    return new Insert($this);
  }
  
  /**
   * Wrapper for \zsql\Update
   *
   * @return \zsql\Update
   */
  public function update()
  {
    return new Update($this);
  }
  
  /**
   * Wrapper for \zsql\Delete
   *
   * @return \zsql\Delete
   */
  public function delete()
  {
    return new Delete($this);
  }
  
  /**
   * Executes an SQL query
   *
   * @param string|\zsql\Query $query 
   * @param string $resultmode 
   * @return \zsql\Result|mixed
   */
  public function query($query, $resultmode = MYSQLI_STORE_RESULT)
  {
    $connection = $this->getConnection();
    
    $this->affectedRows = null;
    $this->insertId = null;
    $this->queryCount++;
    
    $queryString = (string) $query; 
    $ret = $connection->query($queryString, $resultmode);
    
    // Save insert ID if instance of insert
    if( $query instanceof \zsql\Insert ) {
      $this->insertId = $connection->insert_id;
    }
    
    // Save affected rows
    $this->affectedRows = $connection->affected_rows;

    // Handle result
    if( $ret !== false ) {
      // Select -> \zsql\Result
      // Insert -> insertId OR true
      // Update/Delete -> affectedRows
      if( $ret instanceof mysqli_result ) {
        // handle mysqli_result object
        return new Result($ret);
      } else if( $query instanceof \zsql\Insert ) {
        return $this->getInsertId();
      } else if( $query instanceof \zsql\Update || 
                 $query instanceof \zsql\Delete ) {
        return $this->getAffectedRows();
      }
      // handle string update/delete/insert queries 
      return $ret;
    } else {
      // Query failed, throw exception
      throw new Exception(sprintf("%s: %s\n%s",
        $connection->errno, $connection->error, $query));
    }
  }

  /**
   * Quote a raw string
   *
   * @param string $value 
   * @return string
   */
  public function quote($value)
  {
    if( null === $value ) {
      return 'NULL';
    } else if( is_bool($value) ) {
      return ( $value ? '1' : '0' );
    } else if( $value instanceof Expression ) {
      return (string) $value;
    } else if( is_integer($value) ) {
      return sprintf('%d', $value);
    } else if( is_float($value) ) {
      return sprintf('%f', $value); // @todo make sure precision is right
    } else {
      return "'" . $this->getConnection()->real_escape_string($value) . "'";
    }
  }
}
