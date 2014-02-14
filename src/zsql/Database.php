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
   * Insert query modifiers
   */
  const NORMAL  = 0;
  const DELAYED = 1;
  const IGNORE  = 2;
  const REPLACE = 4;
  
  /**
   * @var mysqli
   */
  protected $connection;
  
  /**
   * Holds the total number of queries ran for the database object's lifetime.
   *
   * @var integer
   */
  protected $queryCount = 0;
  
  /**
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
   * @return \Engine\Database
   */
  public function setConnection(mysqli $connection = null)
  {
    $this->connection = $connection;
    return $this;
  }
  
  /**
   * Gets number of queries ran for the database object's lifetime.
   * 
   * @return integer
   */
  public function getQueryCount()
  {
    return $this->queryCount;
  }
  
  /**
   * Wraps common options used to configure all zsql objects
   *
   * @param \zsql\Query $$query 
   * @return \zsql\Query
   */
  protected function _configureZsqlObject(Query $query)
  {
    return $query
      ->setQuoteCallback(array($this, 'quote'))
      ->interpolation();
  }
  
  /**
   * Wrapper for zsql\Select
   *
   * @return \zsql\Select
   */
  public function select()
  {
    $database = $this;
    $queryObject = new Select(
      function($query) use ($database, &$queryObject) {
        $result = $database->query($query);
        if( $queryObject instanceof Select &&
            $result instanceof Result && 
            ($class = $queryObject->getResultClass()) ) {
          $result->setResultClass($class);
        }
        return $result;
      }
    );
    return $this->_configureZsqlObject($queryObject);
  }
  
  /**
   * The insert function
   *
   * @return \zsql\Insert|mixed
   */
  public function insert()
  {
    $database = $this;
    return $this->_configureZsqlObject(new Insert(
      function($query) use ($database) {
        return $database->query($query);
      }
    ));
  }
  
  /**
   * The update function
   *
   * @return \zsql\Update
   */
  public function update()
  {
    $database = $this;
    return $this->_configureZsqlObject(new Update(
      function($query) use ($database) {
        return $database->query($query);
      }
    ));
  }
  
  /**
   * The delete function
   *
   * @return \zsql\Delete
   */
  public function delete()
  {
    $database = $this;
    return $this->_configureZsqlObject(new Delete(
      function($query) use ($database) {
        return $database->query($query);
      }
    ));
  }
  
  /**
   * Executes an SQL query
   *
   * @param string $query 
   * @param string $resultmode 
   * @return \Engine\Database\Result|mixed
   */
  public function query($query, $resultmode = MYSQLI_STORE_RESULT)
  {
    $connection = $this->getConnection();

    $this->queryCount++;
    
    $query = (string) $query; 
    $ret = $connection->query($query, $resultmode);

    if( $ret === false ) {
      // throwing exceptions if an error occurss...
      throw new Exception(sprintf("%s: %s\n%s",
        $connection->errno, $connection->error, $query));
    }
    
    // mysql_query:
    // Returns FALSE on failure. For successful SELECT, SHOW, DESCRIBE or 
    // EXPLAIN queries mysqli_query() will return a mysqli_result object. For 
    // other successful queries mysqli_query() will return TRUE.
    if( $ret instanceof mysqli_result ) {
      // handle mysqli_result object
      return new Result($ret);
    } else {
      return $ret; 
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
