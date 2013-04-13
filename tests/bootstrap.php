<?php

$basepath = realpath(dirname(dirname(__FILE__)));
$testpath = realpath(dirname(__FILE__));

require $basepath . '/src/zsql/Exception.php';
require $basepath . '/src/zsql/Expression.php';
require $basepath . '/src/zsql/Query.php';
require $basepath . '/src/zsql/ExtendedQuery.php';
require $basepath . '/src/zsql/Delete.php';
require $basepath . '/src/zsql/Insert.php';
require $basepath . '/src/zsql/Select.php';
require $basepath . '/src/zsql/Update.php';

require $testpath . '/common.php';
