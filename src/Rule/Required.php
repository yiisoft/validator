<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\DataSetInterface;

/**
 * RequiredValidator validates that the specified attribute does not have null or empty value.
 */
class Required extends Rule
{
    use HasValidationErrorMessage;

    private string $message = 'Value cannot be blank.';

    protected function validateValue($value, DataSetInterface $dataSet = null): Error
    {
        $result = new Error();

        if ($this->isEmpty(is_string($value) ? trim($value) : $value)) {
            $result->addError($this->message);
        }

        return $result;
    }

    public function getName(): string
    {
        return 'required';
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->message
            ],
        );
    }
}
