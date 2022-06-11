<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use Yiisoft\Validator\Result;
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
}
