<?php

namespace zsql;

interface Query
{
     public function __toString();
     public function toString();
     public function params();
}
