<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;

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
            $result->addError($this->translateMessage($this->message));
        }

        return $result;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getName(): string
    {
        return 'required';
    }

    /**
     * @inheritDoc
     * @return array
     */
    public function getOptions(): array
    {
        return parent::getOptions();
    }
}
