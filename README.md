# zsql.php

[![Build Status](https://travis-ci.org/jbboehr/zsql.php.png?branch=master)](https://travis-ci.org/jbboehr/zsql.php)

Lightweight MySQL adapter and SQL generator

## Installation

With [composer](http://getcomposer.org)

```json
{
    "require": {
        "jbboehr/zsql": "0.3.*"
    }
}
```

## Usage

#### Delete

```php
$database = new \zsql\Database($mysqli);
$database->delete()
  ->from('tableName')
  ->where('columnName', 'value')
  ->limit(1)
  ->query();
```

### Insert

```php
$database = new \zsql\Database($mysqli);
$id = $database->insert()
  ->ignore()
  ->into('tableName')
  ->value('columnName', 'value')
  ->value('otherColumnName', 'otherValue')
  ->query();
```

### Select

```php
$database = new \zsql\Database($mysqli);
$rows = $database->select()
  ->from('tableName')
  ->where('columnName', 'value')
  ->order('orderColumn', 'ASC')
  ->limit(2)
  ->offset(5)
  ->query()
  ->fetchAll();
```

### Update

```php
$database = new \zsql\Database($mysqli);
$database->update()
  ->table('tableName')
  ->set('columnName', 'value')
  ->set('someColumn', new zsql\Expression('NOW()'))
  ->where('otherColumnName', 'otherValue')
  ->limit(1)
  ->query();
```

## Basic SQL or Custom Database Adapter

See [this](https://github.com/jbboehr/zsql.php/blob/master/Adapters.md)


## License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).
