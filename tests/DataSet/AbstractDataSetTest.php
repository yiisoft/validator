<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean\Boolean;
use Yiisoft\Validator\Rule\Number\Number;

abstract class AbstractDataSetTest extends TestCase
{
    abstract protected function validate(array $dataSet, array $rules): Result;

    public function validationCasesDataProvider(): array
    {
        return [
            [
                ['bool' => true, 'int' => 41],
                ['bool' => [new Boolean()], 'int' => [new Number()]],
            ],
        ];
    }

    /**
     * @dataProvider validationCasesDataProvider
     */
    final public function test(array $dataSet, array $rules): void
    {
        $result = $this->validate($dataSet, $rules);
        $this->assertTrue($result->isValid());
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
                    'bool' => [new Boolean()],
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

    /**
     * @dataProvider resultDataProvider
     */
    public function testResult(array $dataSet, array $rules): void
    {
        $result = $this->validate($dataSet, $rules);

        $this->assertTrue($result->isAttributeValid('bool'));
        $this->assertFalse($result->isAttributeValid('int'));
    }
}
