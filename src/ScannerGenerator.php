<?php

namespace zsql;

/**
 * Table scanner (generator implementation)
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
