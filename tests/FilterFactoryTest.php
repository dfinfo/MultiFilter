<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 02/06/17
 * Time: 16:17
 */
use PHPUnit\Framework\TestCase;
use Dfinfo\MultiFilter\Filter;
use Dfinfo\MultiFilter\FilterFactory;
use Dfinfo\MultiFilter\Exception\InvalidConfigParameterException;
use Dfinfo\MultiFilter\Exception\ConfigParameterNotFoundException;

class FilterFactoryTest extends TestCase
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @throws ConfigParameterNotFoundException
     * @throws InvalidConfigParameterException
     * @throws \Dfinfo\MultiFilter\Exception\ConstraintViolationException
     * @throws \Dfinfo\MultiFilter\Exception\InvalidArgumentException
     */
    public function setUp()
    {
        $config = [
            'criterias' => [
                    'dateDebut' => [
                        'field' => 'dateParution',
                        'operator' => 'lte',
                    ],
                    'dateFin' => [
                        'field' => 'dateParution',
                        'operator' => 'gte',
                    ],
            ],
        ];

        $this->filter = FilterFactory::create($config);
    }

    /**
     * @throws ConfigParameterNotFoundException
     * @throws InvalidConfigParameterException
     * @throws \Dfinfo\MultiFilter\Exception\ConstraintViolationException
     * @throws \Dfinfo\MultiFilter\Exception\InvalidArgumentException
     */
    public function testCriteriaKeyIsString()
    {
        $this->expectException(InvalidConfigParameterException::class);
        $this->expectExceptionMessage('Invalid Dfinfo\MultiFilter config, criterias array keys must be strings');

        FilterFactory::create([
            'criterias' => [
                [
                    'field'    => 'toto',
                    'operator' => 'eq',
                ],
            ],
        ]);
    }

    /**
     * @throws ConfigParameterNotFoundException
     * @throws InvalidConfigParameterException
     * @throws \Dfinfo\MultiFilter\Exception\ConstraintViolationException
     * @throws \Dfinfo\MultiFilter\Exception\InvalidArgumentException
     */
    public function testConfigCriteriaWhithoutOperator()
    {
        $this->expectException(ConfigParameterNotFoundException::class);
        $this->expectExceptionMessage('config parameter "operator" is missing to create criteria');
        FilterFactory::create([
            'criterias' => [
                'crit1' => [
                    'field' => 'test',
                ],
            ],
        ]);

    }

    /**
     * @throws ConfigParameterNotFoundException
     * @throws InvalidConfigParameterException
     * @throws \Dfinfo\MultiFilter\Exception\ConstraintViolationException
     * @throws \Dfinfo\MultiFilter\Exception\InvalidArgumentException
     */
    public function testConfigCriteriaWhithoutField()
    {
        $this->expectException(ConfigParameterNotFoundException::class);
        $this->expectExceptionMessage('config parameter "field" is missing to create criteria');
        FilterFactory::create([
            'criterias' => [
                'crit1' => [
                    'operator' => 'eq',
                ],
            ],
        ]);
    }

    /**
     *
     */
    public function testFilterHasCriterias()
    {
        $this->assertEquals(2, count($this->filter->getCriterias()));
    }
}
