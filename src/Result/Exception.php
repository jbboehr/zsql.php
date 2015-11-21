<?php

namespace zsql\Result;

use RuntimeException;
use zsql\Exception as ExceptionInterface;

class Exception extends RuntimeException implements ExceptionInterface {}
