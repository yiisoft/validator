<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface ValidatorInterface
{
    public function validate(DataSetInterface $dataSet): Errors;
}
