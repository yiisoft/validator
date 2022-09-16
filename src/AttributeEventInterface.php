<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface AttributeEventInterface
{
    public function afterInitAttribute(DataSetInterface $dataSet): void;
}
