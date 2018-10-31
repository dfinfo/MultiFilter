<?php
declare(strict_types=1);

namespace Dfinfo\MultiFilter;

use Dfinfo\MultiFilter\Exception\InvalidArgumentException;
use Dfinfo\MultiFilter\Exception\InvalidConfigParameterException;
use Doctrine\ORM\QueryBuilder;

class Filter
{
    /**
     * @var Criteria[]
     */
    protected $criterias;
    /**
     * @var FilterStrategy
     */
    protected $filterStrategy;

    /**
     * @param QueryBuilder $qb
     */
    public function apply(QueryBuilder $qb)
    {
        $this->getFilterStrategy()->apply($qb, $this);
    }

    /**
     * @param array $values
     *
     * @throws Exception\ConstraintViolationException
     */
    public function setValuesFromArray(array $values)
    {
        foreach ($values as $criteriasId => $value) {
            $this->getCriteria($criteriasId)->setValue($value);
        }
    }

    /**
     * @param $object
     *
     * @throws Exception\ConstraintViolationException
     * @throws InvalidArgumentException
     */
    public function setValuesFromObject($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException();
        }

        foreach ($this->getCriterias() as $criteriaId => $criteria) {
            $method = 'get' . ucfirst($criteriaId);
            if (!is_null($object->$method())) {
                $criteria->setValue($object->$method());
            }
        }
    }

    public function setValuesFromConfig()
    {

    }

    public function setValuesFromForm()
    {

    }

    /**
     * @param string   $criteriaId
     * @param Criteria $criteria
     *
     * @throws InvalidConfigParameterException
     */
    public function addCriteria($criteriaId, Criteria $criteria)
    {
        if (!is_string($criteriaId)) {
            throw new InvalidConfigParameterException('Invalid Dfinfo\MultiFilter config, criterias array keys must be strings');
        }

        $this->criterias[$criteriaId] = $criteria;
    }

    /**
     * @param string $criteriaId
     *
     * @return Criteria
     */
    public function getCriteria($criteriaId)
    {
        return $this->criterias[$criteriaId];
    }

    /**
     * @param string $criteriaId
     */
    public function removeCriteria($criteriaId)
    {
        unset($this->criterias[$criteriaId]);
    }

    /**
     * @return Criteria[]
     */
    public function getCriterias()
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

    /**
     * @return FilterStrategy
     */
    public function getFilterStrategy()
    {
        return $this->filterStrategy;
    }

    /**
     * @param FilterStrategy $filterStrategy
     */
    public function setFilterStrategy(FilterStrategy $filterStrategy)
    {
        $this->filterStrategy = $filterStrategy;
    }

}
