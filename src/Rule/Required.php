<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the specified value is neither null nor empty.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Required extends Rule
{
    public function __construct(
        private string $message = 'Value cannot be blank.',
        private ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }

    protected function validateValue($value, ?ValidationContext $context = null): Result
    {
        $result = new Result($this->formatter);

        if ($this->isEmpty(is_string($value) ? trim($value) : $value)) {
            $result->addError($this->message);
        }

        return $result;
    }

    public function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            'message' => $this->message,
        ]);
    }
}
