<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 02/06/17
 * Time: 16:04
 */
use Dfinfo\MultiFilter\Criteria;
use PHPUnit\Framework\TestCase;

class CriteriaTest extends TestCase
{
    /**
     * @var Criteria
     */
    protected $criteria;

    public function setup()
    {
        $this->criteria = new Criteria();
    }

    public function testValueForLike()
    {
        $this->criteria->setOperator('like');
        $this->criteria->setValue('test');
        $this->assertEquals('%test%', $this->criteria->getValue());

        $this->criteria->setOperator('notLike');
        $this->assertEquals('%test%', $this->criteria->getValue());
    }

    public function testValueNormal()
    {
        $this->criteria->setOperator('eq');
        $this->criteria->setValue('test');
        $this->assertEquals('test', $this->criteria->getValue());
    }

    public function testOperatorUnknown()
    {
        $this->expectException(\Dfinfo\MultiFilter\Exception\InvalidArgumentException::class);
        $this->criteria->setOperator('unknow');
    }

    public function testOperatorValid()
    {
        $this->criteria->setOperator('eq');
        $this->assertEquals('eq', $this->criteria->getOperator());
    }

    public function testCantSetValueWhenOperatorIsNull()
    {
        $this->criteria->setOperator('isNull');
        $this->expectException(\Dfinfo\MultiFilter\Exception\ConstraintViolationException::class);
        $this->expectExceptionMessage("Il n'est pas cohérent d'affecter une valeur lorsque l'opérateur est isNull ou isNotNull");
        $this->criteria->setValue('test');
    }
}
