<?php
/**
 * @author Denis Fohl
 * 26/02/18
 */

namespace Dfinfo\MultiFilter;


use Doctrine\ORM\QueryBuilder;

class AndStrategy implements FilterStrategy
{
    /**
     * @param QueryBuilder $qb
     * @param Filter       $filter
     */
    public function apply(QueryBuilder $qb, Filter $filter)
    {
        $parameter  = 0;

        foreach ($filter->getCriterias() as $criteria) {
            if ($criteria->isActive()) {
                $parameter +=1;
                if ($criteria->hasDqlJoin()) {
                    $join = $criteria->getDqlJoin();
                    $qb->join('entity.' . $join['property'],
                        $join['property'],
                        'WITH',
                        $join['property'] . '.' . $join['referencedColumnName'] . ' = ?' . $parameter);
                    // TODO : modifier pour ne quoter la valeur que si $criteria->valueMustBeQuoted
                    $qb->setParameter($parameter, $criteria->getValue());
                } else {
                    $fieldsExpr = $criteria->getFieldsExpr($qb, $parameter);
                    $qb->andWhere(
                        call_user_func_array([$qb->expr(), 'orX'], $fieldsExpr)
                    );
                }
            }
        }
    }

}