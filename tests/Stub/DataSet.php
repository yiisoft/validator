<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\MissingAttributeException;

final class DataSet implements DataSetInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getAttributeValue(string $attribute)
    {
        if (!isset($this->data[$attribute])) {
            throw new MissingAttributeException("There is no \"$attribute\" attribute in the class.");
        }

        return $this->data[$attribute];
    }
}
