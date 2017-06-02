<?php
declare(strict_types=1);

namespace Dfinfo\MultiFilter;

use Dfinfo\MultiFilter\Exception\InvalidArgumentException;
use Dfinfo\MultiFilter\Exception\InvalidConfigParameterException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;

class Filter
{
    /**
     * @var Criteria[]
     */
    protected $criterias;

    /**
     * @param QueryBuilder $qb
     */
    public function apply(QueryBuilder $qb)
    {
        $parameter  = 0;

        foreach ($this->criterias as $criteria) {
            if ($this->isActive($criteria)) {
                $parameter +=1;
                if ($criteria->hasDqlJoin()) {
                    $join = $criteria->getDqlJoin();
                    $qb->join('entity.' . $join['property'],
                        $join['property'],
                        'WITH',
                        $join['property'] . '.' . $join['referencedColumnName'] . ' = ?' . $parameter);
                        $qb->setParameter($parameter, $criteria->getValue());
                } else {
                    $fieldsExpr = $this->getFieldsExpr($criteria, $qb, $parameter);
                    $qb->andWhere(
                        call_user_func_array([$qb->expr(), 'orX'], $fieldsExpr)
                    );
                }
            }
        }
    }

    protected function getFieldsExpr(Criteria $criteria, QueryBuilder $qb, int $parameter)
    {
        $noValue    = ['isNull', 'isNotNull'];
        $operator   = $criteria->getOperator();
        $alias      = $qb->getRootAliases()[0];
        $fields     = (is_array($criteria->getField())) ? $criteria->getField() : [$criteria->getField()];

        $fieldsExpr = [];
        if (in_array($operator, $noValue)) {
            foreach ($fields as $field) {
                $fieldsExpr[] = $qb->expr()->$operator($alias . '.' . $field);
            }
        } else {
            foreach ($fields as $field) {
                $fieldsExpr[] = $qb->expr()->$operator($alias . '.' . $field, '?' . $parameter);
            }
            $qb->setParameter($parameter, $criteria->getValue());
        }

        return $fieldsExpr;
    }

    public function setValuesFromForm(Form $form)
    {

    }

    public function setValuesFromArray(array $values)
    {
        foreach ($values as $key => $value) {
            $this->getCriteria($key)->setValue($value);
        }
    }

    public function setValuesFromObject($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException();
        }

        foreach ($this->getCriterias() as $key => $criteria) {
            $method = 'get' . ucfirst($key);
            if (!is_null($object->$method())) {
                $criteria->setValue($object->$method());
            }
        }
    }

    public function setValuesFromConfig()
    {

    }

    /**
     * @param string $key
     * @param Criteria $criteria
     * @throws InvalidConfigParameterException
     */
    public function addCriteria($key, Criteria $criteria)
    {
        if (!is_string($key)) {
            throw new InvalidConfigParameterException('Invalid Dfinfo\MultiFilter config, criterias array keys must be strings');
        }

        $this->criterias[$key] = $criteria;
    }

    /**
     * @param string $key
     * @return Criteria
     */
    public function getCriteria(string $key)
    {
        return $this->criterias[$key];
    }

    /**
     * @param string $key
     */
    public function removeCriteria(string $key)
    {
        unset($this->criterias[$key]);
    }

    /**
     * @param Criteria $criteria
     * @return bool
     */
    protected function isActive(Criteria $criteria)
    {
        if ($criteria->getOperator() == 'isNull' || $criteria->getOperator() == 'isNotNull') {
            return true;
        }

        return !is_null($criteria->getValue());
    }

    /**
     * @return Criteria[]
     */
    public function getCriterias(): array
    {
        return $this->criterias;
    }

    /**
     * @param Criteria[] $criterias
     */
    public function setCriterias(array $criterias)
    {
        $this->criterias = $criterias;
    }

}
