<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Stub\RulesProvidedDataSet;

final class RulesProvidedDataSetTest extends AbstractDataSetTest
{
    protected function validate(array $dataSet, array $rules): Result
    {
        $dataObject = new RulesProvidedDataSet($dataSet, $rules);
        $validator = ValidatorFactory::make();

        return $validator->validate($dataObject);
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
