<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Tests\Stub\DataSet;
use Yiisoft\Validator\Validator;

final class DataSetTest extends AbstractDataSetTest
{
    protected function validate(array $dataSet, array $rules): Result
    {
        $dataObject = new DataSet($dataSet);
        $validator = new Validator();

        return $validator->validate($dataObject, $rules);
    }
}
