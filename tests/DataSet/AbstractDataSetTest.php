<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Number;

abstract class AbstractDataSetTest extends TestCase
{
    /**
     * @dataProvider validationCasesDataProvider
     *
     * @param array $dataSet
     */
    final public function test(array $dataSet, array $rules): void
    {
        $result = $this->validate($dataSet, $rules);
        $this->assertTrue($result->isValid());
    }

    public function validationCasesDataProvider(): array
    {
        return [
            [
                ['bool' => true, 'int' => 41],
                ['bool' => [Boolean::rule()], 'int' => [new Number()]],
            ],
        ];
    }

    /**
     * @dataProvider resultDataProvider
     *
     * @param array $dataSet
     */
    public function testResult(array $dataSet, array $rules): void
    {
        $result = $this->validate($dataSet, $rules);

        $this->assertTrue($result->isAttributeValid('bool'));
        $this->assertFalse($result->isAttributeValid('int'));
        $this->assertCount(2, $result->getAttributeErrors('int'));
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
                        new Number(asInteger: true),
                        new Number(asInteger: true, min: 44),
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

    abstract protected function validate(array $dataSet, array $rules): Result;
}
