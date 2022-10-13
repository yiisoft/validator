<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Tests\Support\DataSet\RulesProvidedDataSet;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;

final class DataSetUsageTest extends TestCase
{
    public function dataArrayDataSetUsage(): array
    {
        return [
            [
                ['bool' => true, 'int' => 41],
                ['bool' => [new Boolean()], 'int' => [new Number()]],
            ],
        ];
    }

    /**
     * @dataProvider dataArrayDataSetUsage
     */
    public function testArrayDataSetUsage(array $dataSet, array $rules): void
    {
        $dataObject = new ArrayDataSet($dataSet);
        $result = ValidatorFactory::make()->validate($dataObject, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataRulesProvidedDataSetUsage(): array
    {
        return [
            [
                ['bool' => true, 'int' => 41],
                ['bool' => [new Boolean()], 'int' => [new Number()]],
            ],
        ];
    }

    /**
     * @dataProvider dataRulesProvidedDataSetUsage
     */
    public function testRulesProvidedDataSetUsage(array $dataSet, array $rules): void
    {
        $dataObject = new RulesProvidedDataSet($dataSet, $rules);
        $result = ValidatorFactory::make()->validate($dataObject);

        $this->assertTrue($result->isValid());
    }

    public function testArrayDataSetResult(): void
    {
        $dataObject = new ArrayDataSet([
            'bool' => true,
            'int' => 41,
        ]);
        $rules = [
            'bool' => [new Boolean()],
            'int' => [
                new Number(asInteger: true),
                new Number(asInteger: true, min: 44),
                static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!', ['int']);
                    }

                    return $result;
                },
            ],
        ];
        $result = ValidatorFactory::make()->validate($dataObject, $rules);

        $this->assertFalse($result->isValid());
        $this->assertTrue($result->isAttributeValid('bool'));
        $this->assertFalse($result->isAttributeValid('int'));
    }

    public function testRulesProvidedDataSetResult(): void
    {
        $dataObject = new RulesProvidedDataSet(
            [
                'bool' => true,
                'int' => 41,
            ],
            [
                'bool' => [new Boolean()],
                'int' => [
                    new Number(asInteger: true),
                    new Number(asInteger: true, min: 44),
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
        $result = ValidatorFactory::make()->validate($dataObject);

        $this->assertFalse($result->isValid());
        $this->assertTrue($result->isAttributeValid('bool'));
        $this->assertFalse($result->isAttributeValid('int'));
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
                    new HasLength(max: 3),
                ],
                'age' => [
                    new Number(max: 25),
                ],
            ]
        );
        $validator = ValidatorFactory::make();
        $result = $validator->validate(
            $dataSet,
            [
                'username' => [
                    new HasLength(max: 10),
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
                    new HasLength(max: 3),
                ],
                'age' => [
                    new Number(max: 25),
                ],
            ]
        );
        $validator = ValidatorFactory::make();
        $result = $validator->validate(
            $dataSet,
            []
        );

        $this->assertTrue($result->isValid());
    }
}
