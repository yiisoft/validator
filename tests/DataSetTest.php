<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Validator\Tests\Stub\DataSet;
use Yiisoft\Validator\Validator;

final class DataSetTest extends AbstractDataSetTest
{
    protected function validate(array $dataSet, array $rules): array
    {
        $dataObject = new DataSet($dataSet);
        $validator = new Validator();

        return $validator->validate($dataObject, $rules);
    }
}
