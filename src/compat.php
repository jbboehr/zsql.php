<?php

class_alias('zsql\\Adapter\\MysqliAdapter', 'zsql\\Database');
class_alias('zsql\\QueryBuilder\\Delete', 'zsql\\Delete');
class_alias('zsql\\QueryBuilder\\ExtendedQuery', 'zsql\\ExtendedQuery');
class_alias('zsql\\QueryBuilder\\Insert', 'zsql\\Insert');
class_alias('zsql\\QueryBuilder\\Query', 'zql\\Query');
class_alias('zsql\\QueryBuilder\\Select', 'zsql\\Select');
class_alias('zsql\\QueryBuilder\\Update', 'zsql\\Update');
class_alias('zsql\\Result\\MysqliResult', 'zsql\\Result');
// Can't do this here because it will cause a parse error
//class_alias('zsql\\Scanner\\ScannerGenerator', 'zsql\\ScannerGenerator');
class_alias('zsql\\Scanner\\ScannerIterator', 'zsql\\ScannerIterator');
class_alias('zsql\\Table\\DefaultTable', 'zsql\\Model');
