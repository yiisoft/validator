<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ResultSet;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Number;

abstract class AbstractDataSetTest extends TestCase
{
    /**
     * @dataProvider validationCasesDataProvider()
     *
     * @param array $dataSet
     */
    final public function test(array $dataSet, array $rules): void
    {
        $resultSet = $this->validate($dataSet, $rules);

        $this->assertTrue($resultSet->isValid());
    }

    public function validationCasesDataProvider(): array
    {
        return [
            [
                [
                    'bool' => true,
                    'int' => 41,
                ],
                [
                    'bool' => [Boolean::rule()],
                    'int' => [Number::rule()],
                ],
            ],
        ];
    }

    /**
     * @dataProvider resultDataProvider()
     *
     * @param array $dataSet
     */
    public function testResult(array $dataSet, array $rules): void
    {
        $resultSet = $this->validate($dataSet, $rules);

        $this->assertTrue($resultSet->getResult('bool')->isValid());

        $intResult = $resultSet->getResult('int');
        $this->assertFalse($intResult->isValid());
        $this->assertCount(1, $intResult->getErrors());
    }

    public function resultDataProvider(): array
    {
        return [
            [
                [
                    'bool' => true,
                    'int' => 41,
                ],
                [
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
                ],
            ],
        ];
    }

    abstract protected function validate(array $dataSet, array $rules): ResultSet;
}
