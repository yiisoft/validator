<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationMessage;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rules;


abstract class GroupRule extends Rule
{
    use HasValidationMessage;

    protected string $message = 'This value is not a valid.';

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        if (!$this->getRules()->validate($value, $dataSet)->isValid()) {
            $result->addError($this->translateMessage($this->message));
        }

        return $result;
    }

    abstract protected function getRules(): Rules;
}
