<?php

namespace zsql\Scanner;

/**
 * Class ScannerGenerator
 * Table scanner (generator implementation)
 * @package zsql\Scanner
 */
class ScannerGenerator extends ScannerIterator
{
    public function __invoke()
    {
        do {
            $rows = $this->query
                ->offset($this->cursor)
                ->limit($this->batchSize)
                ->query()
                ->fetchAll($this->mode);
            $count = count($rows);

            foreach( $rows as $row ) {
                $this->cursor++;
                yield $row;
            }
        } while( $count >= $this->batchSize );
    }
}
