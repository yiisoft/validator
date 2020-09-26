<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rules;

/**
 * GroupRule validates a single value for a set of custom rules
 */
abstract class GroupRule extends Rule
{
    use HasValidationErrorMessage;

    protected string $message = 'This value is not a valid.';

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        if (!$this->getRules()->validate($value, $dataSet)->isValid()) {
            $result->addError($this->message);
        }

        return $result;
    }

    /**
     * Return custom rules set
     * @return Rules
     */
    abstract protected function getRules(): Rules;

    public function getOptions(): array
    {
        return $this->getRules()->asArray();
    }
}
