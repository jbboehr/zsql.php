<?php

$basepath = realpath(dirname(dirname(__FILE__)));
$testpath = realpath(dirname(__FILE__));

if( empty($_SERVER['PHAR']) ) {
  $srcpath = $basepath . '/src/zsql';
} else {
  require $basepath . '/build/zsql.phar';
  $srcpath = 'phar://zsql.phar';
}


require $srcpath . '/Exception.php';
require $srcpath . '/Expression.php';
require $srcpath . '/Query.php';
require $srcpath . '/ExtendedQuery.php';
require $srcpath . '/Delete.php';
require $srcpath . '/Insert.php';
require $srcpath . '/Select.php';
require $srcpath . '/Update.php';

require $srcpath . '/Database.php';
require $srcpath . '/Result.php';
require $srcpath . '/Model.php';

require $testpath . '/common.php';
