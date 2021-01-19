<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\ErrorMessage;
use Yiisoft\Validator\ErrorMessageFormatterInterface;
use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * RequiredValidator validates that the specified attribute does not have null or empty value.
 */
class Required extends Rule
{
    use HasValidationErrorMessage;

    private string $message = 'Value cannot be blank.';

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();

        if ($this->isEmpty(is_string($value) ? trim($value) : $value)) {
            $result->addError(new ErrorMessage($this->message));
        }

        return $result;
    }

    public function getOptions(?ErrorMessageFormatterInterface $formatter = null): array
    {
        return array_merge(
            parent::getOptions($formatter),
            [
                'message' => new ErrorMessage($this->message, [], $formatter),
            ],
        );
    }
}
