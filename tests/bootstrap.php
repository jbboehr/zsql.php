<?php

$basepath = realpath(dirname(dirname(__FILE__)));
$testpath = realpath(dirname(__FILE__));

require $basepath . '/src/Exception.php';
require $basepath . '/src/Expression.php';
require $basepath . '/src/Query.php';
require $basepath . '/src/ExtendedQuery.php';
require $basepath . '/src/Delete.php';
require $basepath . '/src/Insert.php';
require $basepath . '/src/Select.php';
require $basepath . '/src/Update.php';

require $testpath . '/common.php';
