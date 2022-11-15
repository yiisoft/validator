<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\DataSet\SingleValueDataSet;

use function is_array;
use function is_object;

final class DataSetHelper
{
    public static function normalize(mixed $data): DataSetInterface
    {
        if ($data instanceof DataSetInterface) {
            return $data;
        }

        if (is_object($data)) {
            return new ObjectDataSet($data);
        }

        if (is_array($data)) {
            return new ArrayDataSet($data);
        }

        return new SingleValueDataSet($data);
    }
}
