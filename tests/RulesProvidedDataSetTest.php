<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Validator\Tests\Stub\RulesProvidedDataSet;
use Yiisoft\Validator\Validator;

final class RulesProvidedDataSetTest extends AbstractDataSetTest
{
    protected function validate(array $dataSet, array $rules): array
    {
        $dataObject = new RulesProvidedDataSet($dataSet, $rules);
        $validator = new Validator();

        return $validator->validate($dataObject);
    }
}
