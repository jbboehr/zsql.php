<?php

namespace zsql\Scanner;

use Iterator;
use zsql\QueryBuilder\Select;

/**
 * Class ScannerIterator
 * Table scanner (iterator implementation)
 * @package zsql\Scanner
 */
class ScannerIterator implements Iterator
{
    const DEFAULT_BATCH_SIZE = 100;
    /**
     * @var integer
     */
    protected $batchSize;

    /**
     * @var integer
     */
    protected $cursor = 0;

    /**
     * @var integer
     */
    protected $mode;

    /**
     * @var Select
     */
    protected $query;
    protected $currentBatch;
    protected $currentBatchOffset = 0;
    protected $currentBatchSize = 0;

    /**
     * Constructor
     *
     * @param Select $query
     */
    public function __construct(Select $query)
    {
        $this->query = $query;

        // Get batch size and cursor
        $this->batchSize = (integer) $query->getLimit() ? : self::DEFAULT_BATCH_SIZE;
        $this->cursor = (integer) $query->getOffset();
    }

    public function mode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    public function current()
    {
        return $this->currentBatch[$this->cursor - $this->currentBatchOffset];
    }

    public function key()
    {
        return $this->cursor;
    }

    public function next()
    {
        ++$this->cursor;
    }

    public function rewind()
    {
        $this->cursor = 0;
    }

    public function valid()
    {
        if( $this->cursor === 0 ) {
            $this->loadBatch(0);
        } else if( $this->cursor >= $this->currentBatchSize + $this->currentBatchOffset ) {
            if( $this->currentBatchSize < $this->batchSize ) {
                return false;
            }
            $this->loadBatch(max(0, $this->cursor - 1));
        }

        return isset($this->currentBatch[$this->cursor - $this->currentBatchOffset]);
    }

    protected function loadBatch($offset)
    {
        $this->currentBatchOffset = $offset;
        $this->currentBatch = $this->query
            ->offset($offset)
            ->limit($this->batchSize)
            ->query()
            ->fetchAll($this->mode);
        $this->currentBatchSize = count($this->currentBatch);
    }
}
