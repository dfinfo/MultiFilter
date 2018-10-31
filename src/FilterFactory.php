<?php

namespace Dfinfo\MultiFilter;

use Dfinfo\MultiFilter\Exception\ConfigParameterNotFoundException;
use Dfinfo\MultiFilter\Exception\InvalidConfigParameterException;

class FilterFactory
{
    /**
     * @param array $config
     *
     * @return Filter
     * @throws ConfigParameterNotFoundException
     * @throws Exception\ConstraintViolationException
     * @throws Exception\InvalidArgumentException
     * @throws InvalidConfigParameterException
     */
    public static function create(array $config)
    {
        self::validateConfig($config);
        $filter = new Filter();

        foreach ($config['criterias'] as $criteriaId => $criteriaConfig) {
            self::validateCriteriaConfig($criteriaConfig);
            $criteria = new Criteria();
            $criteria->setField($criteriaConfig['field']);
            $criteria->setOperator($criteriaConfig['operator']);
            if (array_key_exists('dqlJoin', $criteriaConfig)) {
                $criteria->setDqlJoin($criteriaConfig['dqlJoin']);
            }
            if (array_key_exists('value', $criteriaConfig)) {
                $criteria->setValue($criteriaConfig['value']);
            }
            $filter->addCriteria($criteriaId, $criteria);
        }

        if (!isset($config['filterStrategy'])) {
            $filter->setFilterStrategy(new AndStrategy());
        } else {
            $strategy = new $config['filterStrategy'];
            $filter->setFilterStrategy($strategy);
        }

        return $filter;
    }

    /**
     * @param array $config
     * @throws InvalidConfigParameterException
     */
    public static function validateConfig(array $config)
    {
        $test = array_filter($config, function ($key) {
            return is_string($key);
        }, ARRAY_FILTER_USE_KEY);

        if (count($test) != count($config)) {
            throw new InvalidConfigParameterException('Invalid filter config, criterias array keys must be strings');
        }
    }

    /**
     * @param array $config
     * @throws ConfigParameterNotFoundException
     */
    public static function validateCriteriaConfig(array $config)
    {
        if (!array_key_exists('field', $config)) {
            throw new ConfigParameterNotFoundException('config parameter "field" is missing to create criteria');
        }
        if (!array_key_exists('operator', $config)) {
            throw new ConfigParameterNotFoundException('config parameter "operator" is missing to create criteria');

        }
    }
}