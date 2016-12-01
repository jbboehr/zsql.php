<?php

namespace zsql;

interface Query
{
     public function __toString() : string;
     public function toString() : string;
     public function params() : array;
}
