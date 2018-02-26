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
        foreach ($values as $key => $value) {
            $this->getCriteria($key)->setValue($value);
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

    public function setValuesFromForm()
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

    /**
     * @return FilterStrategy
     */
    public function getFilterStrategy(): FilterStrategy
    {
        return $this->filterStrategy;
    }

    /**
     * @param FilterStrategy $filterStrategy
     */
    public function setFilterStrategy(FilterStrategy $filterStrategy): void
    {
        $this->filterStrategy = $filterStrategy;
    }

}
