<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Stub\DataSet;
use Yiisoft\Validator\Tests\Stub\RulesProvidedDataSet;
use Yiisoft\Validator\Validator;

class ValidatorTest extends TestCase
{
    /**
     * @dataProvider validationCasesDataProvider()
     * @param array $dataSet
     */
    public function testAddingRulesViaConstructor(array $dataSet, array $rules): void
    {
        $dataObject = new DataSet($dataSet);

        $validator = new Validator();

        $results = $validator->validate($dataObject, $rules);

        $this->assertTrue($results->getResult('bool')->isValid());

        $intResult = $results->getResult('int');
        $this->assertFalse($intResult->isValid());
        $this->assertCount(1, $intResult->getErrors());
    }

    /**
     * @dataProvider validationCasesDataProvider()
     * @param array $dataSet
     */
    public function testRulesProvidedObject(array $dataSet, array $rules): void
    {
        $dataObject = new RulesProvidedDataSet($dataSet, $rules);

        $validator = new Validator();

        $results = $validator->validate($dataObject);

        $this->assertTrue($results->getResult('bool')->isValid());

        $intResult = $results->getResult('int');
        $this->assertFalse($intResult->isValid());
        $this->assertCount(1, $intResult->getErrors());
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
                    'bool' => [new Boolean()],
                    'int' => [
                        (new Number())->integer(),
                        (new Number())->integer()->min(44),
                        static function ($value): Result {
                            $result = new Result();
                            if ($value !== 42) {
                                $result->addError('Value should be 42!');
                            }
                            return $result;
                        },
                    ],
                ]
            ]
        ];
    }
}
