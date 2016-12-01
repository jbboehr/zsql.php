<?php

namespace zsql\Adapter;

use zsql\Exception\RuntimeException;
use zsql\Query;
use zsql\Query\Select;
use zsql\Result\NullResult;

class NullAdapter extends AbstractAdapter
{
    public function ping()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function query($query)
    {
        // Log query
        if( $this->logger ) {
            if( $query instanceof Query ) {
                $queryString = $query->toString();
            } else {
                $queryString = (string) $query;
            }
            $this->logger->debug($queryString);
        }

        if( $query instanceof Select ) {
            return new NullResult();
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function quote($string)
    {
        throw new RuntimeException('Not implemented');
    }
}
