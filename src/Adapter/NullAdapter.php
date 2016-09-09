<?php

namespace zsql\Adapter;

use zsql\QueryBuilder\Query;
use zsql\QueryBuilder\Select;

class NullAdapter extends AbstractAdapter
{
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
        throw new Exception('Not implemented');
    }
}
