<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * RequiredValidator validates that the specified attribute does not have null or empty value.
 */
final class Required extends Rule
{
    use HasValidationErrorMessage;

    private string $message = 'Value cannot be blank.';

    public static function rule(): self
    {
        return new self();
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $result = new Result();

        if ($this->isEmpty(is_string($value) ? trim($value) : $value)) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->formatMessage($this->message),
            ],
        );
    }
}
