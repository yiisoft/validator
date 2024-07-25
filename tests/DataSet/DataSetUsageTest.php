<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Tests\Support\DataSet\RulesProvidedDataSet;
use Yiisoft\Validator\Validator;

final class DataSetUsageTest extends TestCase
{
    public function dataArrayDataSetUsage(): array
    {
        return [
            [
                ['bool' => true, 'int' => 41],
                ['bool' => [new BooleanValue()], 'int' => [new Number()]],
            ],
        ];
    }

    /**
     * @dataProvider dataArrayDataSetUsage
     */
    public function testArrayDataSetUsage(array $dataSet, array $rules): void
    {
        $dataObject = new ArrayDataSet($dataSet);
        $result = (new Validator())->validate($dataObject, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataRulesProvidedDataSetUsage(): array
    {
        return [
            [
                ['bool' => true, 'int' => 41],
                ['bool' => [new BooleanValue()], 'int' => [new Number()]],
            ],
        ];
    }

    /**
     * @dataProvider dataRulesProvidedDataSetUsage
     */
    public function testRulesProvidedDataSetUsage(array $dataSet, array $rules): void
    {
        $dataObject = new RulesProvidedDataSet($dataSet, $rules);
        $result = (new Validator())->validate($dataObject);

        $this->assertTrue($result->isValid());
    }

    public function testArrayDataSetResult(): void
    {
        $dataObject = new ArrayDataSet([
            'bool' => true,
            'int' => 41,
        ]);
        $rules = [
            'bool' => [new BooleanValue()],
            'int' => [
                new Integer(),
                new Integer(min: 44),
                static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!', ['int']);
                    }

                    return $result;
                },
            ],
        ];
        $result = (new Validator())->validate($dataObject, $rules);

        $this->assertFalse($result->isValid());
        $this->assertTrue($result->isPropertyValid('bool'));
        $this->assertFalse($result->isPropertyValid('int'));
    }

    public function testRulesProvidedDataSetResult(): void
    {
        $dataObject = new RulesProvidedDataSet(
            [
                'bool' => true,
                'int' => 41,
            ],
            [
                'bool' => [new BooleanValue()],
                'int' => [
                    new Integer(),
                    new Integer(min: 44),
                    static function ($value): Result {
                        $result = new Result();
                        if ($value !== 42) {
                            $result->addError('Value should be 42!', ['int']);
                        }

                        return $result;
                    },
                ],
            ]
        );
        $result = (new Validator())->validate($dataObject);

        $this->assertFalse($result->isValid());
        $this->assertTrue($result->isPropertyValid('bool'));
        $this->assertFalse($result->isPropertyValid('int'));
    }

    public function testExplicitRulesHavePriority(): void
    {
        $dataSet = new RulesProvidedDataSet(
            [
                'username' => 'test123',
                'age' => 42,
            ],
            [
                'username' => [
                    new Regex('^[a-z]+$'),
                    new Length(max: 3),
                ],
                'age' => [
                    new Number(max: 25),
                ],
            ]
        );
        $validator = new Validator();
        $result = $validator->validate(
            $dataSet,
            [
                'username' => [
                    new Length(max: 10),
                ],
            ]
        );

        $this->assertTrue($result->isValid());
    }

    public function testEmptyExplicitRulesHavePriority(): void
    {
        $dataSet = new RulesProvidedDataSet(
            [
                'username' => 'test123',
                'age' => 42,
            ],
            [
                'username' => [
                    new Regex('^[a-z]+$'),
                    new Length(max: 3),
                ],
                'age' => [
                    new Number(max: 25),
                ],
            ]
        );
        $validator = new Validator();
        $result = $validator->validate(
            $dataSet,
            []
        );

        $this->assertTrue($result->isValid());
    }
}
