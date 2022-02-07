<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\CompareTo;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Stub\DataSet;
use Yiisoft\Validator\Validator;

class ValidatorTest extends TestCase
{
    public function testAddingRulesViaConstructor(): void
    {
        $dataObject = new DataSet(['bool' => true, 'int' => 41]);
        $validator = new Validator();
        $results = $validator->validate($dataObject, [
            'bool' => [Boolean::rule()],
            'int' => [
                Number::rule()->integer(),
                Number::rule()->integer()->min(44),
                static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!');
                    }

                    return $result;
                },
            ],
        ]);

        $this->assertTrue($results['bool']->isValid());

        $intResult = $results['int'];
        $this->assertFalse($intResult->isValid());
        $this->assertCount(1, $intResult->getErrors());
    }

    /**
     * @dataProvider diverseTypesDataProvider
     */
    public function testDiverseTypes($dataSet): void
    {
        $validator = new Validator();
        $results = $validator->validate($dataSet, ['property' => [Required::rule()]]);

        $this->assertCount(1, $results);
        $this->assertTrue($results['property']->isValid());
    }

    public function diverseTypesDataProvider(): array
    {
        $class = new stdClass();
        $class->property = true;

        return [
            [$class],
            [true],
            ['true'],
            [12345],
            [12.345],
            [false],
        ];
    }

    public function testNullAsDataSet(): void
    {
        $validator = new Validator();
        $results = $validator->validate(null, ['property' => [CompareTo::rule(null)]]);

        $this->assertCount(1, $results);
        $this->assertTrue($results['property']->isValid());
    }
}
