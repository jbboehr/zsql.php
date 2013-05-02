#!/usr/bin/env php
<?php

$srcRoot = './src/zsql';
$buildRoot = './build';

try {
  $phar = new Phar($buildRoot . '/zsql.phar', 
      FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME,
      'zsql.phar');
  $phar->setStub('<?php Phar::mapPhar("zsql.phar"); __HALT_COMPILER();');
  $phar->buildFromDirectory($srcRoot);
} catch( Exception $e ) {
  echo $e;
  exit(1);
}

