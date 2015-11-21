<?php

namespace zsql\QueryBuilder;

use RuntimeException;
use zsql\Exception as ExceptionInterface;

class Exception extends RuntimeException implements ExceptionInterface {}
