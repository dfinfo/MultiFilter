<?php

namespace Dfinfo\MultiFilter;


use Dfinfo\MultiFilter\Exception\ConstraintViolationException;
use Dfinfo\MultiFilter\Exception\InvalidArgumentException;

class Criteria
{
    const OPERATORS = [
        'eq',
        'neq',
        'lt',
        'lte',
        'gt',
        'gte',
        'isNull',
        'isNotNull',
        'in',
        'notIn',
        'like',
        'notLike'
    ];
    /**
     * @var string|array
     */
    protected $field;
    /**
     * @var string
     */
    protected $operator;
    /**
     * @var mixed
     */
    protected $value;
    /**
     * @var array
     */
    protected $dqlJoin;

    /**
     * @return string|array
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param $field
     * @throws InvalidArgumentException
     */
    public function setField($field)
    {
        if (is_string($field) || is_array($field)) {
            $this->field = $field;
        } else {
            throw new InvalidArgumentException('le type de field doit être string ou array');
        }
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     * @throws InvalidArgumentException
     */
    public function setOperator(string $operator)
    {
        if (!in_array($operator, self::OPERATORS)) {
            throw new InvalidArgumentException();
        }
        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @throws ConstraintViolationException
     */
    public function setValue($value)
    {
        if ($this->getOperator() == 'isNull' || $this->getOperator() == 'isNotNull') {
            throw new ConstraintViolationException(
                "Il n'est pas cohérent d'affecter une valeur lorsque l'opérateur est isNull ou isNotNull"
            );
        }

        if ($this->getOperator() == 'like' || $this->getOperator() == 'notLike') {
            $this->value = '%' . $value . '%';
        } else {
            $this->value = $value;
        }
    }

    /**
     * @return array
     */
    public function getDqlJoin(): array
    {
        return $this->dqlJoin;
    }

    /**
     * @param array $dqlJoin
     * @throws InvalidArgumentException
     */
    public function setDqlJoin(array $dqlJoin)
    {
        if (!is_array($dqlJoin)
            || !array_key_exists('property', $dqlJoin)
            || !array_key_exists('referencedColumnName', $dqlJoin))
        {
            throw new InvalidArgumentException(
                'dqlJoin doit être un tableau contenant property et referencedColumnName'
            );
        }

        $this->dqlJoin = $dqlJoin;
    }

    /**
     * @return bool
     */
    public function hasDqlJoin()
    {
        return !is_null($this->dqlJoin);
    }

}
