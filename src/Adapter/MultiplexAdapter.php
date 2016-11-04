<?php

namespace zsql\Adapter;

use zsql\Adapter as AdapterInterface;
use zsql\QueryBuilder\Query;
use zsql\QueryBuilder\Select;

class MultiplexAdapter extends AbstractAdapter
{
    /**
     * @param AdapterInterface
     */
    protected $reader;

    /**
     * @param AdapterInterface
     */
    protected $writer;

    /**
     * @var bool
     */
    protected $forceWriter = false;

    /**
     * @param AdapterInterface $reader
     * @param AdapterInterface $writer
     */
    public function __construct(AdapterInterface $reader, AdapterInterface $writer = null)
    {
        $this->reader = $reader;
        $this->writer = $writer;
    }

    public function ping()
    {
        $ret = $this->reader->ping();
        if( $this->writer ) {
            $ret = $ret && $this->writer->ping();
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function query($query)
    {
        $this->affectedRows = null;
        $this->insertId = null;
        $this->queryCount++;

        // Log query
        if( $this->logger ) {
            if( $query instanceof Query ) {
                $queryString = $query->toString();
            } else {
                $queryString = (string) $query;
            }
            $this->logger->debug($queryString);
        }

        $adapter = $this->canUseWriter($query) ? $this->writer : $this->reader;
        $this->useWriter(false);

        try {
            $result = $adapter->query($query);
        } catch( \Exception $e ) {
            if( $this->logger ) {
                $this->logger->error($e);
            }
            throw $e;
        }

        $this->insertId = $adapter->getInsertId();
        $this->affectedRows = $adapter->getAffectedRows();

        return $result;
    }

    /**
     * Sets a flag that can force the next query to use the writer connection if it is available.
     *
     * @param bool $flag
     * @return $this
     */
    public function useWriter($flag = true)
    {
        $this->forceWriter = $flag;
        return $this;
    }

    /**
     * Returns true if a query should use the writer mysqli object.
     *
     * @param $query
     * @return boolean
     */
    public function canUseWriter($query)
    {
        $allowed = !$this->isSelect($query) || $this->writerForced();
        return $this->hasWriter() && $allowed;
    }

    /**
     * Returns true if the useWriter function was called.
     *
     * @return bool
     */
    public function writerForced()
    {
        return $this->forceWriter;
    }

    /**
     * Returns true if a reader object is set.
     *
     * @return bool
     */
    public function hasReader()
    {
        return null !== $this->reader;
    }

    /**
     * Returns true if a writer object is set.
     *
     * @return bool
     */
    public function hasWriter()
    {
        return null !== $this->writer;
    }

    /**
     * Returns the reader mysqli object
     *
     * @return Adapter
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * Returns the reader mysqli object
     *
     * @return AdapterInterface
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * @param AdapterInterface $reader
     * @return $this
     */
    public function setReader(AdapterInterface $reader)
    {
        $this->reader = $reader;
        return $this;
    }

    /**
     * @param AdapterInterface $writer
     * @return $this;
     */
    public function setWriter(AdapterInterface $writer = null)
    {
        $this->writer = $writer;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function quote($string)
    {
        return $this->reader->quote($string);
    }

    /**
     * Tests for queries that do not modify.
     *
     * @param Query|string $query
     * @return boolean
     */
    protected function isSelect($query)
    {
        if( $query instanceof Query ) {
            return $query instanceof Select;
        } else {
            return (boolean) preg_match('/^(SELECT|SHOW)/i', trim($query));
        }
    }
}
