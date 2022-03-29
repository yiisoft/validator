<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataSetInterface;

final class ArrayDataSet implements DataSetInterface
{
    use ArrayDataTrait;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
}
