<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Stub\RulesProvidedDataSet;

final class RulesProvidedDataSetTest extends AbstractDataSetTest
{
    protected function validate(array $dataSet, array $rules): Result
    {
        $dataObject = new RulesProvidedDataSet($dataSet, $rules);
        $validator = FakeValidatorFactory::make();

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
        $validator = FakeValidatorFactory::make();
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
}
