<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\DataSet\ObjectDataSet;

interface AttributeEventInterface
{
    public function afterInitAttribute(ObjectDataSet $dataSet): void;
}
