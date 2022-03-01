<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\Exception\MissingAttributeException;

trait ArrayDataTrait
{
    private array $data;

    public function getAttributeValue(string $attribute)
    {
        if (!isset($this->data[$attribute])) {
            throw new MissingAttributeException("There is no \"$attribute\" key in the array.");
        }

        return $this->data[$attribute];
    }
}
