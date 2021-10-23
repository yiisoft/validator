<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataSetInterface;

final class ScalarDataSet implements DataSetInterface
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getRawAttributeValue(string $attribute)
    {
        return $this->value;
    }

    public function hasAttribute(string $attribute): bool
    {
        return true;
    }
}
