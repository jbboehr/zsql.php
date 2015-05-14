# zsql.php

[![Build Status](https://travis-ci.org/jbboehr/zsql.php.svg?branch=master)](https://travis-ci.org/jbboehr/zsql.php)
[![HHVM Status](http://hhvm.h4cc.de/badge/jbboehr/zsql.png)](http://hhvm.h4cc.de/package/jbboehr/zsql)
[![Code Coverage](https://scrutinizer-ci.com/g/jbboehr/zsql.php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jbboehr/zsql.php/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/jbboehr/zsql/v/stable.svg)](https://packagist.org/packages/jbboehr/zsql)
[![License](https://poser.pugx.org/jbboehr/zsql/license.svg)](https://packagist.org/packages/jbboehr/zsql)

Lightweight MySQL adapter and SQL generator


## Install

Via Composer

``` bash
composer require jbboehr/zsql
```


## Usage

### Delete

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

### Basic SQL or Custom Database Adapter

See [Adapters.md](https://github.com/jbboehr/zsql.php/blob/master/Adapters.md)


## Testing

``` bash
make test
```


## License

This project is licensed under the [MIT license](http://opensource.org/licenses/MIT).
