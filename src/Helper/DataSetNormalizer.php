<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\DataSet\SingleValueDataSet;
use Yiisoft\Validator\DataSetInterface;

use function is_array;
use function is_object;

/**
 * A helper class used to normalize different types of validated data to a data set ({@see DataSetInterface}).
 */
final class DataSetNormalizer
{
    /**
     * Normalizes different types of validated data to a data set:
     *
     * - If {@see $data} is already a data set, it will be left as is.
     * - An object is normalized to {@see ObjectDataSet}.
     * - An array is normalized to {@see ArrayDataSet}.
     * - Everything else is normalized to {@see SingleValueDataSet}.
     *
     * In order to prevent mapping objects and arrays to corresponding data sets, wrap them with
     * {@see SingleValueDataSet} explicitly or use a custom data set ({@see DataSetInterface}).
     *
     * @param mixed $data Raw validated data of any type.
     * @return DataSetInterface Data set instance.
     */
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
