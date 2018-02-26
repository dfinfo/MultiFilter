<?php
/**
 * @author Denis Fohl
 * 26/02/18
 */

namespace Dfinfo\MultiFilter;

use Doctrine\ORM\QueryBuilder;


interface FilterStrategy
{
    /**
     * @param QueryBuilder $qb
     * @param Filter       $filter
     *
     * @return void
     */
    public function apply(QueryBuilder $qb, Filter $filter);
}