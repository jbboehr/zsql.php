
## Custom database adapter integration

The first (optional) argument to the constructors is a callback to execute
the query and return the result. If your database class does not use bound
parameters, you must set a quote callback and enable parameter interpolation.
Otherwise, the array of parameters will be passed as the second argument
to execution callback.

### With parameter interpolation

```php
class Database
{
  public function query($query)
  {
    // Execute the query and return the result
  }

  public function quote($value)
  {
    // Quote and return the value
  }

  public function select()
  {
    $database = $this;
    $select = new zsql\Select(function($query) {
      return $database->query($query);
    });
    return $select
      ->setQuoteCallback(array($this, 'quote'))
      ->interpolation();
  }
}
```

### Without parameter interpolation

```php
class Database
{
  public function query($query, $params)
  {
    // Execute the query using specified params and return the result
  }

  public function quote($value)
  {
    // Quote and return the value
  }

  public function select()
  {
    $database = $this;
    return new zsql\Select(function($query) {
      return $database->query($query);
    });
  }
}
```

## Adapter-less Usage

#### Delete

```php
$delete = new zsql\Delete();
$delete
  ->from('tableName')
  ->where('columnName', 'value')
  ->limit(1);
echo var_export($delete->toString(), true), ";\n", 
     var_export($delete->params(), true), ";\n";
```

produces

```php
'DELETE FROM `tableName` WHERE `columnName` = ? LIMIT ?';
array (
  0 => 'value',
  1 => 1,
);
```

### Insert

```php
$insert = new zsql\Insert();
$insert
  ->ignore()
  ->into('tableName')
  ->value('columnName', 'value')
  ->value('otherColumnName', 'otherValue');
echo var_export($insert->toString(), true), ";\n", 
     var_export($insert->params(), true), ";\n";
```

produces

```php
'INSERT IGNORE INTO `tableName` SET `columnName` = ? , `otherColumnName` = ?';
array (
  0 => 'value',
  1 => 'otherValue',
);
```

### Select

```php
$select = new zsql\Select();
$select
  ->from('tableName')
  ->where('columnName', 'value')
  ->order('orderColumn', 'ASC')
  ->limit(2)
  ->offset(5);
echo var_export($select->toString(), true), ";\n", 
     var_export($select->params(), true), ";\n";
```

produces

```php
'SELECT * FROM `tableName` WHERE `columnName` = ? ORDER BY `orderColumn` ASC LIMIT ?, ?';
array (
  0 => 'value',
  1 => 5,
  2 => 2,
);
```

### Update

```php
$update = new zsql\Update();
$update
  ->table('tableName')
  ->set('columnName', 'value')
  ->set('someColumn', new zsql\Expression('NOW()'))
  ->where('otherColumnName', 'otherValue')
  ->limit(1);
echo var_export($update->toString(), true), ";\n", 
     var_export($update->params(), true), ";\n";
```

produces

```php
'UPDATE `tableName` SET `columnName` = ? , `someColumn` = NOW() WHERE `otherColumnName` = ? LIMIT ?';
array (
  0 => 'value',
  1 => 'otherValue',
  2 => 1,
);
```
